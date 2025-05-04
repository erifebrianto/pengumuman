<!-- Pengaturan Sekolah View -->
<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Pengaturan Sekolah</h3>
        <h6 class="op-7 mb-2">Formulir pengaturan informasi sekolah seperti nama, alamat, dan logo</h6>
      </div>
      <!-- Jika ada tombol aksi tambahan, letakkan di sini -->
    </div>

    <?php if ($this->session->flashdata('success')) : ?>
      <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <?= form_open_multipart('setting'); ?>
    
    <div class="form-group mb-3">
        <label>Nama Sekolah</label>
        <input type="text" name="nama_sekolah" class="form-control" value="<?= set_value('nama_sekolah', $pengaturan->nama_sekolah); ?>">
        <?= form_error('nama_sekolah'); ?>
    </div>

    <div class="form-group mb-3">
        <label>Alamat Sekolah</label>
        <input type="text" name="alamat_sekolah" class="form-control" value="<?= set_value('alamat_sekolah', $pengaturan->alamat_sekolah); ?>">
        <?= form_error('alamat_sekolah'); ?>
    </div>

    <div class="form-group mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= set_value('email', $pengaturan->email); ?>">
        <?= form_error('email'); ?>
    </div>

    <div class="form-group mb-3">
        <label>Kode Pos</label>
        <input type="text" name="kode_pos" class="form-control" value="<?= set_value('kode_pos', $pengaturan->kode_pos); ?>">
    </div>

    <div class="form-group mb-3">
        <label>No Telepon</label>
        <input type="text" name="no_tlp" class="form-control" value="<?= set_value('no_tlp', $pengaturan->no_tlp); ?>">
    </div>

    <div class="form-group mb-3">
        <label>Website</label>
        <input type="text" name="website" class="form-control" value="<?= set_value('website', $pengaturan->website); ?>">
    </div>

    <div class="form-group mb-3">
        <label>Nama Kepala Sekolah</label>
        <input type="text" name="nama_kepala_sekolah" class="form-control" value="<?= set_value('nama_kepala_sekolah', $pengaturan->nama_kepala_sekolah); ?>">
    </div>

    <div class="form-group mb-3">
        <label>Logo Sekolah</label><br>
        <?php if (!empty($pengaturan->logo_sekolah)) : ?>
            <img src="<?= base_url($pengaturan->logo_sekolah); ?>" alt="Logo Sekolah" style="max-height: 100px;"><br>
        <?php endif; ?>
        <input type="file" name="logo_sekolah" class="form-control-file mt-2">
    </div>

    <div class="form-group mb-4">
        <label>Background Halaman SKL</label><br>
        <?php if (!empty($pengaturan->background)) : ?>
            <img src="<?= base_url($pengaturan->background); ?>" alt="Background SKL" style="max-height: 100px;"><br>
        <?php endif; ?>
        <input type="file" name="background" class="form-control-file mt-2">
    </div>


    <div class="form-group mb-4">
        <label>TTD Kepala Sekolah</label><br>
        <?php if (!empty($pengaturan->ttd_kepala_sekolah)) : ?>
            <img src="<?= base_url($pengaturan->ttd_kepala_sekolah); ?>" alt="TTD Kepala Sekolah" style="max-height: 100px;"><br>
        <?php endif; ?>
        <input type="file" name="ttd_kepala_sekolah" class="form-control-file mt-2">
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <?= form_close(); ?>
  </div>
</div>
