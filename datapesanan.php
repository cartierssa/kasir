<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$activePage = 'datapesanan';

require_once 'koneksi.php';

// Ambil keyword search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Hitung total data pesanan
if (!empty($search)) {
  $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pesanan WHERE nama_pelanggan LIKE ?  OR kode_invoice LIKE ?");
  $like = "%$search%";
  $stmt->bind_param("ss", $like, $like);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  $total_data = $result['total'];
} else {
  $result = $conn->query("SELECT COUNT(*) as total FROM pesanan");
  $total_data = $result->fetch_assoc()['total'];
}
$total_pages = ceil($total_data / $per_page);

// Ambil data pesanan
if (!empty($search)) {
  $stmt = $conn->prepare("SELECT pesanan.*, users.nama AS nama_kasir 
    FROM pesanan 
    LEFT JOIN users ON pesanan.user_id = users.id 
    WHERE nama_pelanggan LIKE ? 
    OR pesanan.kode_invoice LIKE ? 
    ORDER BY pesanan.id DESC 
    LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $like, $like, $per_page, $offset);
} else {
  $stmt = $conn->prepare("SELECT pesanan.*, users.nama AS nama_kasir 
    FROM pesanan 
    LEFT JOIN users ON pesanan.user_id = users.id 
    ORDER BY pesanan.id DESC 
    LIMIT ? OFFSET ?");
  $stmt->bind_param("ii", $per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Data User - Kedai Kito</title>
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
      /* Menyesuaikan ukuran font pada cell tabel */
    }

    /* Styling untuk search bar dan tombol tambah user */
    .search-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    /* Menggunakan kelas Bootstrap untuk spacing dan ukuran elemen */
    .search-container .input-group {
      width: 40%;
    }

    .search-container .input-group input {
      font-size: 0.85rem;
      /* Mengurangi ukuran font pada input */
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
      /* Mengurangi ukuran font pada filter */
    }

    /* Styling untuk filter di bawah tabel */
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

    <!-- User List Section -->
    <div class="card shadow-sm">
      <div class="card-body">
        <?php if (isset($_GET['success'])): ?>
          <?php if ($_GET['success'] == 'added'): ?>
            <div class="alert alert-success">Pesanan berhasil ditambahkan!</div>
          <?php elseif ($_GET['success'] == 'deleted'): ?>
            <div class="alert alert-danger">Pesanan berhasil dihapus!</div>
          <?php elseif ($_GET['success'] == 'updated'): ?>
            <div class="alert alert-warning">Pesanan berhasil diperbarui!</div>
          <?php endif; ?>
        <?php endif; ?>

        <h5 class="card-title mb-4 fw-semibold">List Pesanan</h5>
        <div class="search-container">
          <form class="input-group" method="GET" action="">
            <input type="text" class="form-control form-control-sm me-2" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama pelanggan atau invoice ID">
            <button class="btn btn-success btn-sm" type="submit"><i class="bi bi-search"></i></button>
          </form>
          <a href="pos.php" class="btn btn-success btn-sm ms-3">
            <i class="bi bi-plus-circle me-2"></i>Tambah Pesanan
          </a>
        </div>

        <div class="table-responsive">
          <table class="table user-table table-hover">
            <thead>
              <tr>
                <th>Invoice ID</th>
                <th>Nama Kasir</th>
                <th>Nama Pelanggan</th>
                <th>Total Harga</th>
                <th>Tanggal Pesanan</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($pesanan) > 0): ?>
                <?php $no = $offset + 1;
                foreach ($pesanan as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['kode_invoice']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kasir'] ?? 'Guest') ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                    <td>Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                    <td>
                      <?php
                      $badge = 'secondary';
                      if ($row['status'] == 'menunggu pembayaran') $badge = 'warning';
                      elseif ($row['status'] == 'selesai') $badge = 'success';
                      elseif ($row['status'] == 'dibatalkan') $badge = 'danger';
                      ?>
                      <span class="badge bg-<?= $badge ?>"><?= $row['status'] ?></span>
                    </td>
                    <td>
                      <a href="detail_pesanan.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                      <a href="editpesanan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>

                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center">Data pesanan tidak ditemukan.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Filter dan Pagination di pojok kanan bawah -->
        <div class="d-flex justify-content-end align-items-center gap-3 mt-3 flex-wrap">

          <!-- Filter dropdown -->
          <form method="GET" class="d-flex align-items-center gap-2">
            <input type="hidden" name="search" value="<?= htmlspecialchars($search ?? '') ?>">
            <label for="rowsPerPage" class="form-label me-2 mb-0">Tampilkan</label>
            <select id="rowsPerPage" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
              <option <?= $per_page == 5 ? 'selected' : '' ?> value="5">5</option>
              <option <?= $per_page == 10 ? 'selected' : '' ?> value="10">10</option>
              <option <?= $per_page == 20 ? 'selected' : '' ?> value="20">20</option>
              <option <?= $per_page == 50 ? 'selected' : '' ?> value="50">50</option>
            </select>
          </form>

          <!-- Pagination -->
          <?php if ($total_pages > 1): ?>
            <nav>
              <ul class="list-unstyled d-flex gap-3 mb-0">
                <!-- Tombol prev -->
                <li class="<?= $page <= 1 ? 'text-muted' : '' ?>">
                  <?php if ($page > 1): ?>
                    <a href="?per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>" class="text-success text-decoration-none">
                      <i class="bi bi-chevron-left fs-5"></i>
                    </a>
                  <?php else: ?>
                    <i class="bi bi-chevron-left fs-5"></i>
                  <?php endif; ?>
                </li>

                <!-- Tombol next -->
                <li class="<?= $page >= $total_pages ? 'text-muted' : '' ?>">
                  <?php if ($page < $total_pages): ?>
                    <a href="?per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>" class="text-success text-decoration-none">
                      <i class="bi bi-chevron-right fs-5"></i>
                    </a>
                  <?php else: ?>
                    <i class="bi bi-chevron-right fs-5"></i>
                  <?php endif; ?>
                </li>
              </ul>
            </nav>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>

  <script>
    // Auto dismiss alert setelah 2 detik
    setTimeout(() => {
      const alert = document.querySelector('.alert');
      if (alert) {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
      }

      // Hapus parameter 'success' dari URL
      if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url.pathname);
      }
    }, 2000);
  </script>
</body>

</html>