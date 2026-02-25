<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Edit Mata Pelajaran</h3>
                <h6 class="op-7 mb-2">Formulir untuk mengubah data mata pelajaran</h6>
            </div>
        </div>

        <form method="post" action="<?= base_url('mata_pelajaran/update/' . $mata_pelajaran->id) ?>">
            <div class="mb-3">
                <label for="jurusan_id" class="form-label">Jurusan</label>
                <select name="jurusan_id" class="form-control" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($jurusan as $j) : ?>
                        <option value="<?= $j->id ?>" <?= $j->id == $mata_pelajaran->jurusan_id ? 'selected' : '' ?>>
                            <?= $j->jurusan ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nama_mata_pelajaran" class="form-label">Nama Mata Pelajaran</label>
                <input type="text" name="nama_mata_pelajaran" class="form-control" value="<?= $mata_pelajaran->nama_mata_pelajaran ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="<?= base_url('mata_pelajaran') ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
