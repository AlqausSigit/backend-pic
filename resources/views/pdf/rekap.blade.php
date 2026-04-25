<!DOCTYPE html>
<html>
<head>
    <title>Download PDF Rekap MBG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary-box {
            border: 1px solid #000;
            padding: 15px;
            width: 50%;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <h2>Rekap Transaksi Program Makan Bergizi Gratis (MBG)</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kelas</th>
                <th>Jurusan</th>
                <th>Total Ambil / Kembali (transaksi)</th>
                <th>Total Porsi MBG</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $item->kelas->jurusan ?? '-' }}</td>
                <td>{{ $item->total_transaksi }}</td>
                <td>{{ $item->total_porsi }} Porsi</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h4>Ringkasan Data:</h4>
        <p><strong>Total Kelas yang Telah Terdata:</strong> {{ count($data) }} Kelas</p>
        <p><strong>Total Seluruh Transaksi:</strong> {{ $total_semua['total_transaksi'] }}</p>
        <p><strong>Total Seluruh Porsi:</strong> {{ $total_semua['total_porsi'] }} Porsi</p>
    </div>

    <p style="text-align: right; margin-top: 50px;">
        Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}
    </p>

</body>
</html>
