<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="assets/css/sidebar.css" rel="stylesheet">

</head>

<body>
  <!-- Sidebar -->
  <div
    class="sidebar bg-white shadow-sm position-fixed top-0 start-0 h-100 p-3">
    <div class="text-center mb-3">
      <h5>Kedai Kito</h5>
      <small class="text-muted">Aplikasi Point of Sale</small>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?= ($activePage == 'dashboard') ? 'active fw-bold bg-success bg-opacity-25 rounded' : '' ?>" href="dashboard.php">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($activePage == 'datauser') ? 'active fw-bold bg-success bg-opacity-25 rounded' : '' ?>" href="datauser.php">
          <i class="bi bi-person-lines-fill me-2"></i> Data User
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($activePage == 'datakategori') ? 'active fw-bold bg-success bg-opacity-25 rounded' : '' ?>" href="datakategori.php">
          <i class="bi bi-tags-fill me-2"></i> Data Kategori
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($activePage == 'dataproduk') ? 'active fw-bold bg-success bg-opacity-25 rounded' : '' ?>" href="dataproduk.php">
          <i class="bi bi-box-seam me-2"></i> Data Produk
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($activePage == 'datapesanan') ? 'active fw-bold bg-success bg-opacity-25 rounded' : '' ?>" href="datapesanan.php">
          <i class="bi bi-card-list me-2"></i> Data Pesanan
        </a>
      </li>
      <a class="nav-link <?= ($activePage == 'pos') ? 'active fw-bold bg-success bg-opacity-25 rounded' : '' ?>" href="pos.php">
        <i class="bi bi-bag-check-fill me-2"></i> POS
      </a>
      <li class="nav-item mt-auto"> <!-- mt-auto untuk mendorong ke bawah -->
        <a class="nav-link text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bi bi-box-arrow-left me-2"></i> Logout
        </a>
      </li>

    </ul>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!-- Tambahin class ini -->
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: none;">
          <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body" style="border-bottom: none;">
          Yakin ingin logout?
        </div>
        <div class="modal-footer" style="border-top: none;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
      </div>
    </div>
  </div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>