<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
require_once 'koneksi.php';

// Ambil ID produk dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: dataproduk.php");
  exit();
}

$id = (int) $_GET['id'];

// Ambil data produk
$stmt = $conn->prepare("SELECT produk.*, kategori.nama_kategori FROM produk JOIN kategori ON produk.kategori_id = kategori.id WHERE produk.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: dataproduk.php");
  exit();
}

$produk = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <!-- Meta dan stylesheet sama persis dengan halaman data user -->
  <meta charset="UTF-8" />
  <title>Detail Produk - Kedai Kito</title>
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
  <style>
    /* CSS sama persis dengan halaman data user */
    body {
      font-family: "Poppins", sans-serif;
      font-size: 0.85rem;
    }

    .sidebar {
      width: 200px;
      z-index: 1000;
    }

    .sidebar .nav-item+.nav-item {
      margin-top: 6px;
    }

    .sidebar .nav-link {
      color: #2c3e50;
      padding: 8px 12px;
      border-radius: 6px;
      transition: background 0.2s ease;
      font-size: 0.85rem;
      /* Menyesuaikan ukuran teks pada sidebar */
      padding: 8px 10px;
    }

    .sidebar .nav-item+.nav-item {
      margin-top: 4px;
      /* Mengurangi jarak antar item */
    }

    .sidebar .nav-link:hover {
      background-color: rgba(25, 135, 84, 0.1);
      /* hijau muda */
      color: #198754;
    }

    .sidebar .nav-link.active {
      background-color: rgba(25, 135, 84, 0.2);
      /* aktif = lebih kuat */
      color: #198754;
      font-weight: 600;
    }

    /* ... (Salin semua style dari halaman data user di sini) ... */
  </style>
</head>

<body class="bg-light">
  <!-- Sidebar (Sama persis) -->
  <div
    class="sidebar bg-white shadow-sm position-fixed top-0 start-0 h-100 p-3">
    <div class="text-center mb-3">
      <h5>Kedai Kito</h5>
      <small class="text-muted">Aplikasi Point of Sale</small>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a
          class="nav-link active fw-bold bg-success bg-opacity-25 rounded"
          href="index"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="datauser.php"><i class="bi bi-person-lines-fill me-2"></i> Data User</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#"><i class="bi bi-tags-fill me-2"></i> Data Kategori</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#"><i class="bi bi-box-seam me-2"></i> Data Produk</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#"><i class="bi bi-card-list me-2"></i> Data Pesanan</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#"><i class="bi bi-bag-check-fill me-2"></i> POS</a>
      </li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content ms-220 py-3" style="margin-left: 220px">
    <!-- Topbar (Sama persis) -->
    <div
      class="d-flex justify-content-end align-items-center bg-white rounded shadow-sm px-3 py-2 mb-3">
      <div class="text-end me-3">
        <strong><?= $_SESSION['nama'] ?></strong><br />
        <small class="text-muted"><?= $_SESSION['email'] ?></small>
      </div>
      <i class="bi bi-person-circle fs-4 text-secondary"></i>
    </div>

    <!-- Konten Utama Tambah User -->
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-4 fw-semibold">Detail Produk</h5>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger mb-3">
            <?= htmlspecialchars($errors[0]) ?>
          </div>
        <?php endif; ?>


        <form method="POST" enctype="multipart/form-data" novalidate>
          <div class="row g-3">
            <!-- Nama Produk -->
            <div class="col-md-6">
              <label class="form-label">Nama Produk</label>
              <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($produk['nama_produk']) ?>" readonly>
            </div>

            <!-- Kategori -->
            <div class="col-md-6">
              <label class="form-label">Kategori</label>
              <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($produk['nama_kategori']) ?>" readonly>
            </div>

            <!-- Harga -->
            <div class="col-md-6">
              <label class="form-label">Harga</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control form-control-sm" value="<?= number_format($produk['harga'], 0, ',', '.') ?>" readonly>
              </div>
            </div>


            <!-- Stok -->
            <div class="col-md-6">
              <label class="form-label">Stok</label>
              <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($produk['stok']) ?>" readonly>
            </div>

            <!-- Gambar Produk -->
            <div class="col-md-6">
              <label class="form-label">Gambar Produk</label>
              <!-- Preview gambar -->
              <div class="mt-2">
                <img src="uploads/<?= htmlspecialchars($produk['gambar']) ?>" alt="Gambar Produk" class="img-thumbnail" style="max-width: 250px;">
              </div>
            </div>

            <!-- Deskripsi -->
            <div class="col-md-6 d-flex flex-column">
              <label class="form-label">Deskripsi</label>
              <textarea class="form-control form-control-sm" rows="3" readonly><?= htmlspecialchars($produk['deskripsi']) ?></textarea>

              <div class="mt-auto text-end mt-3">
                <a href="dataproduk.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
              </div>
            </div>

          </div>


        </form>

      </div>
    </div>
  </div>

  <!-- Script Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Preview gambar otomatis saat pilih file
    document.getElementById('gambarInput').addEventListener('change', function(e) {
      const preview = document.getElementById('previewGambar');
      const file = e.target.files[0];

      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          preview.src = event.target.result;
          preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
      } else {
        preview.src = '';
        preview.style.display = 'none';
      }
    });
  </script>

</body>

</html>