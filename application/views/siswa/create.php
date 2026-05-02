<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Tambah Data Siswa</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="<?= base_url('dashboard') ?>"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="<?= base_url('siswa') ?>">Data Siswa</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Tambah Manual</a></li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Form Tambah Siswa (SKL)</div>
                    </div>
                    <form method="POST" action="<?= base_url('siswa/create') ?>">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Lengkap Siswa</label>
                                        <input type="text" name="nama_lengkap" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Nomor Induk Siswa (NIS)</label>
                                        <input type="text" name="nis" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>NISN</label>
                                        <input type="text" name="nisn" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Kelas Siswa</label>
                                        <input type="text" name="kelas" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nomor Ujian</label>
                                        <input type="text" name="no_ujian" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Status Lulus</label>
                                        <select name="status" class="form-control" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Lulus">Lulus</option>
                                            <option value="Tidak Lulus">Tidak Lulus</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
                            <a href="<?= base_url('siswa') ?>" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
