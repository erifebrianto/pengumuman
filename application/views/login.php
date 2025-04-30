<h2>Login</h2>
<?php echo $this->session->flashdata('error'); ?>
<form method="post">
    <input type="text" name="username" placeholder="Username" value="<?= set_value('username') ?>"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit">Login</button>
</form>
<a href="<?= base_url('auth/register') ?>">Belum punya akun?</a>
