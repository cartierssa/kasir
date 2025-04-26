<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$activePage = 'datakategori';

require_once 'koneksi.php';

// Ambil ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: datakategori.php?success=updated");
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

// Proses Update
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_kategori = trim($_POST['nama_kategori']);

  // Validasi kosong
  if (empty($nama_kategori)) {
    $errors[] = "Nama kategori wajib diisi!";
  }

  // Validasi minimal karakter
  if (strlen($nama_kategori) < 3) {
    $errors[] = "Nama kategori minimal 3 karakter.";
  }

  // Validasi duplikasi (tidak boleh sama dengan kategori lain)
  $stmt_check = $conn->prepare("SELECT id FROM kategori WHERE nama_kategori = ? AND id != ?");
  $stmt_check->bind_param("si", $nama_kategori, $id);
  $stmt_check->execute();
  $result_check = $stmt_check->get_result();

  if ($result_check->num_rows > 0) {
    $errors[] = "Nama kategori sudah digunakan oleh kategori lain.";
  }

  // Jika tidak ada error, update
  if (empty($errors)) {
    $stmt = $conn->prepare("UPDATE kategori SET nama_kategori = ? WHERE id = ?");
    $stmt->bind_param("si", $nama_kategori, $id);

    if ($stmt->execute()) {
      header("Location: datakategori.php?success=updated");
      exit();
    } else {
      $errors[] = "Gagal memperbarui kategori: " . $conn->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Edit Kategori - Kedai Kito</title>
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
        <h5 class="card-title mb-4 fw-semibold">Edit Kategori</h5>

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
               class="form-control form-control-sm <?= (!empty($errors)) ? 'is-invalid' : '' ?>"
              name="nama_kategori"
              value="<?= htmlspecialchars($kategori['nama_kategori']) ?>"
              required>
          </div>
          <div class="mt-4 d-flex gap-2 justify-content-end">
            <a href="datakategori.php" class="btn btn-secondary btn-sm">Batal</a>
            <button type="submit" class="btn btn-success btn-sm">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
