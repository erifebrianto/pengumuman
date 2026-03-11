<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pengumuman Kelulusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow-x: hidden;
            color: #1e293b;
        }

        /* Abstract Background Elements */
        .bg-circle-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            z-index: 0;
            animation: float 8s ease-in-out infinite;
        }

        .bg-circle-2 {
            position: absolute;
            bottom: -15%;
            right: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14,165,233,0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            z-index: 0;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
            100% { transform: translateY(0px) scale(1); }
        }

        /* Glassmorphism Card */
        .result-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
            text-align: center;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Profile Area */
        .profile-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .student-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #ffffff;
            background: #ffffff;
            padding: 4px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: transform 0.4s ease;
        }

        .glass-card:hover .student-photo {
            transform: scale(1.08) rotate(5deg);
        }

        /* Typography */
        .student-name {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .student-nis {
            font-size: 1rem;
            color: #64748b;
            font-weight: 400;
            margin-bottom: 2rem;
            letter-spacing: 1px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2.5rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.5s forwards;
            opacity: 0;
            transform: scale(0.8);
        }

        @keyframes popIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .status-lulus {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .status-gagal {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: white;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .status-icon {
            font-size: 1.4rem;
            margin-right: 10px;
        }

        /* Premium Download Button */
        .btn-download {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            border-radius: 16px;
            border: 1px solid rgba(79, 70, 229, 0.3);
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
            opacity: 0;
            animation: slideUp 0.8s 0.8s forwards;
        }

        .btn-download::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px -5px rgba(79, 70, 229, 0.5);
            color: white;
        }

        .btn-download:hover::before {
            left: 100%;
        }

        .btn-download i {
            margin-right: 12px;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .btn-download:hover i {
            transform: translateY(2px);
        }
        
        /* Back link */
        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.3s ease;
            opacity: 0;
            animation: slideUp 0.8s 1s forwards;
        }
        
        .back-link:hover {
            color: #0f172a;
        }

    </style>
</head>
<body>

    <!-- Dynamic Background Elements -->
    <div class="bg-circle-1"></div>
    <div class="bg-circle-2"></div>

    <div class="result-wrapper">
        <div class="glass-card">
            
            <div class="profile-container">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($siswa->nama_lengkap) ?>&background=e2e8f0&color=0f172a&size=256&bold=true" alt="Foto Siswa" class="student-photo">
            </div>

            <h2 class="student-name"><?= $siswa->nama_lengkap ?></h2>
            <p class="student-nis">
                <i class="fas fa-id-card me-2"></i> NIS: <?= $siswa->nis ?> &nbsp;|&nbsp; KELAS: <?= $siswa->kelas ?>
            </p>


            <?php if (strtolower($siswa->status) == 'lulus'): ?>
                <p class="mb-3" style="font-size: 1.1rem; color: #334155;">Selamat! Anda telah dinyatakan memenuhi syarat.</p>
                <div class="status-badge status-lulus">
                    <i class="fas fa-check-circle status-icon"></i> LULUS
                </div>
                
                <a href="<?= base_url('skl/download_skl/' . $siswa->token_download) ?>" class="btn-download" id="downloadBtn">
                    <i class="fas fa-cloud-download-alt"></i> Unduh Surat Kelulusan (PDF)
                </a>
            <?php else: ?>
                <p class="mb-3" style="font-size: 1.1rem; color: #475569;">Mohon maaf, Anda belum memenuhi syarat kelulusan.</p>
                <div class="status-badge status-gagal">
                    <i class="fas fa-times-circle status-icon"></i> TIDAK LULUS
                </div>
                
                <a href="<?= base_url('skl/download_skl/' . $siswa->token_download) ?>" class="btn-download" style="background: linear-gradient(135deg, #475569 0%, #334155 100%); box-shadow: 0 10px 25px -5px rgba(51, 65, 85, 0.4);" id="downloadBtn">
                    <i class="fas fa-file-pdf"></i> Unduh Keterangan (PDF)
                </a>
            <?php endif; ?>

            <div>
                <a href="<?= base_url('skl/search') ?>" class="back-link">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Pencarian
                </a>
            </div>

        </div>
    </div>

    <!-- JS for Interactive Download Button Feedback -->
    <script>
        document.getElementById('downloadBtn').addEventListener('click', function(e) {
            let btn = this;
            let originalHtml = btn.innerHTML;
            
            // Add loading state
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyiapkan Dokumen...';
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.8';
            
            // Restore button after 3 seconds (assuming download starts)
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> Pengunduhan Dimulai';
                btn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.style.pointerEvents = 'auto';
                    btn.style.opacity = '1';
                    btn.style.background = ''; // reset to CSS default
                }, 3000);
            }, 2000);
        });
    </script>
</body>
</html>
