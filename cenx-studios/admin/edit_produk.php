<?php
session_start();
require_once '../includes/koneksi.php';

// Cek apakah user admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Pastikan ada parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: produk_list.php"); // Redirect jika tidak valid
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
$produk = mysqli_fetch_assoc($query);

// Cek jika produk tidak ditemukan
if (!$produk) {
    header("Location: produk_list.php");
    exit;
}

// Proses update produk
if (isset($_POST['update'])) {
    $nama = htmlspecialchars($_POST['nama_produk']);
    $harga = intval($_POST['harga']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        move_uploaded_file($tmp, '../uploads/' . $gambar);

        $sql = "UPDATE produk 
                SET nama_produk='$nama', harga=$harga, deskripsi='$deskripsi', gambar='$gambar' 
                WHERE id=$id";
    } else {
        $sql = "UPDATE produk 
                SET nama_produk='$nama', harga=$harga, deskripsi='$deskripsi' 
                WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: produk_list.php");
        exit;
    } else {
        $error = "Gagal mengupdate produk!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Produk</title>
  <link rel="stylesheet" href="../asset/css/edit_produk.css">
</head>
<body>
  <div class="form-container">
    <h2>Edit Produk</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
      <input type="number" name="harga" value="<?= htmlspecialchars($produk['harga']) ?>" required>
      <textarea name="deskripsi" required><?= htmlspecialchars($produk['deskripsi']) ?></textarea>

      <label>Gambar Saat Ini:</label><br>
      <img src="../uploads/<?= htmlspecialchars($produk['gambar']) ?>" width="120"><br><br>

      <input type="file" name="gambar">
      <button type="submit" name="update">Simpan Perubahan</button>
    </form>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  </div>
</body>
</html>
