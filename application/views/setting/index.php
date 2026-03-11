<!-- Pengaturan Sekolah View -->
<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Pengaturan Sekolah</h3>
        <h6 class="op-7 mb-2">Formulir pengaturan informasi sekolah seperti nama, alamat, dan logo</h6>
      </div>
    </div>

    <?php if ($this->session->flashdata('success')) : ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?= form_open_multipart('setting'); ?>
    <div class="row">
      <!-- Main Information Form -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Informasi Utama</div>
          </div>
          <div class="card-body">
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Nama Sekolah <span class="text-danger">*</span></label>
                    <input type="text" name="nama_sekolah" class="form-control" value="<?= set_value('nama_sekolah', $pengaturan->nama_sekolah); ?>" placeholder="Masukkan Nama Sekolah" required>
                    <?= form_error('nama_sekolah', '<small class="text-danger">', '</small>'); ?>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Nama Kepala Sekolah</label>
                    <input type="text" name="nama_kepala_sekolah" class="form-control" value="<?= set_value('nama_kepala_sekolah', $pengaturan->nama_kepala_sekolah); ?>" placeholder="Nama Kepala Sekolah">
                </div>
              </div>
            </div>

            <div class="form-group mb-3">
                <label>Alamat Sekolah <span class="text-danger">*</span></label>
                <textarea name="alamat_sekolah" class="form-control" rows="3" placeholder="Alamat lengkap sekolah" required><?= set_value('alamat_sekolah', $pengaturan->alamat_sekolah); ?></textarea>
                <?= form_error('alamat_sekolah', '<small class="text-danger">', '</small>'); ?>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= set_value('email', $pengaturan->email); ?>" placeholder="email@sekolah.com" required>
                    <?= form_error('email', '<small class="text-danger">', '</small>'); ?>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>No Telepon</label>
                    <input type="text" name="no_tlp" class="form-control" value="<?= set_value('no_tlp', $pengaturan->no_tlp); ?>" placeholder="081xxx / 021xxx">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Kode Pos</label>
                    <input type="text" name="kode_pos" class="form-control" value="<?= set_value('kode_pos', $pengaturan->kode_pos); ?>" placeholder="Kode Pos">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Website</label>
                    <input type="text" name="website" class="form-control" value="<?= set_value('website', $pengaturan->website); ?>" placeholder="www.sekolah.sch.id">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group mb-3">
                    <label>Metode Verifikasi Login Siswa <span class="text-danger">*</span></label>
                    <select name="verification_method" class="form-control" required>
                        <option value="nisn" <?= $pengaturan->verification_method == 'nisn' ? 'selected' : ''; ?>>Metode 1: NISN Saja</option>
                        <option value="exam_number_nis" <?= $pengaturan->verification_method == 'exam_number_nis' ? 'selected' : ''; ?>>Metode 2: Nomor Ujian + NIS</option>
                        <option value="exam_number_dob" <?= $pengaturan->verification_method == 'exam_number_dob' ? 'selected' : ''; ?>>Metode 3: Nomor Ujian + Tanggal Lahir</option>
                        <option value="nisn_dob" <?= $pengaturan->verification_method == 'nisn_dob' ? 'selected' : ''; ?>>Metode 4: NISN + Tanggal Lahir</option>
                        <option value="exam_number" <?= $pengaturan->verification_method == 'exam_number' ? 'selected' : ''; ?>>Metode 5: Nomor Ujian Saja</option>
                    </select>
                    <small class="form-text text-muted">Tentukan kolom apa saja yang harus diisi siswa untuk mengecek kelulusan.</small>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group mb-3">
                    <label>Mode Tampilan Hasil <span class="text-danger">*</span></label>
                    <select name="mode_pengumuman" class="form-control" required>
                        <option value="nilai" <?= $pengaturan->mode_pengumuman == 'nilai' ? 'selected' : ''; ?>>Mode 1: Tampilkan Nilai</option>
                        <option value="status" <?= $pengaturan->mode_pengumuman == 'status' ? 'selected' : ''; ?>>Mode 2: Hanya Status Kelulusan</option>
                    </select>
                    <small class="form-text text-muted">Pilih apakah ingin menampilkan tabel nilai siswa atau hanya status lulus/tidak saja.</small>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Settings for Uploads -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Atribut Visual</div>
          </div>
          <div class="card-body">
            
            <div class="form-group mb-4">
                <label class="fw-bold">Logo Sekolah</label>
                <div class="mb-2 mt-2 text-center">
                  <?php if (!empty($pengaturan->logo_sekolah)) : ?>
                      <img src="<?= base_url($pengaturan->logo_sekolah); ?>" alt="Logo Sekolah" class="img-thumbnail" style="max-height: 120px;">
                  <?php else: ?>
                      <div class="p-4 border border-dashed rounded text-muted bg-light"><i class="fas fa-image mb-2" style="font-size: 24px;"></i><br>Belum ada logo</div>
                  <?php endif; ?>
                </div>
                <input type="file" name="logo_sekolah" class="form-control" accept="image/*">
                <small class="form-text text-muted">Format: JPG, JPEG, PNG. Max: 2MB.</small>
            </div>

            <hr>

            <div class="form-group mb-4">
                <label class="fw-bold">Background Halaman SKL</label>
                <div class="mb-2 mt-2 text-center">
                  <?php if (!empty($pengaturan->background)) : ?>
                      <img src="<?= base_url($pengaturan->background); ?>" alt="Background SKL" class="img-thumbnail" style="max-height: 120px;">
                  <?php else: ?>
                      <div class="p-4 border border-dashed rounded text-muted bg-light"><i class="fas fa-image mb-2" style="font-size: 24px;"></i><br>Belum ada background</div>
                  <?php endif; ?>
                </div>
                <input type="file" name="background" class="form-control" accept="image/*">
                <small class="form-text text-muted">Format: JPG, JPEG, PNG. Max: 2MB.</small>
            </div>

            <hr>

            <div class="form-group mb-4">
                <label class="fw-bold">TTD Kepala Sekolah</label>
                <div class="mb-2 mt-2 text-center">
                  <?php if (!empty($pengaturan->ttd_kepala_sekolah)) : ?>
                      <img src="<?= base_url($pengaturan->ttd_kepala_sekolah); ?>" alt="TTD Kepala Sekolah" class="img-thumbnail" style="max-height: 120px;">
                  <?php else: ?>
                      <div class="p-4 border border-dashed rounded text-muted bg-light"><i class="fas fa-signature mb-2" style="font-size: 24px;"></i><br>Belum ada TTD</div>
                  <?php endif; ?>
                </div>
                <input type="file" name="ttd_kepala_sekolah" class="form-control" accept="image/*">
                <small class="form-text text-muted">Akan ditampilkan pada SKL. Max: 2MB.</small>
            </div>

          </div>
          <div class="card-action text-end">
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan Pengaturan</button>
          </div>
        </div>
      </div>
    </div>
    <?= form_close(); ?>
  </div>
</div>
