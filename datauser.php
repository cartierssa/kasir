<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$activePage = 'datauser';

require_once 'koneksi.php';
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

    .card-pesanan .stat-icon {
      color: #4a90e2;
      background: rgba(74, 144, 226, 0.1);
    }

    #revenueChart {
      max-height: 280px;
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
        <h5 class="card-title mb-4 fw-semibold">List User</h5>
        <div class="search-container">
          <div class="input-group">
            <input
              type="text"
              class="form-control form-control-sm me-2"
              id="searchInput"
              placeholder="Cari berdasarkan nama" />
            <button class="btn btn-success btn-sm">
              <i class="bi bi-search"></i>
            </button>
          </div>
          <a href="tambahuser.php" class="btn btn-success btn-sm ms-3">
            <i class="bi bi-plus-circle me-2"></i>Tambah User
          </a>
        </div>

        <!-- Filter di bawah tabel -->
        <div class="filter-container">
          <select id="rowsPerPage" class="form-select form-select-sm w-auto">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>

        <div class="table-responsive">
          <table class="table user-table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  <i class="bi bi-exclamation-circle me-2"></i>
                  Tidak ada data
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>

</html>