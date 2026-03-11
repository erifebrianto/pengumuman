<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Pengaturan Integrasi API</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="<?= base_url('dashboard') ?>">
            <i class="icon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">Pengaturan</a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">Wablas API</a>
        </li>
      </ul>
    </div>

    <div class="row">
      <!-- Kolom Setting -->
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">
            <h4 class="card-title">Konfigurasi Gateway WhatsApp (Wablas)</h4>
            <div class="card-category">Masukkan Domain dan Token aktif dari Dashboard Wablas Anda.</div>
          </div>
          <div class="card-body">
            
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success mt-2">
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if(validation_errors()): ?>
                <div class="alert alert-danger mt-2">
                    <?= validation_errors() ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('setting/wablas') ?>" method="post">
              <div class="form-group">
                <label for="wablas_domain">Domain Wablas URL</label>
                <input type="url" class="form-control" id="wablas_domain" name="wablas_domain" value="<?= set_value('wablas_domain', $pengaturan->wablas_domain) ?>" placeholder="Contoh: https://tegal.wablas.com" required>
                <small class="form-text text-muted">Abaikan slash (/) di akhir URL.</small>
              </div>
              
              <div class="form-group">
                <label for="wablas_token">API Token String</label>
                <input type="text" class="form-control" id="wablas_token" name="wablas_token" value="<?= set_value('wablas_token', $pengaturan->wablas_token) ?>" placeholder="Masukkan Token Wablas..." required>
                <small class="form-text text-muted">Didapatkan dari menu API di akun Wablas Anda.</small>
              </div>

              <div class="form-group">
                <label class="form-label d-block">Status Layanan Wablas API</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="wablas_status" id="statusAktif" value="1" <?= ($pengaturan->wablas_status == 1) ? 'checked' : '' ?>>
                  <label class="form-check-label text-success fw-bold" for="statusAktif">AKTIF</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="wablas_status" id="statusNonaktif" value="0" <?= ($pengaturan->wablas_status == 0) ? 'checked' : '' ?>>
                  <label class="form-check-label text-danger fw-bold" for="statusNonaktif">NONAKTIF</label>
                </div>
                <small class="form-text text-muted d-block mt-1">Jika dinonaktifkan, siswa tidak akan mendapatkan WA saat mengecek kelulusan.</small>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Limit Pesan per Batch (CLI/Cron)</label>
                    <input type="number" class="form-control" name="wa_batch_limit" value="<?= $pengaturan->wa_batch_limit ?>" required>
                    <small class="form-text text-muted">Jumlah maksimal pesan yang dikirim dalam satu kali eksekusi cron.</small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jeda Min (Detik)</label>
                    <input type="number" class="form-control" name="wa_delay_min" value="<?= $pengaturan->wa_delay_min ?>" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jeda Max (Detik)</label>
                    <input type="number" class="form-control" name="wa_delay_max" value="<?= $pengaturan->wa_delay_max ?>" required>
                  </div>
                </div>
                <div class="col-md-12">
                   <small class="form-text text-muted px-2">Jeda acak antar pesan untuk menghindari pemblokiran (spam filter).</small>
                </div>
              </div>

              <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Konfigurasi</button>
              </div>
            </form>

          </div>
        </div>
      </div>

      <!-- Kolom Test -->
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">
            <h4 class="card-title">Test Koneksi WhatsApp</h4>
            <div class="card-category">Kirim pesan percobaan untuk memastikan kredensial API telah valid.</div>
          </div>
          <div class="card-body">
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger mt-2">
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('setting/test_wablas') ?>" method="post">
              <div class="form-group">
                <label for="no_hp_test">Nomor Tujuan</label>
                <input type="text" class="form-control" id="no_hp_test" name="no_hp_test" placeholder="Contoh: 08123456789 (Tanpa + atau -)" required>
                <small class="form-text text-muted">Pastikan pengaturan API di sebelah kiri telah disimpan lebih dulu.</small>
              </div>

              <div class="form-group mt-3">
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Kirim Pesan Test</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Queue Status -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card border-primary">
          <div class="card-header bg-primary text-white">
            <h4 class="card-title text-white"><i class="fas fa-tasks"></i> Status Antrian WhatsApp</h4>
          </div>
          <div class="card-body">
            <div class="row text-center px-4">
              <div class="col-md-4 mb-3 mb-md-0">
                <div class="p-3 border rounded bg-light">
                  <h2 class="fw-bold text-warning mb-1"><?= $queue_count['pending'] ?></h2>
                  <span class="text-muted fw-bold">PENDING</span>
                  <div class="mt-2"><small>Menunggu di kirim oleh cronjob</small></div>
                </div>
              </div>
              <div class="col-md-4 mb-3 mb-md-0">
                <div class="p-3 border rounded bg-light">
                  <h2 class="fw-bold text-success mb-1"><?= $queue_count['sent'] ?></h2>
                  <span class="text-muted fw-bold">TERKIRIM</span>
                  <div class="mt-2"><small>Pesan berhasil diterima API</small></div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border rounded bg-light">
                  <h2 class="fw-bold text-danger mb-1"><?= $queue_count['failed'] ?></h2>
                  <span class="text-muted fw-bold">GAGAL</span>
                  <div class="mt-2"><small>Gagal setelah 3x percobaan</small></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Template Pesan -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Template Pesan WhatsApp</h4>
            <div class="card-category">
              Format pesan otomatis yang dikirim ke siswa. Gunakan variabel berikut:<br>
              <code>{NAMA_SISWA}</code> <code>{NIS}</code> <code>{KELAS}</code> <code>{LINK_DOWNLOAD}</code>
            </div>
          </div>
          <div class="card-body">
            
            <?php if($this->session->flashdata('success_template')): ?>
                <div class="alert alert-success mt-2">
                    <?= $this->session->flashdata('success_template') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('setting/wablas_template') ?>" method="post">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="wablas_template_lulus" class="fw-bold text-success"><i class="fas fa-check-circle"></i> Template Siswa LULUS</label>
                    <textarea class="form-control" id="wablas_template_lulus" name="wablas_template_lulus" rows="10" required><?= set_value('wablas_template_lulus', $pengaturan->wablas_template_lulus) ?></textarea>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="wablas_template_gagal" class="fw-bold text-danger"><i class="fas fa-times-circle"></i> Template Siswa TIDAK LULUS</label>
                    <textarea class="form-control" id="wablas_template_gagal" name="wablas_template_gagal" rows="10" required><?= set_value('wablas_template_gagal', $pengaturan->wablas_template_gagal) ?></textarea>
                  </div>
                </div>
              </div>

              <div class="form-group border-top pt-3 mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Template</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
