<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Absensi {{ $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 1.97in;
            height: 3.34in;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #0a0f24;
            background: #ffffff;
        }

        .card {
            width: 1.97in;
            height: 3.34in;
            /* ⚠️ dompdf tidak support linear-gradient → pakai solid color */
            background-color: #0a0f24;
            color: #e7ecff;
            padding: 0.10in 0.12in;
            position: relative;
        }

        /* Border emas */
        .card::before {
            content: '';
            position: absolute;
            top: 0.05in;
            left: 0.05in;
            right: 0.05in;
            bottom: 0.05in;
            border: 1.5pt solid #ffcc33;
            border-radius: 6pt;
            pointer-events: none;
        }

        .header {
            text-align: center;
            padding-top: 0.12in;
        }

        .header .brand {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 1pt;
            color: #ffcc33;
            text-transform: uppercase;
        }

        .header .title {
            font-size: 9pt;
            font-weight: bold;
            color: #ffffff;
            margin-top: 2pt;
        }

        .info {
            text-align: center;
            margin-top: 0.12in;
        }

        .info .name {
            font-size: 10pt;
            font-weight: bold;
            color: #ffffff;
            line-height: 1.2;
        }

        .info .meta {
            font-size: 7pt;
            color: #38bdf8;
            margin-top: 3pt;
        }

        .qr-box {
            margin: 0.12in auto 0;
            width: 1.30in;
            height: 1.30in;
            background-color: #ffffff;
            padding: 6pt;
            text-align: center;
        }

        .status {
            text-align: center;
            margin-top: 0.12in;
        }

        .status span {
            display: inline-block;
            font-size: 6.5pt;
            font-weight: bold;
            color: #22e07b;
            border: 0.5pt solid #22e07b;
            border-radius: 8pt;
            padding: 2pt 8pt;
            letter-spacing: 0.5pt;
            text-transform: uppercase;
        }

        .footer {
            position: absolute;
            bottom: 0.12in;
            left: 0.12in;
            right: 0.12in;
            text-align: center;
        }

        .footer .label {
            font-size: 5.5pt;
            color: #94a3b8;
            letter-spacing: 0.5pt;
            text-transform: uppercase;
        }

        .footer .id {
            font-size: 8pt;
            color: #ffcc33;
            font-weight: bold;
            letter-spacing: 1pt;
            margin-top: 2pt;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            <div class="brand">{{ $appName }}</div>
            <div class="title">KARTU ABSENSI</div>
        </div>

        <div class="info">
            <div class="name">{{ strtoupper($student->name) }}</div>
            <div class="meta">{{ $student->id_siswa }} · {{ $student->kelas }}</div>
        </div>

        <div class="qr-box">
            {!! $qrSvg !!}
        </div>

        <div class="status">
            <span>QR PERMANEN</span>
        </div>

        <div class="footer">
            <div class="label">Scan untuk absen</div>
            <div class="id">{{ $student->id_siswa }}</div>
        </div>
    </div>
</body>

</html>
