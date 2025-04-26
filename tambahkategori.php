<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$activePage = 'tambahkategori';

require_once 'koneksi.php';

// Inisialisasi
$nama_kategori = "";
$errors = [];

// Proses Tambah Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_kategori = trim($_POST['nama_kategori']);

  // Validasi input
  if (empty($nama_kategori)) {
    $errors[] = "Nama kategori wajib diisi.";
  } elseif (strlen($nama_kategori) < 3) {
    $errors[] = "Nama kategori minimal 3 karakter.";
  } elseif (strlen($nama_kategori) > 30) {
    $errors[] = "Nama kategori maksimal 30 karakter.";
  } else {
    // Cek keunikan nama kategori
    $cek = $conn->prepare("SELECT id FROM kategori WHERE nama_kategori = ?");
    $cek->bind_param("s", $nama_kategori);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
      $errors[] = "Nama kategori sudah ada.";
    }
  }

  // Jika tidak ada error, simpan ke database
  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
    $stmt->bind_param("s", $nama_kategori);

    if ($stmt->execute()) {
      header("Location: datakategori.php?success=added");
      exit();
    } else {
      $errors[] = "Gagal menambahkan kategori: " . $conn->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <!-- Meta dan stylesheet sama persis dengan halaman data user -->
  <meta charset="UTF-8" />
  <title>Tambah User - Kedai Kito</title>
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
        <h5 class="card-title mb-4 fw-semibold">Tambah Kategori</h5>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger mb-3">
            <?php foreach ($errors as $e): ?>
              <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

        <form method="POST" novalidate>
          <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input
              type="text"
              class="form-control form-control-sm <?= !empty($errors) ? 'is-invalid' : '' ?>"
              name="nama_kategori"
              value="<?= htmlspecialchars($nama_kategori) ?>"
              placeholder="">
          </div>

          <div class="mt-4 d-flex gap-2 justify-content-end">
            <a href="datakategori.php" class="btn btn-secondary btn-sm">Batal</a>
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