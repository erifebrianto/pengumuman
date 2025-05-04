<!-- Siswa Index View -->
<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Coutdown</h3>
        <h6 class="op-7 mb-2">Setting Count Down</h6>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
          <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6">
            <?php if ($this->session->flashdata('success')): ?>
              <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?= $this->session->flashdata('success') ?>
              </div>
            <?php endif; ?>

            <table id="siswaTable" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="border px-4 py-2">#</th>
                  <th class="border px-4 py-2">Waktu Target</th>
                  <th class="border px-4 py-2">Dibuat Pada</th>
                  <th class="border px-4 py-2">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($countdowns as $i => $c): ?>
                <tr class="text-center">
                  <td class="border px-4 py-2"><?= $i + 1 ?></td>
                  <td class="border px-4 py-2"><?= date('d-m-Y H:i:s', strtotime($c->waktu_target)) ?></td>
                  <td class="border px-4 py-2"><?= date('d-m-Y H:i:s', strtotime($c->created_at)) ?></td>
                  <td class="border px-4 py-2 space-x-2">
                    <a href="<?= base_url('setting/edit/'.$c->id) ?>" class="text-blue-600 hover:underline">Edit</a>
                    <a href="<?= base_url('setting/delete/'.$c->id) ?>" onclick="return confirm('Yakin ingin hapus?')" class="text-red-600 hover:underline">Hapus</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- DataTables & Bootstrap Scripts -->
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