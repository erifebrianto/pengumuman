<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Preview Data Siswa</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="<?= base_url('dashboard') ?>"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="<?= base_url('siswa') ?>">Data Siswa</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="<?= base_url('siswa/import') ?>">Import</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Preview</a></li>
      </ul>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title"><i class="fas fa-eye text-primary"></i> Data yang Akan Diimpor</div>
            <div class="card-category">Pastikan data berikut sudah benar sebelum disimpan.</div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-head-bg-primary table-bordered-bd-primary">
                <thead>
                    <tr>
                        <th>Nama Lengkap Siswa</th>
                        <th>Nomor Induk Siswa</th>
                        <th>NISN</th>
                        <th>Kelas Siswa</th>
                        <th>Nomor Ujian</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Status Lulus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preview as $row): ?>
                    <tr>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= $row['nis'] ?></td>
                        <td><?= $row['nisn'] ?></td>
                        <td><?= $row['kelas'] ?></td>
                        <td><?= $row['no_ujian'] ?></td>
                        <td><?= $row['tempat_lahir'] ?></td>
                        <td><?= !empty($row['tanggal_lahir']) ? date('d-m-Y', strtotime($row['tanggal_lahir'])) : ($row['raw_tanggal_lahir'] ?? 'Kosong/Gagal Parse') ?></td>
                        <td><?= $row['status'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-action">
            <div class="d-flex justify-content-end">
                <a href="<?= site_url('siswa/import') ?>" class="btn btn-danger me-2" style="margin-right: 10px;">
                    <i class="fas fa-times"></i> Batal
                </a>
                <form action="<?= site_url('siswa/do_import') ?>" method="post" style="display:inline;">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Import Data Sekarang
                    </button>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
