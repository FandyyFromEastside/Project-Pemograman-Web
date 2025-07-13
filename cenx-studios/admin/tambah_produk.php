<?php
session_start();
require_once '../includes/koneksi.php';

// ✅ Cek apakah user admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']); // Ambil dari form

    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];
    $path   = "../asset/images/" . $gambar;

    if (move_uploaded_file($tmp, $path)) {
        $insert = mysqli_query($conn, "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, kategori)
            VALUES ('$nama_produk', '$deskripsi', '$harga', '$stok', '$gambar', '$kategori')");

        if ($insert) {
            $success = "✅ Produk berhasil ditambahkan ke kategori <strong>$kategori</strong>!";
        } else {
            $error = "❌ Gagal menambahkan produk: " . mysqli_error($conn);
        }
    } else {
        $error = "❌ Upload gambar gagal!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Produk - Admin</title>
  <link rel="stylesheet" href="../asset/css/tambah_produk.css">
</head>
<body>

<nav>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="edit_produk.php">Edit Produk</a>
  <a href="../produk.php">Lihat Produk</a>
  <a href="produk_list.php">Produk List</a>
  <a href="admin_dashboard.php">Statistik</a>
  <a href="../auth/logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Tambah Produk Baru</h2>

  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <input type="text" name="nama_produk" placeholder="Nama Produk" required>
    <textarea name="deskripsi" placeholder="Deskripsi Produk" rows="4" required></textarea>
    <input type="number" name="harga" placeholder="Harga (angka saja)" required>
    <input type="number" name="stok" placeholder="Stok Produk" required>

    <!-- Dropdown kategori -->
    <select name="kategori" required>
      <option value="">-- Pilih Kategori --</option>
      <option value="umum">Umum</option>
      <option value="eksklusif">Eksklusif</option>
    </select>

    <input type="file" name="gambar" accept="image/*" required>
    <button type="submit" name="tambah">Tambah Produk</button>
  </form>
</div>

</body>
</html>
