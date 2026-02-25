<div class="container">
    <h2>Preview Data Siswa</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>NIS</th>
                <th>NISN</th>
                <th>No HP (WA)</th>
                <th>Kelas</th>
                <th>Nama Orang Tua</th>
                <th>Rata-rata</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($preview as $row): ?>
            <tr>
                <td><?= $row['nama_lengkap'] ?></td>
                <td><?= $row['tempat_lahir'] ?></td>
                <td><?= $row['tanggal_lahir'] ?></td>
                <td><?= $row['nis'] ?></td>
                <td><?= $row['nisn'] ?></td>
                <td><?= $row['no_hp'] ?></td>
                <td><?= $row['kelas'] ?></td>
                <td><?= $row['nama_ortu'] ?></td>
                <td><?= $row['rata_rata'] ?></td>
                <td><?= $row['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="<?= site_url('siswa/do_import') ?>" method="post">
        <button type="submit" class="btn btn-success">Import Data</button>
    </form>
</div>
