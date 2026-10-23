<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketsReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tickets;
    private int $rowNumber = 0;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * Mengembalikan koleksi data tiket yang telah difilter.
     */
    public function collection()
    {
        return $this->tickets;
    }

    /**
     * Header kolom pada file Excel.
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Tiket',
            'Tanggal Lapor',
            'Nama Pelapor',
            'Unit RS',
            'Kategori Kendala',
            'Judul Kendala',
            'Prioritas',
            'Status',
            'Teknisi Penanggung Jawab',
            'Waktu Selesai',
            'Durasi Penyelesaian',
        ];
    }

    /**
     * Mapping data setiap baris tiket.
     */
    public function map($ticket): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $ticket->kode,
            $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
            $ticket->user?->name ?? '-',
            $ticket->unit?->nama ?? '-',
            $ticket->category?->nama ?? '-',
            $ticket->judul,
            ucfirst($ticket->prioritas),
            ucfirst($ticket->status),
            $ticket->teknisi?->name ?? 'Belum Ditugaskan',
            $ticket->waktu_selesai ? $ticket->waktu_selesai->format('d/m/Y H:i') : '-',
            $ticket->durasi_penyelesaian,
        ];
    }

    /**
     * Styling baris judul / header tabel pada Excel.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Warna tema indigo
                ],
            ],
        ];
    }
}

