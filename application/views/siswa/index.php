<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Data Siswa</h3>
        <h6 class="op-7 mb-2">Daftar lengkap siswa dan status kelulusannya</h6>
      </div>
      <div class="ms-md-auto py-2 py-md-0">
        <a href="#" class="btn btn-label-info btn-round me-2">Export</a>
        <a href="#" class="btn btn-primary btn-round">Tambah Siswa</a>
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
                <th>NIS</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Dibuat Pada</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; foreach ($siswa as $row): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= $row->nama_lengkap ?></td>
                  <td><?= $row->nis ?></td>
                  <td><?= $row->kelas ?></td>
                  <td>
                    <span class="badge bg-<?= $row->status == 'Lulus' ? 'success' : 'danger' ?>">
                      <?= $row->status ?>
                    </span>
                  </td>
                  <td><?= date('d-m-Y H:i', strtotime($row->created_at)) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function () {
    $('#siswaTable').DataTable({
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Cari siswa..."
      }
    });
  });
</script>
