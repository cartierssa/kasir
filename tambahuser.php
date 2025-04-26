<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Tambah User - Kedai Kito</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <style>
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

      .sidebar .nav-item + .nav-item {
        margin-top: 6px;
      }

      .sidebar .nav-link {
        color: #2c3e50;
        padding: 8px 12px;
        border-radius: 6px;
        transition: background 0.2s ease;
        font-size: 0.85rem; /* Menyesuaikan ukuran teks pada sidebar */
        padding: 8px 10px;
      }

      .sidebar .nav-item + .nav-item {
        margin-top: 4px; /* Mengurangi jarak antar item */
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
      class="sidebar bg-white shadow-sm position-fixed top-0 start-0 h-100 p-3"
    >
      <div class="text-center mb-3">
        <h5>Kedai Hura</h5>
        <small class="text-muted">Aplikasi Point of Sale</small>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a
            class="nav-link active fw-bold bg-success bg-opacity-25 rounded"
            href="index"
            ><i class="bi bi-speedometer2 me-2"></i> Dashboard</a
          >
        </li>
        <li class="nav-item">
          <a class="nav-link" href="datauser.php"
            ><i class="bi bi-person-lines-fill me-2"></i> Data User</a
          >
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"
            ><i class="bi bi-tags-fill me-2"></i> Data Kategori</a
          >
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"
            ><i class="bi bi-box-seam me-2"></i> Data Produk</a
          >
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"
            ><i class="bi bi-card-list me-2"></i> Data Pesanan</a
          >
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"
            ><i class="bi bi-bag-check-fill me-2"></i> POS</a
          >
        </li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content ms-220 py-3" style="margin-left: 220px">
      <!-- Topbar (Sama persis) -->
      <div
        class="d-flex justify-content-end align-items-center bg-white rounded shadow-sm px-3 py-2 mb-3"
      >
        <div class="text-end me-3">
          <strong>Admin KedaiKito</strong><br />
          <small class="text-muted">admin@kedaikito.test</small>
        </div>
        <i class="bi bi-person-circle fs-4 text-secondary"></i>
      </div>

      <!-- Konten Utama Tambah User -->
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-4 fw-semibold">Tambah User</h5>
          
          <form>
            <div class="row g-3">
              <!-- Nama -->
              <div class="col-md-6">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control form-control-sm" ">
              </div>

              <!-- Email -->
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control form-control-sm" ">
              </div>

              <!-- Password -->
              <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" class="form-control form-control-sm" ">
              </div>

              <!-- Nomor Telepon -->
              <div class="col-md-6">
                <label class="form-label">Nomor Telepon</label>
                <input type="tel" class="form-control form-control-sm" ">
              </div>

              <!-- Role -->
              <div class="col-md-6">
                <label class="form-label">Role</label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="admin">
                    <label class="form-check-label" for="admin">Admin</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="karyawan">
                    <label class="form-check-label" for="karyawan">Karyawan</label>
                  </div>
                </div>
              </div>

              <!-- Status -->
              <div class="col-md-6">
                <label class="form-label">Status</label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="aktif">
                    <label class="form-check-label" for="aktif">Aktif</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="tidak-aktif">
                    <label class="form-check-label" for="tidak-aktif">Tidak Aktif</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 d-flex gap-2 justify-content-end">
              <button 
                type="button" 
                class="btn btn-secondary btn-sm"
                onclick="window.location.href='datauser.php'"
              >
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