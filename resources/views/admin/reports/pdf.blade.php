<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Tiket IT</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 2.5px solid #1E293B;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .kop-title {
            font-size: 14px;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-subtitle {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #1E293B;
            text-transform: uppercase;
            text-decoration: underline;
        }

        /* Tabel Metadata Filter */
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 6px 10px;
        }

        .meta-table td {
            font-size: 9.5px;
            padding: 2px 4px;
            vertical-align: top;
        }

        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 120px;
        }

        /* Tabel Ringkasan Metrik */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .summary-table th {
            background-color: #4F46E5;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #4338CA;
        }

        .summary-table td {
            padding: 6px 4px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #CBD5E1;
            background-color: #FFFFFF;
        }

        /* Tabel Data Tiket */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #F1F5F9;
            color: #334155;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #CBD5E1;
            text-align: left;
        }

        .data-table td {
            padding: 5px 5px;
            font-size: 8.5px;
            border: 1px solid #E2E8F0;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier', monospace; font-size: 8.5px; font-weight: bold; }

        /* Badge Status & Prioritas */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-baru { background-color: #DBEAFE; color: #1E40AF; }
        .badge-diproses { background-color: #FEF3C7; color: #92400E; }
        .badge-selesai { background-color: #D1FAE5; color: #065F46; }
        .badge-ditutup { background-color: #F1F5F9; color: #475569; }
        .badge-tinggi { color: #DC2626; font-weight: bold; }
        .badge-sedang { color: #D97706; }
        .badge-rendah { color: #2563EB; }

        /* Lembar Pengesahan / Tanda Tangan */
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-table td {
            vertical-align: top;
            font-size: 9.5px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Instansi -->
    <table class="kop-table">
        <tr>
            <td style="width: 70px; vertical-align: middle;">
                <div style="width: 48px; height: 48px; background-color: #4F46E5; border-radius: 8px; color: #ffffff; text-align: center; line-height: 48px; font-weight: bold; font-size: 20px;">
                    IT
                </div>
            </td>
            <td style="vertical-align: middle;">
                <div class="kop-title">RUMAH SAKIT UMUM DAERAH</div>
                <div class="kop-subtitle">INSTALASI TEKNOLOGI INFORMASI & KOMUNIKASI (IT HELPDESK SYSTEM)</div>
                <div style="font-size: 8px; color: #64748B;">Layanan Pengaduan Gangguan Sistem Informasi, Jaringan, dan Perangkat Medis / Non-Medis</div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 8px; color: #64748B;">Dicetak Tanggal:</div>
                <div style="font-weight: bold; font-size: 9px; color: #1E293B;">{{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <!-- Judul Laporan -->
    <div class="report-header">
        <div class="report-title">LAPORAN REKAPITULASI PENGADUAN & LAYANAN TIKET IT</div>
    </div>

    <!-- Parameter Filter Aktif -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Periode Waktu:</td>
            <td style="width: 250px;">{{ $activeFilters['periode'] }}</td>
            <td class="meta-label">Status Tiket:</td>
            <td>{{ $activeFilters['status'] }}</td>
        </tr>
        <tr>
            <td class="meta-label">Unit / Ruangan RS:</td>
            <td>{{ $activeFilters['unit'] }}</td>
            <td class="meta-label">Teknisi Penanggung Jawab:</td>
            <td>{{ $activeFilters['teknisi'] }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kategori Kendala:</td>
            <td>{{ $activeFilters['category'] }}</td>
            <td class="meta-label">Total Data Ditemukan:</td>
            <td><strong style="color: #4F46E5;">{{ number_format($summary['total']) }} Tiket</strong></td>
        </tr>
    </table>

    <!-- Tabel Ringkasan Metrik -->
    <table class="summary-table">
        <thead>
            <tr>
                <th style="width: 16%;">Total Tiket</th>
                <th style="width: 16%;">Tiket Baru</th>
                <th style="width: 16%;">Diproses</th>
                <th style="width: 16%;">Selesai</th>
                <th style="width: 16%;">Ditutup</th>
                <th style="width: 20%;">Rata-rata Waktu Selesai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color: #0F172A;">{{ $summary['total'] }}</td>
                <td style="color: #2563EB;">{{ $summary['baru'] }}</td>
                <td style="color: #D97706;">{{ $summary['diproses'] }}</td>
                <td style="color: #059669;">{{ $summary['selesai'] }}</td>
                <td style="color: #475569;">{{ $summary['ditutup'] }}</td>
                <td style="color: #7C3AED;">{{ $summary['avg_duration'] }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Data Tiket -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px;" class="text-center">No</th>
                <th style="width: 90px;">Kode Tiket</th>
                <th style="width: 75px;">Tgl Lapor</th>
                <th style="width: 95px;">Pelapor</th>
                <th style="width: 85px;">Unit RS</th>
                <th style="width: 70px;">Kategori</th>
                <th>Judul Kendala</th>
                <th style="width: 45px;" class="text-center">Prioritas</th>
                <th style="width: 45px;" class="text-center">Status</th>
                <th style="width: 85px;">Teknisi</th>
                <th style="width: 75px;">Tgl Selesai</th>
                <th style="width: 60px;">Durasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $index => $ticket)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $ticket->kode }}</td>
                    <td>{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $ticket->user?->name ?? '-' }}</td>
                    <td>{{ $ticket->unit?->nama ?? '-' }}</td>
                    <td>{{ $ticket->category?->nama ?? '-' }}</td>
                    <td>{{ $ticket->judul }}</td>
                    <td class="text-center">
                        <span class="badge-{{ $ticket->prioritas }}">
                            {{ ucfirst($ticket->prioritas) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $ticket->status }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </td>
                    <td>{{ $ticket->teknisi?->name ?? '-' }}</td>
                    <td>{{ $ticket->waktu_selesai ? $ticket->waktu_selesai->format('d/m/Y H:i') : '-' }}</td>
                    <td style="font-weight: 500;">{{ $ticket->durasi_penyelesaian }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 20px; color: #94A3B8;">
                        Tidak ada data tiket yang sesuai dengan parameter filter laporan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Lembar Tanda Tangan / Verifikasi -->
    <table class="signature-table">
        <tr>
            <td style="width: 65%;"></td>
            <td style="width: 35%; text-align: center;">
                <div>Rumah Sakit, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 4px; font-weight: bold;">Kepala Instalasi TI / Administrator</div>
                <div style="height: 50px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Administrator Sistem' }}</div>
                <div style="color: #64748B; font-size: 8.5px;">NIP / ID: {{ auth()->user()->id ?? 'ADMIN-IT' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>

