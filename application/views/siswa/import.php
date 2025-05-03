<div class="container">
    <h2>Import Data Siswa</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <?= form_open_multipart('siswa/import') ?>
    <input type="file" name="file_excel" accept=".xls,.xlsx" required>
    <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>
