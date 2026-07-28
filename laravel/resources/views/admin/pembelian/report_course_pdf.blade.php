<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Materi - {{ $course->title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        .invoice-box {
            max-width: 100%;
            margin: auto;
            background: #fff;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
        }

        .logo-img {
            vertical-align: middle;
            margin-right: 10px;
            width: 36px;
            height: auto;
        }

        .brand-name {
            font-size: 20px;
            font-weight: bold;
            color: #4f46e5;
            display: inline-block;
            vertical-align: middle;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-title {
            text-align: right;
            font-size: 12px;
            text-transform: uppercase;
            color: #4b5563;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .course-summary-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .course-main-title {
            font-size: 15px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 6px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .details-table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #4f46e5;
        }

        .details-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .details-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge-success {
            background-color: #ecfdf5;
            color: #047857;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 8px;
            border: 1px solid #a7f3d0;
            display: inline-block;
        }

        .total-section-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .total-section-table td {
            padding: 6px 10px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td style="border: none; padding: 0;">
                    @if (file_exists(public_path('assets/images/Eduvan.png')))
                        <img class="logo-img" src="{{ public_path('assets/images/Eduvan.png') }}" alt="Logo">
                    @endif
                    <span class="brand-name">EduLearn</span>
                </td>
                <td class="report-title" style="border: none; padding: 0;">
                    Laporan Penjualan Materi Kursus
                </td>
            </tr>
        </table>

        <div class="course-summary-box">
            <div class="summary-title">Materi / Kursus Terpilih</div>
            <div class="course-main-title">{{ $course->title }}</div>
            <table style="width: 100%; font-size: 11px; margin-top: 6px;">
                <tr>
                    <td style="border: none; padding: 0; color: #4b5563;">
                        Total Terjual: <strong style="color: #4f46e5;">{{ $totalSold }} Siswa</strong>
                    </td>
                    <td style="border: none; padding: 0; text-align: right; color: #4b5563;">
                        Metode Pembayaran: <strong style="color: #059669;">DompetX Automated Gateway</strong>
                    </td>
                </tr>
            </table>
        </div>

        <div class="summary-title" style="margin-bottom: 8px;">Daftar Riwayat Student Pembeli</div>

        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">No</th>
                    <th style="width: 120px;">Tanggal Membeli</th>
                    <th>Nama Student</th>
                    <th style="width: 80px; text-align: center;">Status</th>
                    <th style="width: 100px; text-align: right;">Harga Beli</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($course->enrollments as $index => $enroll)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="color: #6b7280;">
                            {{ $enroll->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                        </td>
                        <td>
                            <strong>{{ $enroll->user->name ?? 'Student' }}</strong>
                            <span style="display: block; font-size: 9px; color: #9ca3af; font-weight: normal;">
                                {{ $enroll->user->email ?? '-' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-success">LUNAS</span>
                        </td>
                        <td style="text-align: right; font-weight: bold; color: #111827;">
                            Rp {{ number_format($enroll->price_bought, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            style="text-align: center; color: #9ca3af; font-style: italic; padding: 20px;">
                            Belum ada riwayat pembelian mahasiswa untuk materi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="total-section-table">
            <tr>
                <td style="width: 50%; border: none;"></td>
                <td
                    style="width: 25%; text-align: right; font-weight: bold; color: #4b5563; font-size: 10px; uppercase; border: none;">
                    Total Pendapatan:
                </td>
                <td
                    style="width: 25%; text-align: right; font-size: 14px; font-weight: 800; color: #059669; border-top: 2px dashed #e5e7eb;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div class="footer">
            Dokumen ini merupakan laporan resmi penjualan materi yang diproses otomatis oleh Sistem EduLearn &
            DompetX.<br>
            Dicetak otomatis pada: {{ $downloaded_at }}
        </div>
    </div>

</body>

</html>
