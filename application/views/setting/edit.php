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
  <div class="max-w-xl mx-auto bg-white rounded-xl shadow-md p-6">
    <form method="post">
      <div class="mb-4">
        <label for="waktu_target" class="block mb-1 font-medium">Waktu Target</label>
        <input type="datetime-local" name="waktu_target" id="waktu_target" class="w-full border px-4 py-2 rounded" required
               value="<?= date('Y-m-d\TH:i', strtotime($countdown->waktu_target)) ?>">
      </div>

      <div class="flex justify-between">
        <a href="<?= base_url('setting/countdowns') ?>" class="text-gray-600 hover:underline">← Kembali</a>
        <button type="submit" class="btn btn-success btn-round">Update</button>
      </div>
    </form>

  </div>
</div>
</div>
</div>
</div>