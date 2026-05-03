<!-- Siswa Index View -->
<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Data Siswa</h3>
        <h6 class="op-7 mb-2">Daftar lengkap siswa dan status kelulusannya</h6>
      </div>
      <div class="ms-md-auto py-2 py-md-0 d-flex gap-2">
        <button id="generatePdfBtn" onclick="generatePdfBatch()" class="btn btn-warning btn-round"><i class="fas fa-file-pdf"></i> Generate Pengumuman Batch</button>
        <button id="stopPdfBtn" onclick="stopPdfBatch()" class="btn btn-danger btn-round" style="display: none;"><i class="fas fa-stop-circle"></i> Stop Generate</button>
        <a href="<?php echo base_url();?>/siswa/create" class="btn btn-primary btn-round">Tambah Siswa</a>
        <a href="<?php echo base_url();?>/siswa/import" class="btn btn-success btn-round">Import</a>
        <button onclick="confirmBulkDelete()" class="btn btn-danger btn-round"><i class="fas fa-trash-alt"></i> Hapus Semua Data</button>
        <a href="<?php echo base_url('skl/logs');?>" class="btn btn-secondary btn-round" target="_blank"><i class="fas fa-file-alt"></i> Lihat Log Proses</a>
      </div>
    </div>

    <style>
    @keyframes pulseGlow {
      0% { box-shadow: 0 0 5px rgba(255, 152, 0, 0.4); }
      50% { box-shadow: 0 0 20px rgba(255, 152, 0, 0.7); }
      100% { box-shadow: 0 0 5px rgba(255, 152, 0, 0.4); }
    }
    .pulse-glow-warning {
      animation: pulseGlow 2s ease-in-out infinite;
      border-left: 5px solid #ff9800 !important;
    }
    </style>

    <!-- Progress Bar Section for Generating PDF Batch -->
    <div id="progressSection" class="card mb-4 pulse-glow-warning" style="display: none; background-color: #fffef0;">
      <div class="card-body">
        <div class="alert alert-warning mb-3 d-flex align-items-center" role="alert">
          <i class="fas fa-exclamation-triangle me-2 fs-4"></i>
          <div>
            <strong>Mohon ditunggu!</strong> Proses generate dokumen PDF sedang berlangsung di latar belakang.
          </div>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="fw-bold"><i class="fas fa-spinner fa-spin me-2"></i>Sedang Memproses Dokumen PDF...</span>
          <span id="progressText" class="fw-bold">0%</span>
        </div>
        <div class="progress" style="height: 22px; border-radius: 11px;">
          <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning text-dark fw-bold" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
        <small class="text-muted d-block mt-2" id="progressSubtext">Proses sedang berjalan di background. Mohon tunggu hingga selesai. <a href="<?php echo base_url('skl/logs');?>" class="text-secondary fw-bold text-decoration-underline ms-1" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Detail Log</a></small>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="siswaTable" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NO Ujian</th>
                <th>NIS</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Dibuat Pada</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($siswa)): ?>
                <?php $no = 1; foreach ($siswa as $row): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row->nama_lengkap ?></td>
                    <td><?= $row->no_ujian ?></td>
                    <td><?= $row->nis ?></td>
                    <td><?= $row->nisn ?></td>
                    <td><?= $row->kelas ?></td>
                    <td>
                      <span class="badge bg-<?= $row->status == 'Lulus' ? 'success' : 'danger' ?>">
                        <?= ucfirst($row->status) ?>
                      </span>
                    </td>
                    <td><?= date('d-m-Y H:i', strtotime($row->created_at)) ?></td>
                    <td>
                      <button class="btn btn-sm btn-info" onclick="showDetail(<?= $row->id ?>)">Detail</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center">Tidak ada data siswa</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Siswa -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailLabel">Detail Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-2">
          <div class="col-md-4 fw-bold">Nama Lengkap</div>
          <div class="col-md-8" id="detail_nama">-</div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 fw-bold">Kelas</div>
          <div class="col-md-8" id="detail_kelas">-</div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 fw-bold">Status</div>
          <div class="col-md-8" id="detail_status">-</div>
        </div>
        <div class="row">
          <div class="col-md-4 fw-bold">Nilai</div>
          <div class="col-md-8">
            <ul id="detail_nilai" class="list-group list-group-flush"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- DataTables, Bootstrap & SweetAlert2 Scripts -->
