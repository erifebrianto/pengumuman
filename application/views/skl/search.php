<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Data Kelulusan</title>
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
        .search-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }
        .search-box h3 {
            margin-bottom: 25px;
            color: #1e3c72;
        }
    </style>
</head>
<body>

<div class="search-box text-center">
    <h3>Cari Data Kelulusan</h3>
    <form method="post" action="<?= base_url('skl/result') ?>">
        <input type="text" name="nis" class="form-control mb-3" placeholder="Masukkan NIS" required>
        <button type="submit" class="btn btn-primary w-100">Cari</button>
    </form>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger mt-3"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>
</div>

</body>
</html>
