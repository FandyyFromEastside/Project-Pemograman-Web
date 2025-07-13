<?php
session_start();
require_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Statistik umum
$totalUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$totalProduk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM produk"))['total'];
$totalPesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan"))['total'];
$totalPendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as total FROM pemesanan"))['total'] ?? 0;

// Statistik bulanan
$bulan = [];
$pendapatan = [];
for ($i = 1; $i <= 12; $i++) {
    $res = mysqli_query($conn, "
        SELECT SUM(total) as total_pendapatan
        FROM pemesanan
        WHERE MONTH(tanggal) = $i
    ");
    $data = mysqli_fetch_assoc($res);
    $bulan[] = date("M", mktime(0, 0, 0, $i, 10)); // Jan, Feb, dst
    $pendapatan[] = (int)$data['total_pendapatan'];
}

// Produk terlaris
$produk = [];
$jumlah = [];
$hasil = mysqli_query($conn, "
    SELECT p.nama_produk, SUM(dp.jumlah) as total_jual
    FROM detail_pemesanan dp
    JOIN produk p ON dp.id_produk = p.id
    GROUP BY dp.id_produk
    ORDER BY total_jual DESC
    LIMIT 5
");
while ($row = mysqli_fetch_assoc($hasil)) {
    $produk[] = $row['nama_produk'];
    $jumlah[] = (int)$row['total_jual'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - CENX Studios</title>
  <link rel="stylesheet" href="../asset/css/admin_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="dashboard-container">
    <header>
      <h1>Dashboard Admin</h1>
      <nav>
        <a href="tambah_produk.php">Tambah Produk</a>
        <a href="../admin/edit_produk.php">Edit Produk</a>
        <a href="../produk.php">Lihat Produk</a>
        <a href="produk_list.php">Produk List</a>
        <a href="admin_dashboard.php">Statistik</a>
        <a href="../auth/logout.php">Logout</a>
      </nav>
    </header>

    <div class="card-grid">
      <div class="card">
        <i class="fa-solid fa-users"></i>
        <h3>Total User</h3>
        <p><?= $totalUser ?></p>
      </div>
      <div class="card">
        <i class="fa-solid fa-boxes-stacked"></i>
        <h3>Total Produk</h3>
        <p><?= $totalProduk ?></p>
      </div>
      <div class="card">
        <i class="fa-solid fa-truck-fast"></i>
        <h3>Total Pesanan</h3>
        <p><?= $totalPesanan ?></p>
      </div>
      <div class="card">
        <i class="fa-solid fa-money-bill-wave"></i>
        <h3>Total Pendapatan</h3>
        <p>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
      </div>
    </div>

    <div class="chart-section">
      <h2>Grafik Statistik</h2>

      <canvas id="areaPendapatan" height="100"></canvas>
      <canvas id="produkTerlaris" height="120" style="margin-top:40px;"></canvas>
      <canvas id="donutData" height="100" style="margin-top:40px;"></canvas>
    </div>

    <footer>
      <p>&copy; <?= date('Y') ?> CENX Studios. All rights reserved.</p>
    </footer>
  </div>

  <script>
    const bulan = <?= json_encode($bulan) ?>;
    const pendapatan = <?= json_encode($pendapatan) ?>;
    const produk = <?= json_encode($produk) ?>;
    const jumlah = <?= json_encode($jumlah) ?>;

    new Chart(document.getElementById('areaPendapatan'), {
      type: 'line',
      data: {
        labels: bulan,
        datasets: [{
          label: 'Pendapatan per Bulan (Rp)',
          data: pendapatan,
          fill: true,
          backgroundColor: 'rgba(16,185,129,0.2)',
          borderColor: '#10b981',
          tension: 0.4
        }]
      },
      options: {
        plugins: {
          title: { display: true, text: 'Pendapatan Bulanan' }
        }
      }
    });

    new Chart(document.getElementById('produkTerlaris'), {
      type: 'bar',
      data: {
        labels: produk,
        datasets: [{
          label: 'Jumlah Terjual',
          data: jumlah,
          backgroundColor: '#3b82f6'
        }]
      },
      options: {
        indexAxis: 'y',
        plugins: {
          title: { display: true, text: 'Top 5 Produk Terlaris' }
        }
      }
    });

    new Chart(document.getElementById('donutData'), {
      type: 'doughnut',
      data: {
        labels: ['User', 'Produk', 'Pesanan'],
        datasets: [{
          data: [<?= $totalUser ?>, <?= $totalProduk ?>, <?= $totalPesanan ?>],
          backgroundColor: ['#f59e0b', '#6366f1', '#ef4444']
        }]
      },
      options: {
        plugins: {
          title: { display: true, text: 'Proporsi Data Sistem' }
        }
      }
    });
  </script>
</body>
</html>
