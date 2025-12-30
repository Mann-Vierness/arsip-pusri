<?php

namespace App\Services;

use App\Models\SuratKeputusan;
use App\Models\SuratPerjanjian;
use App\Models\SuratAddendum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /* =====================================================
     * =============== SURAT KEPUTUSAN =====================
     * ============ TIDAK DIUBAH (AS IS) ==================
     * ===================================================== */

    public function generateSKNumber($tanggal = null)
    {
        $tanggalObj = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $tahun = $tanggalObj->format('Y');

        if (!$tanggalObj->isToday()) {
            throw new \Exception('SK hanya bisa diinput untuk tanggal hari ini');
        }

        $deletedSK = SuratKeputusan::onlyTrashed()
            ->whereYear('TANGGAL', $tahun)
            ->orderBy('NOMOR_SK', 'asc')
            ->first();

        if ($deletedSK) {
            $num = $deletedSK->NOMOR_SK;
            $deletedSK->forceDelete();
            return $num;
        }

        $lastSK = SuratKeputusan::withTrashed()
            ->whereYear('TANGGAL', $tahun)
            ->orderByRaw(
                "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(NOMOR_SK, '/', 3), '/', -1) AS UNSIGNED) DESC"
            )
            ->first();

        if (!$lastSK) {
            $nomor = 1;
        } else {
            preg_match('/SK\/DIR\/(\d+)\/\d{4}/', $lastSK->NOMOR_SK, $m);
            $nomor = (int)($m[1] ?? 0) + 1;
        }

        return "SK/DIR/" . str_pad($nomor, 3, '0', STR_PAD_LEFT) . "/{$tahun}";
    }

    /* =====================================================
     * ============ SURAT PERJANJIAN (SP) ==================
     * ============ BACKDATE CHRONOLOGICAL ================
     * ===================================================== */

    public function generateSPNumber($tanggal, $dir = null)
    {
        return DB::transaction(function () use ($tanggal, $dir) {
            return $this->generateChronologicalNumber(
                SuratPerjanjian::class,
                'SP',
                $tanggal,
                $dir
            );
        });
    }

    /* =====================================================
     * ================= ADDENDUM SP ======================
     * ============ BACKDATE CHRONOLOGICAL ================
     * ===================================================== */

    public function generateAddendumNumber($tanggal, $dir = null)
    {
        return DB::transaction(function () use ($tanggal, $dir) {
            return $this->generateChronologicalNumber(
                SuratAddendum::class,
                'ADD-SP',
                $tanggal,
                $dir
            );
        });
    }

    /* =====================================================
     * ============ CORE ENGINE (SP & ADDENDUM) ============
     * ===================================================== */

    private function generateChronologicalNumber(
    string $modelClass,
    string $type,
    $tanggal,
    $dir
) {
    $tanggalObj = Carbon::parse($tanggal);
    $tahun = $tanggalObj->format('Y');
    $isDIR = ($dir === 'DIR');
    $isToday = $tanggalObj->isToday();

    // Ambil data terakhir <= tanggal input
    $last = $modelClass::withTrashed()
        ->whereYear('TANGGAL', $tahun)
        ->where('TANGGAL', '<=', $tanggalObj->toDateString())
        ->orderBy('TANGGAL', 'desc')
        ->orderByRaw("
            CAST(LEFT(NO, 3) AS UNSIGNED) DESC,
            LENGTH(SUBSTRING_INDEX(NO, '/', 1)) DESC,
            SUBSTRING_INDEX(NO, '/', 1) DESC
        ")
        ->lockForUpdate()
        ->first();

    // =============================
    // JIKA HARI INI → TANPA SUFFIX
    // =============================
    if ($isToday) {
        if (!$last) {
            $number = 1;
        } else {
            preg_match('/(\d{3})/', $last->NO, $m);
            $number = (int)$m[1] + 1;
        }

        $nomorStr = str_pad($number, 3, '0', STR_PAD_LEFT);

        return $isDIR
            ? "{$nomorStr}/{$type}/DIR/{$tahun}"
            : "{$nomorStr}/{$type}/{$tahun}";
    }

    // =============================
    // BACKDATE → PAKAI SUFFIX
    // =============================
    if (!$last) {
        $number = '001';
        $suffix = 'A';
    } else {
        preg_match('/(\d{3})([A-Z]*)/', $last->NO, $m);
        $number = $m[1];
        $suffix = $m[2] ?? '';
        $suffix = $suffix === '' ? 'A' : $this->nextSuffix($suffix);
    }

    $final = $number . $suffix;

    return $isDIR
        ? "{$final}/{$type}/DIR/{$tahun}"
        : "{$final}/{$type}/{$tahun}";
}


    /* =====================================================
     * =============== SUFFIX ENGINE FINAL =================
     * =========== A–Z, AA–AZ, BA–BZ, ... =================
     * ===================================================== */

    private function nextSuffix(string $current): string
    {
        $chars = str_split($current);
        $i = count($chars) - 1;

        // Increment seperti sistem angka (base-26)
        while ($i >= 0) {
            if ($chars[$i] !== 'Z') {
                $chars[$i] = chr(ord($chars[$i]) + 1);
                return implode('', $chars);
            }

            // Jika Z → reset ke A, carry ke depan
            $chars[$i] = 'A';
            $i--;
        }

        // Semua karakter adalah Z (Z, ZZ, ZZZ, dst)
        return 'A' . implode('', $chars);
    }
}
