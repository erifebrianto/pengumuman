<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
  <div class="max-w-xl mx-auto bg-white rounded-xl shadow-md p-6">
    <form method="post">
      <div class="mb-4">
        <label for="waktu_target" class="block mb-1 font-medium">Waktu Target</label>
        <input type="datetime-local" name="waktu_target" id="waktu_target" class="w-full border px-4 py-2 rounded" required>
      </div>

      <div class="flex justify-between">
        <a href="<?= base_url('setting') ?>" class="text-gray-600 hover:underline">← Kembali</a>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
      </div>
    </form>

  </div>
</body>
</html>
