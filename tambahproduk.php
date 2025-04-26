<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
$activePage = 'tambahproduk';

require_once 'koneksi.php';

$nama_produk = $kategori_id = $harga = $stok = $deskripsi = "";
$errors = [];

// Ambil data kategori
$kategori_result = $conn->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_produk = trim($_POST['nama_produk']);
  $kategori_id = $_POST['kategori_id'];
  $harga = trim($_POST['harga']);
  $stok = trim($_POST['stok']);
  $deskripsi = trim($_POST['deskripsi']);

  // Validasi nama produk
  if (empty($nama_produk)) {
    $errors[] = "Nama produk wajib diisi.";
  } elseif (strlen($nama_produk) < 3) {
    $errors[] = "Nama produk minimal 3 karakter.";
  } elseif (strlen($nama_produk) > 50) {
    $errors[] = "Nama produk maksimal 50 karakter.";
  }

  // Validasi kategori
  if (empty($kategori_id)) {
    $errors[] = "Kategori wajib dipilih.";
  }

  // Validasi harga
  if (empty($harga)) {
    $errors[] = "Harga wajib diisi.";
  } elseif (!is_numeric($harga) || $harga < 1000) {
    $errors[] = "Harga minimal Rp 1.000.";
  }

  // Validasi stok
  if (empty($stok)) {
    $errors[] = "Stok wajib diisi.";
  } elseif (!ctype_digit($stok) || $stok < 20) {
    $errors[] = "Stok minimal 20.";
  }

  // Validasi gambar
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $file_name = $_FILES['gambar']['name'];
    $file_tmp = $_FILES['gambar']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed)) {
      $errors[] = "Format gambar harus jpg, jpeg, atau png.";
    }
  } else {
    $errors[] = "Gambar produk wajib diupload.";
  }

  // Jika valid
  if (empty($errors)) {
    $upload_dir = 'uploads/';
    $new_filename = uniqid() . '.' . $file_ext;
    move_uploaded_file($file_tmp, $upload_dir . $new_filename);

    $stmt = $conn->prepare("INSERT INTO produk (nama_produk, kategori_id, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiss", $nama_produk, $kategori_id, $harga, $stok, $deskripsi, $new_filename);

    if ($stmt->execute()) {
      header("Location: dataproduk.php?success=added");
      exit();
    } else {
      $errors[] = "Gagal menambahkan produk: " . $conn->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <!-- Meta dan stylesheet sama persis dengan halaman data user -->
  <meta charset="UTF-8" />
  <title>Tambah Produk - Kedai Kito</title>
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
        <h5 class="card-title mb-4 fw-semibold">Tambah Produk</h5>

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
              <input type="text" class="form-control form-control-sm" name="nama_produk" value="<?= htmlspecialchars($nama_produk) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kategori</label>
              <select class="form-select form-select-sm" name="kategori_id" required>
                <option value="">- Pilih Kategori -</option>
                <?php while ($row = $kategori_result->fetch_assoc()): ?>
                  <option value="<?= $row['id'] ?>" <?= $kategori_id == $row['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nama_kategori']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <!-- Harga -->
            <div class="col-md-6">
              <label class="form-label">Harga</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text">Rp</span>
                <input type="number" class="form-control" name="harga" value="<?= isset($harga) ? htmlspecialchars($harga) : 0 ?>" required>
              </div>
            </div>


            <div class="col-md-6 "> <!-- Tambah class mt-0 -->
              <label class="form-label">Stok</label>
              <input type="number" class="form-control form-control-sm" name="stok" value="<?= htmlspecialchars($stok) ?>" required>
            </div>


            <!-- Deskripsi -->
            <div class="col-md-6">
              <label class="form-label">Deskripsi</label>
              <textarea class="form-control form-control-sm" name="deskripsi" rows="2"><?= htmlspecialchars($deskripsi) ?></textarea>
            </div>

            <!-- Gambar Produk -->
            <div class="col-md-6 "> <!-- Tambah class mt-0 -->
              <label class="form-label">Gambar Produk</label>
              <input type="file" class="form-control form-control-sm" name="gambar" accept=".jpg, .jpeg, .png" required>
            </div>

          </div>
          <div class="mt-4 d-flex gap-2 justify-content-end">
            <button
              type="button"
              class="btn btn-secondary btn-sm"
              onclick="window.location.href='dataproduk.php'">
              Batal
            </button>
            <button type="submit" class="btn btn-success btn-sm">Tambah</button>
          </div>
    
        </form>
      </div>
    </div>
  </div>

  <!-- Script Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>