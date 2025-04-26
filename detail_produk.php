<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
$activePage = 'detail_produk';

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
    body {
      font-family: "Poppins", sans-serif;
      font-size: 0.85rem;
    }
  </style>
</head>

<body class="bg-light">
  <!-- Sidebar -->

  <?php include 'components/sidebar.php'; ?>

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