<link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function () {
    <?php if ($this->session->flashdata('import_success')): ?>
    Swal.fire({
      icon: 'success',
      title: 'Import Selesai',
      text: '<?= $this->session->flashdata('import_success'); ?>',
      timer: 5000,
      showConfirmButton: true
    });
    <?php endif; ?>

    <?php if ($this->session->flashdata('delete_success')): ?>
    Swal.fire({
      icon: 'success',
      title: 'Penghapusan Berhasil',
      text: '<?= $this->session->flashdata('delete_success'); ?>',
      timer: 5000,
      showConfirmButton: true
    });
    <?php endif; ?>

    $('#siswaTable').DataTable({
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Cari siswa..."
      }
    });
  });

  function showDetail(id) {
    fetch('<?= base_url("siswa/detail/") ?>' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('detail_nama').innerText = data.siswa.nama_lengkap;
        document.getElementById('detail_kelas').innerText = data.siswa.kelas;
        document.getElementById('detail_status').innerText = data.siswa.status;

        const nilaiList = document.getElementById('detail_nilai');
        nilaiList.innerHTML = '';

        if (data.nilai.length > 0) {
          data.nilai.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = `${item.nama_mata_pelajaran}: ${item.nilai}`;
            nilaiList.appendChild(li);
          });
        } else {
          const li = document.createElement('li');
          li.className = 'list-group-item text-muted';
          li.textContent = 'Belum ada nilai.';
          nilaiList.appendChild(li);
        }

        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();
      })
      .catch(error => {
        console.error('Gagal memuat data detail:', error);
        alert('Terjadi kesalahan saat mengambil data.');
      });
  }

  // Polling Generate Status Server-Side
  function checkProgress() {
    $.getJSON('<?= base_url("siswa/check_pdf_progress") ?>', function(data) {
        if (data.status === 'processing') {
            const percent = data.total > 0 ? Math.round((data.progress / data.total) * 100) : 0;
            $('#progressSection').show();
            $('#progressText').text(percent + '%');
            $('#progressBar').css('width', percent + '%').attr('aria-valuenow', percent).text(percent + '%');
            $('#progressSubtext').html('Memproses ' + data.progress + ' dari ' + data.total + ' siswa. Mohon tunggu...');

            $('#generatePdfBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating ('+data.progress+'/'+data.total+')');
            $('#stopPdfBtn').show();
            setTimeout(checkProgress, 2000); // Polling every 2 secs
        } else if (data.status === 'completed') {
            $('#progressSection').hide();
            $('#generatePdfBtn').prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Generate Pengumuman (Selesai ' + data.total + ')');
            $('#stopPdfBtn').hide();
            $('#stopPdfBtn').prop('disabled', false).html('<i class="fas fa-stop-circle"></i> Stop Generate');
        } else if (data.status === 'stopped') {
            $('#progressSection').hide();
            $('#generatePdfBtn').prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Generate Pengumuman (Dihentikan)');
            $('#stopPdfBtn').hide();
            $('#stopPdfBtn').prop('disabled', false).html('<i class="fas fa-stop-circle"></i> Stop Generate');
        } else {
            $('#progressSection').hide();
            $('#generatePdfBtn').prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Generate Pengumuman Batch');
            $('#stopPdfBtn').hide();
            $('#stopPdfBtn').prop('disabled', false).html('<i class="fas fa-stop-circle"></i> Stop Generate');
        }
    });
  }

  // Trigger CLI Batch Generate
  function generatePdfBatch() {
      Swal.fire({
          title: 'Generate Pengumuman PDF',
          text: "Pilih mode pemrosesan batch:",
          icon: 'question',
          showCancelButton: true,
          showDenyButton: true,
          confirmButtonColor: '#3085d6',
          denyButtonColor: '#ffc107',
          cancelButtonColor: '#d33',
          confirmButtonText: '<i class="fas fa-forward"></i> Skip Data Lama (Cepat)',
          denyButtonText: '<i class="fas fa-trash-alt"></i> Ulangi dari Awal (Hapus Hasil)',
          cancelButtonText: 'Batal'
      }).then((result) => {
          if (result.isConfirmed || result.isDenied) {
              const mode = result.isConfirmed ? 'skip' : 'overwrite';
              
              // Tampilkan progressSection secara instan agar ada visual feedback
              $('#progressSection').show();
              $('#progressText').text('0%');
              $('#progressBar').css('width', '0%').attr('aria-valuenow', 0).text('0%');
              $('#progressSubtext').html('Memulai pemrosesan dokumen... Silakan tunggu beberapa detik.');

              $('#generatePdfBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memulai...');
              $('#stopPdfBtn').show();
              
              $.post('<?= base_url("siswa/trigger_pdf_batch") ?>', { mode: mode }, function(res) {
                  const data = JSON.parse(res);
                  if(data.status === 'success') {
                      Swal.fire({
                          title: 'Berhasil!',
                          text: 'Proses generate telah berhasil dimulai di background.',
                          icon: 'success',
                          timer: 2000,
                          showConfirmButton: false
                      });
                      checkProgress(); // Mulai polling
                  } else {
                      alert(data.message);
                      $('#progressSection').hide();
                      $('#generatePdfBtn').prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Generate Pengumuman Batch');
                      $('#stopPdfBtn').hide();
                  }
              });
          }
      });
  }

  function stopPdfBatch() {
      if(confirm('Apakah Anda yakin ingin menghentikan proses generate dokumen yang sedang berjalan?')) {
          $('#stopPdfBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghentikan...');
          $.post('<?= base_url("siswa/stop_pdf_batch") ?>', function(res) {
              const data = JSON.parse(res);
              if(data.status === 'success') {
                  // Berhasil kirim command, biarkan polling checkProgress yang update tulisan
              } else {
                  alert(data.message);
                  $('#stopPdfBtn').prop('disabled', false).html('<i class="fas fa-stop-circle"></i> Stop Generate');
              }
          });
      }
  }

  function confirmBulkDelete() {
      Swal.fire({
          title: 'Hapus Semua Data?',
          text: "Anda akan menghapus SELURUH data siswa beserta nilainya. Aksi ini tidak dapat dibatalkan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, Hapus Semua!',
          cancelButtonText: 'Batal'
      }).then((result) => {
          if (result.isConfirmed) {
              window.location.href = "<?= base_url('siswa/delete_all') ?>";
          }
      });
  }

  // Check on load
  $(document).ready(function() {
      checkProgress();
  });
</script>
