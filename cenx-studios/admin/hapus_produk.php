<?php
session_start();
require_once '../includes/koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Pastikan ada ID produk di URL
if (!isset($_GET['id'])) {
    echo "ID produk tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Cek apakah produk ada
$cek = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
if (mysqli_num_rows($cek) == 0) {
    echo "Produk tidak ditemukan.";
    exit;
}

// Hapus produk
$hapus = mysqli_query($conn, "DELETE FROM produk WHERE id = $id");

if ($hapus) {
    header("Location: admin_dashboard.php"); // Bisa arahkan ke produk_list.php kalau sudah punya
    exit;
} else {
    echo "Gagal menghapus produk.";
}
?>
