<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian Kursus - EduLearn</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .kop-surat {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .kop-title {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .kop-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .meta-table {
            width: 100%;
            margin-top: 10px;
            font-size: 10px;
            color: #4b5563;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.data-table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #4f46e5;
        }

        table.data-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .badge-success {
            background-color: #ecfdf5;
            color: #047857;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #a7f3d0;
            display: inline-block;
        }

        .summary-box {
            margin-top: 25px;
            float: right;
            width: 250px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #f9fafb;
            padding: 12px;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .summary-label {
            display: table-cell;
            font-size: 10px;
            color: #6b7280;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #111827;
        }

        .grand-total {
            border-top: 1px dashed #d1d5db;
            padding-top: 6px;
            margin-top: 6px;
        }

        .grand-total .summary-label {
            font-weight: bold;
            color: #111827;
        }

        .grand-total .summary-value {
            font-size: 13px;
            color: #059669;
            font-weight: 800;
        }

        .footer-note {
            margin-top: 150px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="kop-surat">
        <table style="width: 100%;">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1 class="kop-title">EduLearn Platform</h1>
                    <div class="kop-subtitle">Laporan Rekapitulasi Transaksi Pembelian Kursus</div>
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <span style="font-size: 12px; font-weight: bold; color: #4f46e5;">DOMPETX AUTOMATED GATEWAY</span>
                </td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td style="border: none; padding: 0;">
                    <strong>Tanggal Cetak:</strong>
                    {{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <strong>Dicetak Oleh:</strong> Administrator EduLearn
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 110px;">Tanggal Transaksi</th>
                <th>Nama Student</th>
                <th>Materi Kursus</th>
                <th class="text-center" style="width: 90px;">Metode</th>
                <th class="text-right" style="width: 100px;">Harga Beli</th>
                <th class="text-center" style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $t->created_at ? $t->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }} WIB
                    </td>
                    <td>
                        <strong>{{ $t->user->name ?? 'Student' }}</strong>
                    </td>
                    <td>{{ $t->course->title ?? '-' }}</td>
                    <td class="text-center">DompetX</td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($t->amount ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge-success">LUNAS</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #9ca3af; font-style: italic;">
                        Belum ada data transaksi pembelian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Transaksi Lunas:</span>
            <span class="summary-value">{{ $transactions->count() }} Transaksi</span>
        </div>
        <div class="summary-row grand-total">
            <span class="summary-label">Grand Total Pendapatan:</span>
            <span class="summary-value">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>
    <div class="footer-note">
        Dokumen ini dibuat dan divalidasi secara otomatis oleh Sistem EduLearn Dashboard & DompetX Payment Gateway.
    </div>

</body>

</html>
