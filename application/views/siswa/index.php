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
                <th>No HP (WA)</th>
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
                    <td><?= $row->no_hp ?></td>
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
            $('#generatePdfBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating ('+data.progress+'/'+data.total+')');
            $('#stopPdfBtn').show();
            setTimeout(checkProgress, 2000); // Polling every 2 secs
        } else if (data.status === 'completed') {
            $('#generatePdfBtn').prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Generate Pengumuman (Selesai ' + data.total + ')');
            $('#stopPdfBtn').hide();
            $('#stopPdfBtn').prop('disabled', false).html('<i class="fas fa-stop-circle"></i> Stop Generate');
        } else if (data.status === 'stopped') {
            $('#generatePdfBtn').prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Generate Pengumuman (Dihentikan)');
            $('#stopPdfBtn').hide();
            $('#stopPdfBtn').prop('disabled', false).html('<i class="fas fa-stop-circle"></i> Stop Generate');
        } else {
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
              $('#generatePdfBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memulai...');
              $('#stopPdfBtn').show();
              
              $.post('<?= base_url("siswa/trigger_pdf_batch") ?>', { mode: mode }, function(res) {
                  const data = JSON.parse(res);
                  if(data.status === 'success') {
                      checkProgress(); // Mulai polling
                  } else {
                      alert(data.message);
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

  // Check on load
  $(document).ready(function() {
      checkProgress();
  });
</script>
