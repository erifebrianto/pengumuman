<div class="container">
    <div class="page-inner">
        <h3>Form Tambah Siswa dan Nilai</h3>
        <form method="POST" action="<?= base_url('siswa/create') ?>">
            <div class="row">
                <div class="col-md-6">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>

                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" required>

                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" required>

                    <label>NIS</label>
                    <input type="text" name="nis" class="form-control" required>

                    <label>NISN</label>
                    <input type="text" name="nisn" class="form-control" required>

                    <label>No HP (WhatsApp)</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 0812xx" required>
                </div>
                <div class="col-md-6">
                    <label>No Ujian</label>
                    <input type="text" name="no_ujian" class="form-control" required>

                    <label>Kelas</label>
                    <input type="text" name="kelas" class="form-control" required>

                    <label>Nama Orang Tua</label>
                    <input type="text" name="nama_ortu" class="form-control" required>

                    <label>Rata-rata</label>
                    <input type="number" step="0.01" name="rata_rata" class="form-control">

                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="lulus">Lulus</option>
                        <option value="tidak lulus">Tidak Lulus</option>
                    </select>
                </div>
            </div>

            <hr>

            <label>Pilih Jurusan</label>
            <select id="jurusan_select" class="form-control mb-3">
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusan as $j): ?>
                    <option value="<?= $j->id ?>"><?= $j->jurusan ?></option>
                <?php endforeach; ?>
            </select>

            <div id="form_mapel_nilai">
                <!-- Mapel dan Nilai akan muncul di sini -->
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
</div>

<script>
document.getElementById('jurusan_select').addEventListener('change', function() {
    let jurusan_id = this.value;
    let container = document.getElementById('form_mapel_nilai');
    container.innerHTML = 'Loading...';

    fetch('<?= base_url("siswa/get_mapel_by_jurusan") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'jurusan_id=' + jurusan_id
    })
    .then(res => res.json())
    .then(data => {
        let html = '<h5>Nilai Mata Pelajaran</h5>';
        data.forEach(function(item) {
            html += `
                <div class="form-group">
                    <label>${item.nama_mata_pelajaran}</label>
                    <input type="number" name="nilai[${item.id}]" class="form-control" required>
                </div>
            `;
        });
        container.innerHTML = html;
    })
    .catch(err => {
        container.innerHTML = 'Gagal mengambil data mata pelajaran.';
        console.error(err);
    });
});
</script>
