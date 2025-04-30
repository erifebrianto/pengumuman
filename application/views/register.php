<h2>Register</h2>
<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata('success'); ?>
<form method="post">
    <input type="text" name="username" placeholder="Username" value="<?= set_value('username') ?>"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <input type="password" name="passconf" placeholder="Konfirmasi Password"><br>
    <button type="submit">Register</button>
</form>
<a href="<?= base_url('auth/login') ?>">Sudah punya akun?</a>
