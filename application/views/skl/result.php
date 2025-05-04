<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Kelulusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: cover;
        }
        .result-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }
        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .student-photo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-download {
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container position-relative">
        <h1 class="display-4 fw-bold mb-3">PENGUMUMAN KELULUSAN</h1>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="result-card card mb-4">
                <div class="card-body p-4 text-center">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($siswa->nama_lengkap) ?>&background=random" alt="Foto Siswa" class="student-photo mb-3">
                    <h3 class="card-title mb-1"><?= $siswa->nama_lengkap ?></h3>
                    <p class="text-muted">Kelas : <?= $siswa->kelas ?></p>               

                    <?php if (strtolower($siswa->status) == 'lulus'): ?>
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Selamat!</h4>
                            <p class="mb-0">Anda telah dinyatakan <strong>LULUS</strong>. Silakan unduh SKL Anda.</p>
                        </div>
                        <a href="<?= base_url('skl/download_skl/' . $siswa->nis) ?>" class="btn btn-success btn-download mt-3" target="_blank">
                            <i class="fas fa-download me-1"></i> Download SKL (PDF)
                        </a>
                    <?php else: ?>
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading"><i class="fas fa-times-circle me-2"></i>Tidak Lulus</h4>
                            <p class="mb-0">Anda belum memenuhi kriteria kelulusan.</p>
                        </div>
                        <a href="<?= base_url('skl/download_skl/' . $siswa->nis) ?>" class="btn btn-success w-100 mt-3" target="_blank">
                            Download SKL (PDF)
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
