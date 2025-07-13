<?php
session_start();
require_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Produk - Admin</title>
  <link rel="stylesheet" href="../asset/css/produk_list.css">
</head>
<body>
  <div class="container">
    <h1>Kelola Produk</h1>
    <a href="tambah_produk.php" class="btn-tambah">+ Tambah Produk</a>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Gambar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($produk)): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['nama_produk']) ?></td>
          <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
          <td><img src="../asset/images/<?= $row['gambar'] ?>" width="60"></td>
          <td>
            <a href="edit_produk.php?id=<?= $row['id'] ?>">✏️ Edit</a> |
            <a href="hapus_produk.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus produk ini?')">🗑️ Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
