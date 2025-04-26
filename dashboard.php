<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$activePage = 'dashboard';

require_once 'koneksi.php';

// Hitung jumlah produk
$stmt_produk = $conn->query("SELECT COUNT(*) FROM produk");
$jumlah_produk = $stmt_produk->fetch_row()[0];

// Hitung jumlah pesanan
$stmt_pesanan = $conn->query("SELECT COUNT(*) FROM pesanan");
$jumlah_pesanan = $stmt_pesanan->fetch_row()[0];

// Hitung total pendapatan hari ini berdasarkan created_at dan status "selesai"
$stmt_pendapatan = $conn->query("SELECT SUM(total) FROM pesanan WHERE DATE(created_at) = CURDATE() AND status = 'selesai'");
$pendapatan_hari_ini = $stmt_pendapatan->fetch_row()[0] ?: 0;

// Hitung total pendapatan bulan ini dengan status "selesai"
$stmt_pendapatan_bulan_ini = $conn->query("SELECT SUM(total) FROM pesanan WHERE MONTH(created_at) = MONTH(CURDATE()) AND status = 'selesai'");
$pendapatan_bulan_ini = $stmt_pendapatan_bulan_ini->fetch_row()[0] ?: 0;

// Hitung total pendapatan tahun ini dengan status "selesai"
$stmt_pendapatan_tahun_ini = $conn->query("SELECT SUM(total) FROM pesanan WHERE YEAR(created_at) = YEAR(CURDATE()) AND status = 'selesai'");
$pendapatan_tahun_ini = $stmt_pendapatan_tahun_ini->fetch_row()[0] ?: 0;


// Hitung jumlah pembayaran yang belum dibayar berdasarkan status "menunggu pembayaran"
$stmt_pembayaran_belum_dibayar = $conn->query("SELECT COUNT(*) FROM pesanan WHERE status = 'menunggu pembayaran'");
$jumlah_pembayaran_belum_dibayar = $stmt_pembayaran_belum_dibayar->fetch_row()[0];

// Ambil total pendapatan per bulan (tanpa memisahkan tahun)
$stmt_pendapatan_per_bulan = $conn->query("
  SELECT MONTH(created_at) AS bulan, SUM(total) AS pendapatan
  FROM pesanan
  WHERE status = 'selesai'
  GROUP BY MONTH(created_at)
  ORDER BY MONTH(created_at)
");
$pendapatan_per_bulan = array_fill(0, 12, 0); // Inisialisasi array dengan 0 untuk setiap bulan

// Mengisi array dengan pendapatan per bulan
while ($row = $stmt_pendapatan_per_bulan->fetch_assoc()) {
    $pendapatan_per_bulan[$row['bulan'] - 1] = $row['pendapatan']; // Simpan pendapatan pada bulan yang sesuai
}


?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Dashboard - Kedai Kito</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      font-size: 0.85rem;
    }

    .stat-card {
      width: 180px;
      height: 80px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
      font-size: 1.2rem;
      margin-right: 10px;
      color: #198754;
      padding: 8px;
      background: rgba(25, 135, 84, 0.1);
      border-radius: 6px;
    }

    .stat-text h6 {
      font-size: 0.65rem;
      font-weight: 500;
      color: #6c757d;
      margin-bottom: 2px;
    }

    .stat-text h3 {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 0;
    }

    .card-pesanan .stat-icon {
      color: #4a90e2;
      background: rgba(74, 144, 226, 0.1);
    }

    #revenueChart {
      max-height: 280px;
    }
  </style>
</head>

<body class="bg-light">


  <?php include 'components/sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content ms-220 py-3" style="margin-left: 220px">
    <!-- Topbar -->
    <div
      class="d-flex justify-content-end align-items-center bg-white rounded shadow-sm px-3 py-2 mb-3">
      <div class="text-end me-3">
        <strong><?= $_SESSION['nama'] ?></strong><br />
        <small class="text-muted"><?= $_SESSION['email'] ?></small>
      </div>
      <i class="bi bi-person-circle fs-4 text-secondary"></i>
    </div>

    <!-- Stat Cards -->
    <div class="d-flex flex-wrap gap-2 mb-3">

      <div class="card stat-card d-flex align-items-center px-3 py-2">
        <div class="d-flex align-items-center">
          <i class="bi bi-box-seam stat-icon"></i>
          <div class="stat-text">
            <h6>JUMLAH PRODUK</h6>
            <h3><?= $jumlah_produk ?></h3>
          </div>
        </div>
      </div>

      <div
        class="card stat-card card-pesanan d-flex align-items-center px-3 py-2">
        <div class="d-flex align-items-center">
          <i class="bi bi-receipt stat-icon"></i>
          <div class="stat-text">
            <h6>JUMLAH PESANAN</h6>
            <h3><?= $jumlah_pesanan ?></h3>
          </div>
        </div>
      </div>

      <div class="card stat-card d-flex align-items-center px-3 py-2">
        <div class="d-flex align-items-center">
          <i class="bi bi-cash-stack stat-icon"></i>
          <div class="stat-text">
            <h6>PENDAPATAN HARI INI</h6>
            <h3>Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></h3>
          </div>
        </div>
      </div>

      <div class="card stat-card d-flex align-items-center px-3 py-2">
        <div class="d-flex align-items-center">
          <i class="bi bi-cash-stack stat-icon"></i>
          <div class="stat-text">
            <h6>PENDAPATAN BULAN INI</h6>
            <h3>Rp <?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></h3>
          </div>
        </div>
      </div>

      <div class="card stat-card d-flex align-items-center px-3 py-2">
        <div class="d-flex align-items-center">
          <i class="bi bi-cash-stack stat-icon"></i>
          <div class="stat-text">
            <h6>PENDAPATAN TAHUN INI</h6>
            <h3>Rp <?= number_format($pendapatan_tahun_ini, 0, ',', '.') ?></h3>
          </div>
        </div>
      </div>

      <div class="card stat-card d-flex align-items-center px-3 py-2">
        <div class="d-flex align-items-center">
          <i class="bi bi-file-earmark-excel stat-icon"></i>
          <div class="stat-text">
            <h6>PEMBAYARAN BELUM DIBAYAR</h6>
            <h3><?= $jumlah_pembayaran_belum_dibayar ?></h3>
          </div>
        </div>
      </div>

    </div>

    <!-- Chart Card -->
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="text-center fw-semibold mb-3">
          Jumlah Pendapatan per Bulan
        </h6>
        <canvas id="revenueChart" height="120"></canvas>
      </div>
    </div> 
  </div>

  <script>
   // Data pendapatan per bulan dari PHP
   const pendapatanPerBulan = <?php echo json_encode($pendapatan_per_bulan); ?>;

const ctx = document.getElementById("revenueChart").getContext("2d");
new Chart(ctx, {
    type: "line",
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        datasets: [{
            label: "Jumlah Pendapatan per Bulan",
            data: pendapatanPerBulan,
            borderColor: "green",
            fill: false,
            tension: 0.3,
        }],
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
            },
        },
    },
});
  </script>
</body>

</html>