<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

require_once 'koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: datapesanan.php");
  exit();
}

$id = (int) $_GET['id'];

// Ambil data pesanan + nama kasir (admin)
$stmt = $conn->prepare("
    SELECT p.*, u.nama AS admin 
    FROM pesanan p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: datapesanan.php");
  exit();
}

$pesanan = $result->fetch_assoc();

// Ambil detail item pesanan
$stmt_items = $conn->prepare("
    SELECT pi.*, pr.nama_produk 
    FROM detail_pesanan pi
    JOIN produk pr ON pi.produk_id = pr.id
    WHERE pi.pesanan_id = ?
");
$stmt_items->bind_param("i", $id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Pesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="main-content ms-220 py-3" style="margin-left: 220px">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-4 fw-semibold">Detail Pesanan: [<?= htmlspecialchars($pesanan['kode_invoice']) ?>]</h5>

        <div class="row mb-4">
          <div class="col-md-6">
            <table class="table table-bordered">
              <tr>
                <td><strong>Nama Kasir</strong></td>
                <td><?= htmlspecialchars($pesanan['admin']) ?></td>
              </tr>
              <tr>
                <td><strong>Nama Pelanggan</strong></td>
                <td><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></td>
              </tr>
              <tr>
                <td><strong>Status</strong></td>
                <td><?= htmlspecialchars($pesanan['status']) ?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="mb-4">
          <h6 class="fw-semibold">Detail Produk</h6>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                  <td><?= htmlspecialchars($item['jumlah']) ?></td>
                  <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                  <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <div class="row justify-content-end">
          <div class="col-md-4">
            <table class="table table-bordered">
              <tr>
                <td><strong>Total Harga</strong></td>
                <td>Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="mt-4">
          <a href="datapesanan.php" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
