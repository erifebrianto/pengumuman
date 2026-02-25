<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengumuman Kelulusan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }

    .search-box {
      transition: all 0.3s ease;
    }

    .input-focus-effect:focus {
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .alert-entrance {
      animation: alertEntrance 0.5s ease-out;
    }

    @keyframes alertEntrance {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4" style="background-image: url('<?= base_url($background) ?>'); background-size: cover; background-repeat: no-repeat;">

  <div class="w-full max-w-md">
    <div class="search-box bg-white rounded-xl shadow-md p-8">
      <div class="text-center mb-6">
        <!-- Logo Sekolah -->
        <div class="mb-4">
          <img src="<?= base_url($logo_sekolah) ?>" alt="Logo Sekolah" class="mx-auto h-20 w-auto">
        </div>

        <!-- Nama Sekolah -->
        <h2 class="text-xl font-semibold text-gray-700 mb-2"><?= $nama_sekolah ?></h2>
        <p class="text-gray-500">Masukkan Nomor Ujian dan NIS untuk melihat kelulusan</p>
      </div>


      <!-- Countdown -->
      <?php
      date_default_timezone_set('Asia/Jakarta');
        $target_timestamp = isset($countdown->waktu_target) ? strtotime($countdown->waktu_target) : time();
      ?>
      <div id="countdown-box" class="mb-6 text-center <?= (time() >= $target_timestamp) ? 'hidden' : '' ?>">
        <div class="text-xl font-semibold text-gray-700 mb-4">Pengumuman dibuka dalam:</div>
        <div id="countdown-timer" class="grid grid-cols-4 gap-3 justify-center">
          <div class="bg-blue-100 text-blue-800 p-4 rounded-lg">
            <div class="text-2xl font-bold" id="countdown-days">0</div>
            <div class="text-sm font-medium mt-1">Hari</div>
          </div>
          <div class="bg-blue-100 text-blue-800 p-4 rounded-lg">
            <div class="text-2xl font-bold" id="countdown-hours">00</div>
            <div class="text-sm font-medium mt-1">Jam</div>
          </div>
          <div class="bg-blue-100 text-blue-800 p-4 rounded-lg">
            <div class="text-2xl font-bold" id="countdown-minutes">00</div>
            <div class="text-sm font-medium mt-1">Menit</div>
          </div>
          <div class="bg-blue-100 text-blue-800 p-4 rounded-lg">
            <div class="text-2xl font-bold" id="countdown-seconds">00</div>
            <div class="text-sm font-medium mt-1">Detik</div>
          </div>
        </div>
      </div>


    <!-- Form Pencarian -->
    <form method="post" action="<?= base_url('skl/result') ?>" class="space-y-4 <?= (time() < $target_timestamp) ? 'hidden' : '' ?>" id="form-box">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="bi bi-123 text-gray-400"></i>
            </div>
            <input 
              type="text" 
              name="no_ujian" 
              class="input-focus-effect w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
              placeholder="Masukkan No. Ujian (contoh: 2025-0309-002)" 
              required
              pattern="\d{4}-\d{4}-\d{3}"
              title="Format: 2025-0309-002"
            />

        </div>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="bi bi-person text-gray-400"></i>
            </div>
            <input 
                type="text" 
                name="nis" 
                class="input-focus-effect w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                placeholder="Masukkan NIS" 
                required
                pattern="[0-9]*"
                inputmode="numeric"
                title="Masukkan hanya angka NIS"
            >
        </div>
        <button 
            type="submit" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
        >
            <i class="bi bi-search"></i>
            Cari Data
        </button>
    </form>


      <!-- Error Message -->
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger mt-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg alert-entrance">
          <div class="flex items-center">
            <i class="bi bi-exclamation-circle-fill text-red-500 mr-2"></i>
            <span><?= $this->session->flashdata('error') ?></span>
          </div>
        </div>
      <?php endif; ?>

      <!-- Additional Info -->
      <div class="mt-6 pt-6 border-t border-gray-100">
        <div class="text-center text-sm text-gray-500">
          <p class="flex items-center justify-center gap-1">
            <i class="bi bi-info-circle"></i>
            Hubungi admin jika mengalami kesalahan data
          </p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-6 text-sm text-gray-500">
      <p>Sistem Informasi Kelulusan © <?= date('Y') ?></p>
    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const targetTime = <?= $target_timestamp * 1000 ?>;
    const countdownBox = document.getElementById('countdown-box');
    const formBox = document.getElementById('form-box');

    const daysEl = document.getElementById('countdown-days');
    const hoursEl = document.getElementById('countdown-hours');
    const minutesEl = document.getElementById('countdown-minutes');
    const secondsEl = document.getElementById('countdown-seconds');

    function updateCountdown() {
      const now = new Date().getTime();
      const distance = targetTime - now;

      if (distance <= 0) {
        clearInterval(interval);
        countdownBox.classList.add('hidden');
        formBox.classList.remove('hidden');
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      daysEl.textContent = days;
      hoursEl.textContent = String(hours).padStart(2, '0');
      minutesEl.textContent = String(minutes).padStart(2, '0');
      secondsEl.textContent = String(seconds).padStart(2, '0');
    }

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
  });
</script>

</body>
</html>
