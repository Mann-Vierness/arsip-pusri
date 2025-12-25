<?php

namespace App\Services;

use App\Models\SuratKeputusan;
use App\Models\SuratPerjanjian;
use App\Models\SuratAddendum;
use Carbon\Carbon;

class DocumentNumberService
{
    /**
     * Increment letter suffix: A, B, C... Z, AA, AB... ZZ
     */
    private function incrementLetter($str = '')
    {
        if ($str === '' || $str === null) {
            return 'A';
        }

        $arr = str_split($str);
        $i = count($arr) - 1;
        $carry = true;

        while ($i >= 0 && $carry) {
            if ($arr[$i] === 'Z') {
                $arr[$i] = 'A';
                $i--;
            } else {
                $arr[$i] = chr(ord($arr[$i]) + 1);
                $carry = false;
            }
        }

        if ($carry) {
            array_unshift($arr, 'A');
        }

        // Maksimal 2 huruf (A-Z, AA-ZZ)
        if (count($arr) > 2) {
            return 'ZZ';
        }

        return implode('', $arr);
    }

    /**
     * Parse nomor format: angka + suffix huruf (e.g., "001", "004A", "005B")
     * Returns array [angka_str, suffix]
     */
    private function parseNomor($nomor)
    {
        preg_match('/^(\d+)([A-Z]*)$/', $nomor, $matches);
        $angka = $matches[1] ?? '001';
        $suffix = $matches[2] ?? '';
        return [$angka, $suffix];
    }

    /**
     * Format nomor: angka (3 digit) + suffix
     */
    private function formatNomor($angka, $suffix = '')
    {
        $angka_str = str_pad($angka, 3, '0', STR_PAD_LEFT);
        return $angka_str . $suffix;
    }

    /**
     * Generate nomor SK (Surat Keputusan)
     * Format: SK/DIR/NOMOR/TAHUN
     * Hanya bisa input hari ini, tanpa backdate
     */
    public function generateSKNumber($tanggal = null)
    {
        $tanggalObj = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $tahun = $tanggalObj->format('Y');
        
        // SK hanya bisa input hari ini
        if (!$tanggalObj->isToday()) {
            throw new \Exception('SK hanya bisa diinput untuk tanggal hari ini');
        }

        // 1. Reuse nomor dari soft delete hari ini
        $deletedSK = SuratKeputusan::onlyTrashed()
            ->whereDate('TANGGAL', $tanggalObj->toDateString())
            ->orderBy('NOMOR_SK', 'asc')
            ->first();

        if ($deletedSK) {
            $num = $deletedSK->NOMOR_SK;
            $deletedSK->forceDelete();
            return $num;
        }

        // 2. Cari nomor terakhir hari ini
        $lastSK = SuratKeputusan::withTrashed()
            ->whereDate('TANGGAL', $tanggalObj->toDateString())
            ->orderBy('NOMOR_SK', 'desc')
            ->first();

        if (!$lastSK) {
            $nomor = 1;
        } else {
            preg_match('/SK\/DIR\/(\d+)([A-Z]*)\/\d{4}/', $lastSK->NOMOR_SK, $matches);
            $nomor = (int)$matches[1] + 1;
        }

        $nomor_str = str_pad($nomor, 3, '0', STR_PAD_LEFT);
        return "SK/DIR/{$nomor_str}/{$tahun}";
    }

    /**
     * Generate nomor SP (Surat Perjanjian)
     * Format: -NOMOR/SP/DIR/TAHUN atau -NOMOR/SP/TAHUN
     * Dengan backdate, suffix huruf, soft delete reuse, reset per tahun
     */
    public function generateSPNumber($tanggal, $dir = null)
    {
        return $this->generateBackdatableNumber($tanggal, 'sp', $dir);
    }

    /**
     * Generate nomor Addendum Perjanjian
     * Format: -NOMOR/ADD-DIR/TAHUN atau -NOMOR/ADD/TAHUN
     * Dengan backdate, suffix huruf, soft delete reuse, reset per tahun
     */
    public function generateAddendumNumber($tanggal, $dir = null)
    {
        return $this->generateBackdatableNumber($tanggal, 'addendum', $dir);
    }

    /**
     * Generate nomor untuk dokumen yang bisa backdate (SP, Addendum)
     */
    private function generateBackdatableNumber($tanggal, $jenis = 'sp', $dir = null)
    {
        $tanggalObj = Carbon::parse($tanggal);
        $tahun = $tanggalObj->format('Y');
        $isToday = $tanggalObj->isToday();

        // Tentukan model dan format nomor sesuai permintaan
        if ($jenis === 'sp') {
            $model = SuratPerjanjian::class;
            $format = ($dir === 'DIR') ? '{NOMOR}/SP/DIR/{TAHUN}' : '{NOMOR}/SP/{TAHUN}';
        } else {
            $model = SuratAddendum::class;
            $format = ($dir === 'DIR') ? '{NOMOR}/ADD-SP/DIR/{TAHUN}' : '{NOMOR}/ADD-SP/{TAHUN}';
        }

        $field = 'NO';

        // Ambil semua data tahun ini (SP & Addendum) untuk urutan global
        if ($jenis === 'sp') {
            $allGlobal = SuratPerjanjian::withTrashed()->whereYear('TANGGAL', $tahun)->get();
        } else {
            $allGlobal = SuratAddendum::withTrashed()->whereYear('TANGGAL', $tahun)->get();
        }
        $allGlobal = $allGlobal->sortBy(function($item) {
            return [$item->TANGGAL, $item->NO];
        });

        $field = 'NO';

        // 1. Reuse soft delete dari tanggal yang sama
        $deleted = $model::onlyTrashed()
            ->whereDate('TANGGAL', $tanggalObj->toDateString())
            ->whereYear('TANGGAL', $tahun)
            ->orderBy($field, 'asc')
            ->first();

        if ($deleted) {
            $num = $deleted->$field;
            $deleted->forceDelete();
            return $num;
        }

        // 2. Penomoran global: cari nomor terakhir di tahun ini
        if ($allGlobal->isEmpty()) {
            $nomor = $this->formatNomor(1, '');
            return str_replace(['{NOMOR}', '{TAHUN}'], [$nomor, $tahun], $format);
        }

        // Cari anchor: nomor terakhir yang tanggalnya <= tanggal input
        $anchor = null;
        foreach ($allGlobal as $record) {
            if ($record->TANGGAL <= $tanggalObj) {
                $anchor = $record;
            }
        }

        if (!$anchor) {
            $angka = 1;
            $suffix = 'A';
        } else {
            [$angka, $suffix] = $this->parseNomor($anchor->$field);
            $angka = (int)$angka + 1;
        }

        $nomor = $this->formatNomor($angka, '');
        return str_replace(['{NOMOR}', '{TAHUN}'], [$nomor, $tahun], $format);
    }

    /**
     * Get last document number for a specific date (untuk reference)
     */
    public function getLastNumberForDate($tanggal, $jenis = 'sp')
    {
        $tanggalObj = Carbon::parse($tanggal);
        $tahun = $tanggalObj->format('Y');

        $model = $jenis === 'sp' ? SuratPerjanjian::class : SuratAddendum::class;
        
        $record = $model::withTrashed()
            ->whereDate('TANGGAL', $tanggalObj->toDateString())
            ->whereYear('TANGGAL', $tahun)
            ->orderBy('NO', 'desc')
            ->first();

        return $record ? $record->NO : null;
    }
}
