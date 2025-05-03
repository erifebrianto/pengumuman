<!-- skl/result.php -->
<h2>Data Siswa</h2>
<p>Nama Lengkap: <?php echo $nama_lengkap; ?></p>
<p>NISN: <?php echo $nisn; ?></p>
<p>Alamat: <?php echo $tempat_lahir; ?></p>
<p>Status: <?php echo $status; ?></p>

<a href="<?php echo site_url('skl/download_skl/' . $nis); ?>">Download SKL</a>
