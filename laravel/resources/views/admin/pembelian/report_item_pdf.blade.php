<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kuitansi Laporan Transaksi - {{ $trans->user->name ?? 'Student' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #374151;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            font-size: 12px;
            background-color: #ffffff;
        }

        .invoice-card {
            max-width: 100%;
            margin: auto;
            background: #ffffff;
        }

        .header-container {
            border-bottom: 2px dashed #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo-img {
            vertical-align: middle;
            margin-right: 10px;
            width: 38px;
            height: auto;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #4f46e5;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .brand-tagline {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .invoice-title {
            text-align: right;
            font-size: 14px;
            font-weight: 800;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .meta-section {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .meta-block {
            width: 50%;
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .meta-value {
            font-size: 12px;
            color: #1f2937;
            line-height: 1.4;
        }

        .item-details-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .course-title-label {
            font-size: 10px;
            color: #4f46e5;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .course-main-title {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 6px;
        }

        .course-description {
            font-size: 11px;
            color: #6b7280;
        }

        .payment-summary {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 15px;
        }

        .summary-row td {
            padding: 4px 0;
            font-size: 12px;
        }

        .summary-label {
            color: #4b5563;
        }

        .summary-value {
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }

        .grand-total-row {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 10px 12px;
            margin-top: 10px;
        }

        .grand-total-label {
            font-size: 13px;
            font-weight: 700;
            color: #065f46;
        }

        .grand-total-value {
            font-size: 16px;
            font-weight: 800;
            color: #059669;
            text-align: right;
        }

        .badge-success {
            color: #047857;
            font-weight: bold;
            font-size: 10px;
            background-color: #d1fae5;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
            display: inline-block;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 12px;
        }
    </style>
</head>

<body>

    <div class="invoice-card">
        <table style="width: 100%; border-collapse: collapse;" class="header-container">
            <tr>
                <td style="border: none; padding: 0; vertical-align: middle;">
                    @if (file_exists(public_path('assets/images/Eduvan.png')))
                        <img class="logo-img" src="{{ public_path('assets/images/Eduvan.png') }}" alt="Logo">
                    @endif
                    <div style="display: inline-block; vertical-align: middle;">
                        <div class="brand-name">EduLearn</div>
                        <div class="brand-tagline">Learning Course Marketplace</div>
                    </div>
                </td>
                <td class="invoice-title" style="border: none; padding: 0; vertical-align: middle;">
                    Kuitansi Pembayaran
                </td>
            </tr>
        </table>
        <table class="meta-section">
            <tr>
                <td class="meta-block">
                    <div class="meta-label">Diterbitkan Kepada:</div>
                    <div class="meta-value">
                        <strong>{{ $trans->user->name ?? 'Student' }}</strong><br>
                        Email: {{ $trans->user->email ?? '-' }}<br>
                        <span style="color: #6b7280; font-size: 11px;">Status: Registered Student</span>
                    </div>
                </td>
                <td class="meta-block" style="text-align: right;">
                    <div class="meta-label">Detail Transaksi:</div>
                    <div class="meta-value">
                        Nomor Ref: <span
                            style="font-family: monospace; font-weight: bold; color: #4f46e5;">#EDX-{{ $trans->id }}</span><br>
                        Tanggal: {{ $trans->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB<br>
                        Metode: <strong>{{ $trans->payment_method ?? 'DompetX Gateway' }}</strong><br>
                        Status: <span class="badge-success">LUNAS / SUCCESS</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="item-details-box">
            <div class="course-title-label">Materi Pembelajaran</div>
            <div class="course-main-title">{{ $trans->course->title ?? '-' }}</div>
            <div class="course-description">
                Selamat, Anda telah mendapatkan hak akses penuh secara permanen untuk modul pembelajaran digital ini di
                dalam sistem platform EduLearn.
            </div>
        </div>

        <div class="payment-summary">
            <table style="width: 100%; border-collapse: collapse;">
                <tr class="summary-row">
                    <td class="summary-label">Subtotal Harga Materi</td>
                    <td class="summary-value">Rp {{ number_format($trans->price_bought, 0, ',', '.') }}</td>
                </tr>
                <tr class="summary-row">
                    <td class="summary-label">Biaya Layanan Gateway</td>
                    <td class="summary-value" style="color: #059669;">Termasuk (Rp 0)</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 8px;">
                        <table style="width: 100%; border-collapse: collapse;" class="grand-total-row">
                            <tr>
                                <td class="grand-total-label" style="border: none;">Total Pembayaran Lunas</td>
                                <td class="grand-total-value" style="border: none;">
                                    Rp {{ number_format($trans->price_bought, 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Kuitansi digital ini diterbitkan secara sah dan diproses otomatis via DompetX Gateway.<br>
            <span style="color: #9ca3af;">Dicetak otomatis pada: {{ $downloaded_at }}</span>
        </div>

    </div>

</body>

</html>
