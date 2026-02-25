<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Import Data Siswa</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="<?= base_url('dashboard') ?>"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="<?= base_url('siswa') ?>">Data Siswa</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Import</a></li>
      </ul>
    </div>

    <div class="row">
      <!-- Kolom Panduan & Template -->
      <div class="col-md-5">
        <div class="card h-100">
          <div class="card-header">
            <h4 class="card-title"><i class="fas fa-file-excel text-success"></i> Template Import</h4>
            <div class="card-category">Unduh dan isi template sebelum upload.</div>
          </div>
          <div class="card-body">
            <a href="<?= base_url('assets/template_import_siswa.xlsx') ?>" class="btn btn-success btn-block mb-4" download>
              <i class="fas fa-download"></i> Unduh Template Excel
            </a>
            <p class="fw-bold mb-2">Urutan Kolom Wajib:</p>
            <table class="table table-sm table-bordered text-sm">
              <thead class="table-dark">
                <tr><th>Kolom</th><th>Isi</th></tr>
              </thead>
              <tbody>
                <tr><td>A</td><td>Nama Lengkap</td></tr>
                <tr><td>B</td><td>Tempat Lahir</td></tr>
                <tr><td>C</td><td>Tanggal Lahir (YYYY-MM-DD)</td></tr>
                <tr><td>D</td><td>NIS</td></tr>
                <tr><td>E</td><td>NISN</td></tr>
                <tr><td class="text-success fw-bold">F</td><td class="text-success fw-bold">No HP (WhatsApp)</td></tr>
                <tr><td>G</td><td>No Ujian</td></tr>
                <tr><td>H</td><td>Kelas</td></tr>
                <tr><td>I</td><td>Nama Orang Tua</td></tr>
                <tr><td>J</td><td>Rata-rata Nilai</td></tr>
                <tr><td>K</td><td>Status (Lulus / Tidak Lulus)</td></tr>
                <tr><td>L-dst</td><td>Nama Mapel, Nilai (pasangan kolom)</td></tr>
              </tbody>
            </table>
            <div class="alert alert-warning mt-2 p-2">
              <small><i class="fas fa-exclamation-triangle"></i> Baris pertama adalah <strong>header</strong>, data dimulai dari baris ke-2.</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Upload -->
      <div class="col-md-7">
        <div class="card h-100">
          <div class="card-header">
            <h4 class="card-title"><i class="fas fa-upload text-primary"></i> Upload File Excel</h4>
            <div class="card-category">Pilih file .xlsx yang sudah diisi sesuai template.</div>
          </div>
          <div class="card-body d-flex flex-column justify-content-center">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <?= form_open_multipart('siswa/import') ?>
            <div class="form-group">
              <label for="file_excel" class="fw-bold">Pilih File Excel</label>
              <input type="file" name="file_excel" id="file_excel" class="form-control" accept=".xls,.xlsx" required>
              <small class="text-muted">Ukuran maksimal 2MB. Format: .xls atau .xlsx</small>
            </div>
            <div class="form-group mt-3">
              <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-upload"></i> Upload &amp; Preview Data
              </button>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
