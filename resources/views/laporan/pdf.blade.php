<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Permohonan</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px;
            color: #333;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo {
            width: 70px;
            height: 70px;
        }
        .company-details {
            text-align: right;
        }
        .company-details h2 {
            margin: 0;
            font-size: 18px;
            color: #b22222; /* Merah Tua */
        }
        .report-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title h1 {
            margin: 0;
            font-size: 22px;
        }
        .report-title p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #555;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-table th, .main-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .main-table thead {
            background-color: #f2f2f2;
        }
        .main-table th {
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 9px;
            color: #888;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <img src="{{ public_path('images/Logo notaris bu lilis.png') }}" alt="Logo" class="logo">
            </td>
            <td class="company-details">
                <h2>LAJ Notary Hub</h2>
                <p>
                    Kantor Notaris & PPAT Lilis Aenun Jariah S.H., M.Kn.<br>
                    Jl. Raya Bandung No.11, Bojong, Kec. Karangtengah, Kabupaten Cianjur, Jawa Barat 43281<br>
                    +62-812-9075-3695 | lajnotary@gmail.com
                </p>
            </td>
        </tr>
    </table>

    <div class="report-title">
        <h1>Laporan Pengajuan Permohonan</h1>
        @if (!empty($filters['start_date']) && !empty($filters['end_date']))
            <p>Periode: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}</p>
        @endif
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pemohon</th>
                <th>Email</th>
                <th>Jenis Layanan</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($permohonans as $permohonan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($permohonan->client)->name ?? '[N/A]' }}</td>
                    <td>{{ optional($permohonan->client)->email ?? '[N/A]' }}</td>
                    <td>{{ optional($permohonan->service)->name ?? '[N/A]' }}</td>
                    <td>{{ $permohonan->created_at->format('d-m-Y') }}</td>
                    <td>{{ $permohonan->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data untuk periode yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y, H:i:s') }}
    </div>

</body>
</html>