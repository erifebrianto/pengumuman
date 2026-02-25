<!DOCTYPE html>
<html>
<head>
  <title>Pengaturan Countdown</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h3>Pengaturan Waktu Countdown</h3>
    
    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('settings/update') ?>" method="post" class="mt-4">
      <div class="mb-3">
        <label for="waktu_target" class="form-label">Tanggal & Waktu Target</label>
        <input 
          type="datetime-local" 
          name="waktu_target" 
          id="waktu_target" 
          class="form-control" 
          required
          value="<?= isset($countdown->waktu_target) ? date('Y-m-d\TH:i', strtotime($countdown->waktu_target)) : '' ?>"
        >
      </div>
      <button type="submit" class="btn btn-primary">Simpan Countdown</button>
    </form>
  </div>
</body>
</html>
