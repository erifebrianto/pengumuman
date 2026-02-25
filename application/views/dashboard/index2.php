<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kelulusan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
  </style>
</head>
<body class="bg-gray-50">
<div class="container">
  <div class="page-inner">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Kelulusan</h1>
        <p class="text-gray-600 mt-1">Statistik Kelulusan Siswa Tahun Ajaran <?= date('Y') ?></p>
      </div>

    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Total Siswa Card -->
      <div class="card-gradient-primary rounded-xl shadow-lg overflow-hidden hover-scale">
        <div class="p-6 flex items-center">
          <div class="glass-effect p-4 rounded-full mr-4">
            <i class="bi bi-people-fill text-white text-2xl"></i>
          </div>
          <div>
            <p class="text-blue-100 font-medium">Total Siswa</p>
            <h3 class="text-white text-3xl font-bold"><?= $total_siswa ?></h3>
          </div>
        </div>
        <div class="px-6 pb-4 pt-2 bg-white bg-opacity-10">
          <p class="text-blue-100 text-sm flex items-center">
            <span class="inline-block w-2 h-2 rounded-full bg-blue-200 mr-2"></span>
            Seluruh siswa yang terdaftar
          </p>
        </div>
      </div>
      
      <!-- Lulus Card -->
      <div class="card-gradient-success rounded-xl shadow-lg overflow-hidden hover-scale">
        <div class="p-6 flex items-center">
          <div class="glass-effect p-4 rounded-full mr-4">
            <i class="bi bi-check-circle-fill text-white text-2xl"></i>
          </div>
          <div>
            <p class="text-green-100 font-medium">Jumlah Lulus</p>
            <h3 class="text-white text-3xl font-bold"><?= $jumlah_lulus ?></h3>
          </div>
        </div>
        <div class="px-6 pb-4 pt-2 bg-white bg-opacity-10">
          <p class="text-green-100 text-sm flex items-center">
            <span class="inline-block w-2 h-2 rounded-full bg-green-200 mr-2"></span>
            <?= round(($jumlah_lulus/$total_siswa)*100, 2) ?>% dari total siswa
          </p>
        </div>
      </div>
      
      <!-- Tidak Lulus Card -->
      <div class="card-gradient-danger rounded-xl shadow-lg overflow-hidden hover-scale">
        <div class="p-6 flex items-center">
          <div class="glass-effect p-4 rounded-full mr-4">
            <i class="bi bi-x-circle-fill text-white text-2xl"></i>
          </div>
          <div>
            <p class="text-red-100 font-medium">Tidak Lulus</p>
            <h3 class="text-white text-3xl font-bold"><?= $jumlah_tidak_lulus ?></h3>
          </div>
        </div>
        <div class="px-6 pb-4 pt-2 bg-white bg-opacity-10">
          <p class="text-red-100 text-sm flex items-center">
            <span class="inline-block w-2 h-2 rounded-full bg-red-200 mr-2"></span>
            <?= round(($jumlah_tidak_lulus/$total_siswa)*100, 2) ?>% dari total siswa
          </p>
        </div>
      </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Pie Chart -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-semibold text-gray-800">Diagram Kelulusan</h3>
          <p class="text-sm text-gray-500">Persentase kelulusan siswa</p>
        </div>
        <div class="p-6">
          <div class="chart-container">
            <canvas id="kelulusanChart"></canvas>
          </div>
        </div>
      </div>
      
      <!-- Bar Chart (Additional) -->
   <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-semibold text-gray-800">Perbandingan Kelas</h3>
          <p class="text-sm text-gray-500">Distribusi kelulusan per kelas</p>
      </div>
      <div class="p-6">
          <div class="chart-container">
              <canvas id="kelasChart"></canvas>
          </div>
      </div>
  </div>
    </div>
  </div>
</div>
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