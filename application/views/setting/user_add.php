<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Tambah Administrator</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="<?= base_url('dashboard') ?>"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="<?= base_url('setting/users') ?>">User Management</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Tambah User</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Form Tambah Admin</div>
                    </div>
                    <form method="POST" action="<?= base_url('setting/user_add') ?>">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required placeholder="Masukkan Username">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required placeholder="Masukkan Password">
                            </div>
                        </div>
                        <div class="card-action">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                            <a href="<?= base_url('setting/users') ?>" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
