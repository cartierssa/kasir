<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
$activePage = 'editpesanan';
require_once 'koneksi.php';

// Ambil ID pesanan dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: datapesanan.php");
  exit();
}

$pesanan_id = (int)$_GET['id'];

// Ambil data pesanan
$stmt_pesanan = $conn->prepare("
    SELECT p.*, u.nama AS nama_kasir 
    FROM pesanan p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt_pesanan->bind_param("i", $pesanan_id);
$stmt_pesanan->execute();
$pesanan = $stmt_pesanan->get_result()->fetch_assoc();

// Ambil detail pesanan
$stmt_detail = $conn->prepare("
    SELECT dp.*, pr.nama_produk 
    FROM detail_pesanan dp
    JOIN produk pr ON dp.produk_id = pr.id
    WHERE dp.pesanan_id = ?
");
$stmt_detail->bind_param("i", $pesanan_id);
$stmt_detail->execute();
$detail_pesanan = $stmt_detail->get_result()->fetch_all(MYSQLI_ASSOC);

// Hitung total harga
$total_harga = 0;
foreach ($detail_pesanan as $item) {
  $total_harga += $item['total_harga'];
}

// Proses update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = $_POST['status'];

  // Kalau status diubah jadi "berhasil", kurangi stok produk
  if ($status === 'selesai') {
    // Ambil detail pesanan lagi
    $stmt_detail = $conn->prepare("
      SELECT produk_id, jumlah 
      FROM detail_pesanan 
      WHERE pesanan_id = ?
    ");
    $stmt_detail->bind_param("i", $pesanan_id);
    $stmt_detail->execute();
    $result_detail = $stmt_detail->get_result();

    // Kurangi stok produk sesuai jumlah di detail pesanan
    while ($row = $result_detail->fetch_assoc()) {
      $stmt_update_stok = $conn->prepare("
        UPDATE produk 
        SET stok = stok - ? 
        WHERE id = ?
      ");
      $stmt_update_stok->bind_param("ii", $row['jumlah'], $row['produk_id']);
      $stmt_update_stok->execute();
    }
  }

  // Update status pesanan
  $stmt_update = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
  $stmt_update->bind_param("si", $status, $pesanan_id);

  if ($stmt_update->execute()) {
    header("Location: datapesanan.php?success=updated");
    exit();
  }
}


?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Data Produk - Kedai Kito</title>
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

    <!-- Data List Section -->
    <div class="card shadow-sm">
      <div class="card-body">
        <?php if (isset($_GET['success'])): ?>
          <?php if ($_GET['success'] == 'added'): ?>
            <div class="alert alert-success alert-dismissible fade show">
              Produk berhasil ditambahkan!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php elseif ($_GET['success'] == 'deleted'): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              Produk berhasil dihapus!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php elseif ($_GET['success'] == 'updated'): ?>
            <div class="alert alert-warning alert-dismissible fade show">
              Produk berhasil diperbarui!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <h5 class="card-title mb-4 fw-semibold">Edit Pesanan (<?= $pesanan['kode_invoice'] ?>)</h5>

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
                <<td>
                  <form method="POST">
                    <select name="status" class="form-select form-select-sm">
                      <option value="menunggu pembayaran" <?= $pesanan['status'] === 'menunggu pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                      <option value="selesai" <?= $pesanan['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                      <option value="dibatalkan" <?= $pesanan['status'] === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                    </td>
              </tr>
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
              <?php foreach ($detail_pesanan as $item): ?>
                <tr>
                  <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                  <td><?= $item['jumlah'] ?></td>
                  <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                  <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
              <tr class="table-light">
                <td colspan="2"></td>
                <td class="text-start fw-semibold">Total Harga</td>
                <!-- Kolom total harga -->
                <td class="fw-bold">Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Tombol Aksi -->
        <div class="mt-4 d-flex gap-2 justify-content-end">
          <button type="button" class="btn btn-secondary btn-sm" onclick="window.history.back()">Batal</button>
          <button type="submit" class="btn btn-success btn-sm">Simpan Perubahan</button>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>