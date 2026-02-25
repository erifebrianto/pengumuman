<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kelulusan</title>
  <!-- Bootstrap 5 CSS -->
<!--   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
 -->  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }

    .card-gradient-primary {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .card-gradient-success {
      background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    }

    .card-gradient-danger {
      background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
    }

    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
    }

    .glass-effect {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 10px;
      border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .hover-scale {
      transition: transform 0.3s ease;
    }

    .hover-scale:hover {
      transform: translateY(-5px);
    }
    .mr-4 {
        margin-right: 1rem;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="page-inner">
      <!-- Header Section -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
          <h1 class="h3 text-dark">Dashboard Kelulusan</h1>
          <p class="text-muted">Statistik Kelulusan Siswa Tahun Ajaran <?= date('Y') ?></p>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <!-- Total Siswa Card -->
        <div class="col">
          <div class="card card-gradient-primary text-white rounded-3 shadow-sm hover-scale">
            <div class="card-body d-flex">
              <div class="glass-effect p-4 rounded-full mr-4">
                <i class="bi bi-people-fill fs-3"></i>
              </div>
              <div>
                <p class="fs-6">Total Siswa</p>
                <h3 class="fw-bold"><?= $total_siswa ?></h3>
              </div>
            </div>
            <div class="card-footer bg-transparent text-white-50">
              <p class="small d-flex align-items-center">
                <span class="d-inline-block w-2 h-2 rounded-circle bg-blue-200 me-2"></span>
                Seluruh siswa yang terdaftar
              </p>
            </div>
          </div>
        </div>

        <!-- Lulus Card -->
        <div class="col">
          <div class="card card-gradient-success text-white rounded-3 shadow-sm hover-scale">
            <div class="card-body d-flex">
              <div class="glass-effect p-4 rounded-full mr-4">
                <i class="bi bi-check-circle-fill fs-3"></i>
              </div>
              <div>
                <p class="fs-6">Jumlah Lulus</p>
                <h3 class="fw-bold"><?= $jumlah_lulus ?></h3>
              </div>
            </div>
            <div class="card-footer bg-transparent text-white-50">
              <p class="small d-flex align-items-center">
                <span class="d-inline-block w-2 h-2 rounded-circle bg-green-200 me-2"></span>
                <?= round(($jumlah_lulus/$total_siswa)*100, 2) ?>% dari total siswa
              </p>
            </div>
          </div>
        </div>

        <!-- Tidak Lulus Card -->
        <div class="col">
          <div class="card card-gradient-danger text-white rounded-3 shadow-sm hover-scale">
            <div class="card-body d-flex">
              <div class="glass-effect p-4 rounded-full mr-4">
                <i class="bi bi-x-circle-fill fs-3"></i>
              </div>
              <div>
                <p class="fs-6">Tidak Lulus</p>
                <h3 class="fw-bold"><?= $jumlah_tidak_lulus ?></h3>
              </div>
            </div>
            <div class="card-footer bg-transparent text-white-50">
              <p class="small d-flex align-items-center">
                <span class="d-inline-block w-2 h-2 rounded-circle bg-red-200 me-2"></span>
                <?= round(($jumlah_tidak_lulus/$total_siswa)*100, 2) ?>% dari total siswa
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="row g-4">
        <!-- Pie Chart -->
        <div class="col-12 col-lg-6">
          <div class="card shadow-sm">
            <div class="card-header">
              <h5 class="card-title">Diagram Kelulusan</h5>
              <p class="card-text text-muted small">Persentase kelulusan siswa</p>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="kelulusanChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Bar Chart (Additional) -->
        <div class="col-12 col-lg-6">
          <div class="card shadow-sm">
            <div class="card-header">
              <h5 class="card-title">Perbandingan Kelas</h5>
              <p class="card-text text-muted small">Distribusi kelulusan per kelas</p>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="kelasChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Chart.js Script -->
  <script>
    // Pie Chart
    const pieCtx = document.getElementById('kelulusanChart').getContext('2d');
    const kelulusanChart = new Chart(pieCtx, {
      type: 'doughnut',
      data: {
        labels: ['Lulus', 'Tidak Lulus'],
        datasets: [{
          data: [<?= $jumlah_lulus ?>, <?= $jumlah_tidak_lulus ?>],
          backgroundColor: ['#10b981', '#ef4444'],
          borderColor: ['#fff', '#fff'],
          borderWidth: 2,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              boxWidth: 10,
              padding: 20,
              font: {
                family: 'Poppins',
                size: 12
              }
            }
          },
          tooltip: {
            backgroundColor: '#1f2937',
            titleFont: {
              family: 'Poppins',
              size: 14
            },
            bodyFont: {
              family: 'Poppins',
              size: 12
            },
            padding: 12,
            cornerRadius: 8
          }
        },
        cutout: '70%'
      }
    });

    const kelasData = <?= json_encode($kelulusan_per_kelas) ?>;
    const kelasLabels = kelasData.map(item => item.kelas);
    const kelasLulus = kelasData.map(item => item.lulus);
    const kelasTidakLulus = kelasData.map(item => item.tidak_lulus);

    // Bar Chart Perbandingan Kelas
    const barCtx = document.getElementById('kelasChart').getContext('2d');
    const kelasChart = new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: kelasLabels,
        datasets: [
          {
            label: 'Lulus',
            data: kelasLulus,
            backgroundColor: '#10b981',
            borderRadius: 6
          },
          {
            label: 'Tidak Lulus',
            data: kelasTidakLulus,
            backgroundColor: '#ef4444',
            borderRadius: 6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              font: {
                family: 'Poppins'
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>
</html>
