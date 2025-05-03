<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .result-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
        }
        .result-box h3 {
            margin-bottom: 25px;
            color: #1e3c72;
            text-align: center;
        }
        .table th {
            width: 30%;
        }
    </style>
</head>
<body>

<div class="result-box">
    <h3>Hasil Pencarian Siswa</h3>
    <table class="table table-bordered">
        <tr><th>Nama Lengkap</th><td><?= $siswa->nama_lengkap ?></td></tr>
        <tr><th>NIS</th><td><?= $siswa->nis ?></td></tr>
        <tr><th>Kelas</th><td><?= $siswa->kelas ?></td></tr>
        <tr><th>Status</th><td><?= ucfirst($siswa->status) ?></td></tr>
    </table>

    <a href="<?= base_url('skl/download_skl/' . $siswa->nis) ?>" class="btn btn-success w-100 mt-3" target="_blank">
        Download SKL (PDF)
    </a>
</div>

</body>
</html>
