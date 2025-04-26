<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
$activePage = 'detail_kategori';

require_once 'koneksi.php';

// Ambil ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: datakategori.php");
  exit();
}

$id = (int) $_GET['id'];

// Ambil data kategori dari database
$stmt = $conn->prepare("SELECT * FROM kategori WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: datakategori.php");
  exit();
}

$kategori = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Lihat Kategori - Kedai Kito</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
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

  <div class="main-content ms-220 py-3" style="margin-left: 220px">
    <div class="d-flex justify-content-end align-items-center bg-white rounded shadow-sm px-3 py-2 mb-3">
      <div class="text-end me-3">
        <strong><?= $_SESSION['nama'] ?></strong><br />
        <small class="text-muted"><?= $_SESSION['email'] ?></small>
      </div>
      <i class="bi bi-person-circle fs-4 text-secondary"></i>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-4 fw-semibold">Lihat Kategori</h5>

        <div class="mb-3">
          <label class="form-label">Nama Kategori</label>
          <input
            type="text"
            class="form-control form-control-sm"
            value="<?= htmlspecialchars($kategori['nama_kategori']) ?>"
            readonly>
        </div>
        
        <div class="mt-4 d-flex gap-2 justify-content-end">
        <a href="datakategori.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
