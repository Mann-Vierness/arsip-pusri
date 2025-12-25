<?php

namespace App\Services;

use Carbon\Carbon;

class CsvExportService
{
    /**
     * Export documents to CSV format
     * 
     * @param \Illuminate\Database\Eloquent\Collection $documents
     * @param string $type (sk|sp|addendum|approval)
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportDocuments($documents, $type = 'sk')
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $this->getFilename($type) . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($documents, $type) {
            $output = fopen('php://output', 'w');
            
            // Add UTF-8 BOM untuk Excel
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers
            fputcsv($output, $this->getHeaders($type));

            // Write data rows
            foreach ($documents as $document) {
                fputcsv($output, $this->formatRow($document, $type));
            }

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Get CSV headers berdasarkan tipe dokumen
     */
    private function getHeaders($type)
    {
        switch ($type) {
            case 'sk':
                return [
                    'No.',
                    'Nomor SK',
                    'Tanggal',
                    'Perihal',
                    'Penandatangan',
                    'Unit Kerja',
                    'Status',
                    'User',
                    'Dibuat Pada',
                ];
            case 'sp':
                return [
                    'No.',
                    'Nomor SP',
                    'Tanggal',
                    'Perihal',
                    'Pihak Pertama',
                    'Pihak Lain',
                    'Status',
                    'User',
                    'Dibuat Pada',
                ];
            case 'addendum':
                return [
                    'No.',
                    'Nomor Addendum',
                    'Tanggal',
                    'Pihak Pertama',
                    'Pihak Lain',
                    'Perihal',
                    'Perubahan',
                    'Status',
                    'User',
                    'Dibuat Pada',
                ];
            case 'approval':
                return [
                    'No.',
                    'Tipe Dokumen',
                    'Nomor Dokumen',
                    'Tanggal',
                    'Perihal',
                    'User Pembuat',
                    'Status',
                    'Disetujui Oleh',
                    'Tanggal Approval',
                    'Alasan Penolakan',
                ];
            default:
                return ['Data'];
        }
    }

    /**
     * Format satu row dokumen ke format CSV
     */
    private function formatRow($document, $type)
    {
        $index = isset($document->row_index) ? $document->row_index : 1;

        switch ($type) {
            case 'sk':
                return [
                    $index,
                    $document->NOMOR_SK ?? '',
                    optional($document->TANGGAL)->format('d/m/Y') ?? '',
                    $document->PERIHAL ?? '',
                    $document->PENANDATANGAN ?? '',
                    $document->UNIT_KERJA ?? '',
                    $this->getStatusLabel($document->approval_status),
                    $document->USER ?? '',
                    optional($document->created_at)->format('d/m/Y H:i') ?? '',
                ];
            case 'sp':
                return [
                    $index,
                    $document->NO ?? '',
                    optional($document->TANGGAL)->format('d/m/Y') ?? '',
                    $document->PERIHAL ?? '',
                    $document->PIHAK_PERTAMA ?? '',
                    $document->PIHAK_LAIN ?? '',
                    $this->getStatusLabel($document->approval_status),
                    $document->USER ?? '',
                    optional($document->created_at)->format('d/m/Y H:i') ?? '',
                ];
            case 'addendum':
                return [
                    $index,
                    $document->NO ?? '',
                    optional($document->TANGGAL)->format('d/m/Y') ?? '',
                    $document->PIHAK_PERTAMA ?? '',
                    $document->PIHAK_LAIN ?? '',
                    $document->PERIHAL ?? '',
                    $document->PERUBAHAN ?? '',
                    $this->getStatusLabel($document->approval_status),
                    $document->USER ?? '',
                    optional($document->created_at)->format('d/m/Y H:i') ?? '',
                ];
            case 'approval':
                return [
                    $index,
                    $document['type_text'] ?? '',
                    $document['nomor'] ?? '',
                    optional($document['tanggal'])->format('d/m/Y') ?? $document['tanggal'] ?? '',
                    $document['perihal'] ?? '',
                    $document['user'] ?? '',
                    $this->getStatusLabel($document['status']),
                    $document['approved_by'] ?? '',
                    optional($document['approved_at'])->format('d/m/Y H:i') ?? $document['approved_at'] ?? '',
                    $document['rejection_reason'] ?? '',
                ];
            default:
                return [$index, 'N/A'];
        }
    }

    /**
     * Get label untuk approval status
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Get filename untuk export
     */
    private function getFilename($type)
    {
        $date = Carbon::now()->format('d_m_Y_His');
        $types = [
            'sk' => 'Surat_Keputusan',
            'sp' => 'Surat_Perjanjian',
            'addendum' => 'Surat_Addendum',
            'approval' => 'Approval_Dokumen',
        ];

        $typeName = $types[$type] ?? 'Export';
        return "{$typeName}_{$date}.csv";
    }
}
