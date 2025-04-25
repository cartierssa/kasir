<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

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

    .sidebar {
      width: 200px;
      z-index: 1000;
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
      <li class="nav-item mt-auto"> <!-- mt-auto untuk mendorong ke bawah -->
        <a class="nav-link text-danger" href="logout.php">
          <i class="bi bi-box-arrow-left me-2"></i> Logout
        </a>
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