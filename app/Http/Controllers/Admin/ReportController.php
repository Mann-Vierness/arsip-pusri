<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratKeputusan;
use App\Models\SuratPerjanjian;
use App\Models\SuratAddendum;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        // Validasi input
        $request->validate([
            'start_month' => 'nullable|integer|min:1|max:12',
            'end_month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2100',
        ]);

        // Default: bulan ini saja
        $startMonth = $request->input('start_month', now()->month);
        $endMonth = $request->input('end_month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        // Pastikan end_month >= start_month
        if ($endMonth < $startMonth) {
            $temp = $startMonth;
            $startMonth = $endMonth;
            $endMonth = $temp;
        }

        // Buat tanggal awal dan akhir
        $startDate = Carbon::create($selectedYear, $startMonth, 1)->startOfMonth();
        $endDate = Carbon::create($selectedYear, $endMonth, 1)->endOfMonth();

        // Hitung total dokumen per kategori untuk rentang yang dipilih
        $totalSK = SuratKeputusan::whereBetween('TANGGAL', [$startDate, $endDate])->count();
        $totalSP = SuratPerjanjian::whereBetween('TANGGAL', [$startDate, $endDate])->count();
        $totalAddendum = SuratAddendum::whereBetween('TANGGAL', [$startDate, $endDate])->count();
        $grandTotal = $totalSK + $totalSP + $totalAddendum;

        // Data untuk tabel rekap (hanya bulan dalam rentang yang dipilih)
        $monthlyData = [];
        for ($month = $startMonth; $month <= $endMonth; $month++) {
            $skCount = SuratKeputusan::whereYear('TANGGAL', $selectedYear)
                ->whereMonth('TANGGAL', $month)
                ->count();

            $spCount = SuratPerjanjian::whereYear('TANGGAL', $selectedYear)
                ->whereMonth('TANGGAL', $month)
                ->count();

            $addendumCount = SuratAddendum::whereYear('TANGGAL', $selectedYear)
                ->whereMonth('TANGGAL', $month)
                ->count();

            $monthlyData[] = [
                'month' => $month,
                'month_name' => Carbon::createFromDate($selectedYear, $month, 1)->locale('id')->translatedFormat('F'),
                'sk' => $skCount,
                'sp' => $spCount,
                'addendum' => $addendumCount,
                'total' => $skCount + $spCount + $addendumCount,
            ];
        }

        // Data untuk grafik
        $chartLabels = array_column($monthlyData, 'month_name');
        $chartDataSK = array_column($monthlyData, 'sk');
        $chartDataSP = array_column($monthlyData, 'sp');
        $chartDataAddendum = array_column($monthlyData, 'addendum');

        // Daftar bulan untuk dropdown
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Daftar tahun untuk dropdown (5 tahun ke belakang sampai 1 tahun ke depan)
        $currentYear = now()->year;
        $years = range($currentYear - 5, $currentYear + 1);

        return view('admin.reports.documents', compact(
            'startMonth',
            'endMonth',
            'selectedYear',
            'totalSK',
            'totalSP',
            'totalAddendum',
            'grandTotal',
            'monthlyData',
            'chartLabels',
            'chartDataSK',
            'chartDataSP',
            'chartDataAddendum',
            'months',
            'years',
            'startDate',
            'endDate'
        ));
    }
}