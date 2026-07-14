<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Hasil Belajar Siswa - EduLearn</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.4;
            margin: 30px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-logo {
            font-size: 24px;
            font-weight: 900;
            color: #0d9488;
            letter-spacing: -1px;
        }

        .header-sub {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }

        .title-transkrip {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0;
            letter-spacing: 1px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .biodata-table {
            width: 100%;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .biodata-table td {
            padding: 4px 0;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .main-table th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            text-align: left;
        }

        .main-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .summary-box {
            margin-top: 25px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }

        .footer-sign {
            width: 100%;
            margin-top: 40px;
        }

        .footer-sign td {
            text-align: center;
            width: 50%;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" ...>...</button>
    </div>
    <table class="header-table">
        <tr>
            <td>
                <span class="header-logo">Edu<span style="color: #334155;">Learn</span></span><br>
                <span class="header-sub">Sistem Manajemen Pembelajaran Inklusif Berdiferensiasi</span>
            </td>
            <td style="text-align: right; font-weight: bold; font-size: 11px; color: #475569;">
                KODE FORM: F03/EL-{{ strtoupper(Str::slug($schoolName)) }}<br>
                TAHUN AJARAN: 2026/2027
            </td>
        </tr>
    </table>

    <div class="title-transkrip">Laporan Hasil Belajar Siswa (Rapor)</div>
    <table class="biodata-table">
        <tr>
            <td style="width: 150px;">NISN Siswa</td>
            <td style="width: 10px;">:</td>
            <td>{{ $student->nisn_or_nip }}</td>
        </tr>
        <tr>
            <td>Nama Lengkap Siswa</td>
            <td>:</td>
            <td style="text-transform: uppercase;">{{ $student->name }}</td>
        </tr>
        <tr>
            <td>Tingkat Kelas / Sekolah</td>
            <td>:</td>
            <td>{{ $student->class }} - {{ $schoolName }}</td>
        </tr>
    </table>
    <table class="main-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>Nama Mapel</th>
                <th class="text-center" style="width: 120px;">Jumlah Bab Diikuti</th>
                <th class="text-center" style="width: 120px;">Total Nilai Kuis</th>
                <th class="text-center" style="width: 100px;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($raporData as $row)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td><strong>{{ $row['mapel_name'] }}</strong></td>
                    <td class="text-center">{{ $row['count_bab'] }} Bab</td>
                    <td class="text-center" style="font-weight: bold;">{{ $row['total_score'] }}</td>
                    <td class="text-center"
                        style="font-weight: bold; color: {{ $row['grade'] == 'E' ? 'red' : 'black' }};">
                        {{ $row['grade'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">Belum ada riwayat
                        nilai kuis yang terekam untuk siswa ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="summary-box">
        Total Bab Selesai: {{ $totalBabSelesai }} Bab<br>
        Rata-Rata Nilai Kelulusan: {{ round($rataRataKelulusan, 2) }} / 100
    </div>
    <table class="footer-sign">
        <tr>
            <td></td>
            <td>
                Karawang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Diketahui Oleh,<br>
                Guru <br><br><br><br>
                <u><strong>{{ $teacherName }}</strong></u><br>
                ELEARN.2026
            </td>
        </tr>
    </table>

</body>

</html>
