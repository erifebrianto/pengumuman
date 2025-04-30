<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Daftar Mata Pelajaran</h3>
                <h6 class="op-7 mb-2">Berikut adalah daftar mata pelajaran berdasarkan jurusan</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <a href="<?= base_url('mata_pelajaran/create') ?>" class="btn btn-primary btn-round">Tambah Mata Pelajaran</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Jurusan</th>
                        <th>Nama Mata Pelajaran</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($mata_pelajaran)) : ?>
                        <?php foreach ($mata_pelajaran as $index => $mp) : ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $mp->jurusan ?></td>
                                <td><?= $mp->nama_mata_pelajaran ?></td>
                                <td>
                                    <a href="<?= base_url('mata_pelajaran/edit/' . $mp->id) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?= base_url('mata_pelajaran/delete/' . $mp->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data mata pelajaran.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
