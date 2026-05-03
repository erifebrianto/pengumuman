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
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <h4 class="card-title"><i class="fas fa-file-excel text-success"></i> Template Import</h4>
              <div class="card-category">Unduh dan isi template sebelum upload.</div>
            </div>
            <a href="<?= base_url('template/template_import.xlsx') ?>" class="btn btn-success btn-sm btn-round" download>
              <i class="fas fa-download"></i> Unduh
            </a>
          </div>
          <div class="card-body">
            <div class="alert alert-info">Buat file Excel (.xlsx) baru dengan nama header kolom (baris pertama) persis seperti "Isi" di bawah ini.</div>
            <p class="fw-bold mb-2">Urutan Kolom Wajib:</p>
            <table class="table table-sm table-bordered text-sm">
              <thead class="table-dark">
                <tr><th>Kolom</th><th>Isi</th></tr>
              </thead>
              <tbody>
                <tr><td>A</td><td>Nama Lengkap Siswa</td></tr>
                <tr><td>B</td><td>Nomor Induk Siswa</td></tr>
                <tr><td>C</td><td>NISN</td></tr>
                <tr><td>D</td><td>Kelas Siswa</td></tr>
                <tr><td>E</td><td>Nomor Ujian</td></tr>
                <tr><td>F</td><td>Tempat Lahir</td></tr>
                <tr><td>G</td><td>Tanggal Lahir</td></tr>
                <tr><td>H</td><td>Status Lulus</td></tr>
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
            <h4 class="card-title"><i class="fas fa-upload text-primary"></i> Upload File Excel / CSV</h4>
            <div class="card-category">Pilih file .xlsx atau .csv yang sudah diisi sesuai template.</div>
          </div>
          <div class="card-body d-flex flex-column justify-content-center">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <?= form_open_multipart('siswa/import') ?>
            <div class="form-group">
              <label for="file_excel" class="fw-bold">Pilih File Excel / CSV</label>
              <input type="file" name="file_excel" id="file_excel" class="form-control" accept=".xls,.xlsx,.csv" required>
              <small class="text-muted">Ukuran maksimal 2MB. Format: .xls, .xlsx, atau .csv</small>
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
