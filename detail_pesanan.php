<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
$activePage = 'detail_pesanan';

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
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      font-size: 0.85rem;
    }

    .user-table th {
      font-weight: 600;
      background-color: #f8f9fa;
      font-size: 0.8rem;
    }

    .user-table td {
      font-size: 0.8rem;

    }

    .search-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .search-container .input-group {
      width: 40%;
    }

    .search-container .input-group input {
      font-size: 0.85rem;

    }

    .search-container .form-control {
      border-radius: 6px;
    }

    .search-container .input-group button {
      border-radius: 6px;
      background-color: #198754;
      color: white;
      border: none;
      font-size: 0.85rem;
    }

    .search-container .input-group button i {
      font-size: 1.2rem;
    }

    .search-container .filter-container {
      display: flex;
      align-items: center;
    }

    .search-container .filter-container select {
      font-size: 0.85rem;

    }


    .filter-container {
      display: flex;
      justify-content: flex-start;
      margin-top: 10px;
    }

    .filter-container select {
      width: auto;
      border-radius: 6px;
      font-size: 0.85rem;
    }


    .pagination .page-link {
      color: #198754;

    }

    .pagination .page-link:hover {
      background-color: rgba(25, 135, 84, 0.1);

    }

    .pagination .page-item.active .page-link {
      background-color: #198754;

      border-color: #198754;
      color: #fff;
    }
  </style>
</head>
<body class="bg-light">
    <!-- Sidebar -->
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
    
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-4 fw-semibold">Detail Pesanan: [<?= htmlspecialchars($pesanan['kode_invoice']) ?>]</h5>

        <div class="table-responsive">
          <table class="table user-table table-hover align-middle">
            <tbody>
              <tr>
                <td class="fw-semibold" style="width: 30%">Nama Kasir</td>
                <td><?= htmlspecialchars($pesanan['nama_kasir'] ?? 'Guest') ?></td>
              </tr>
              <tr>
                <td class="fw-semibold" style="width: 30%">Nama Pelanggan</td>
                <td><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></td>
              </tr>
              <tr>
                <td class="fw-semibold">Status</td>
                <td><?= htmlspecialchars($pesanan['status']) ?></td>
            </tbody>
          </table>
        </div>

        <!-- Daftar Produk -->
        <div class="table-responsive">
          <table class="table user-table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nama Produk</th>
                <th style="width: 15%">Jumlah</th>
                <th style="width: 25%">Harga</th>
                <th style="width: 25%">Total Harga</th>
              </tr>
            </thead>
            <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                  <td><?= $item['jumlah'] ?></td>
                  <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                  <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
              <tr class="table-light">
                <!-- Gabung kolom 1 & 2 -->
                <td colspan="2"></td>
                <!-- Kolom harga -->
                <td class="text-start fw-semibold">Total Harga</td>
                <!-- Kolom total harga -->
                <td>Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4 d-flex gap-2 justify-content-end">
          <a href="datapesanan.php" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
        
      </div>
    </div>
  </div>
</body>
</html>
