<div class="container">
    <h3>Cari Data Kelulusan</h3>
    <form method="post" action="<?= base_url('skl/result') ?>">
        <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS" required>
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger mt-2"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>
</div>