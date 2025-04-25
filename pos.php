<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$activePage = 'pos';

require_once 'koneksi.php';

// Ambil data produk dan kategori dari database
$stmt = $conn->prepare("
  SELECT produk.*, kategori.nama_kategori 
  FROM produk 
  LEFT JOIN kategori ON produk.kategori_id = kategori.id
");
$stmt->execute();
$produk = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Ambil daftar kategori untuk filter
$kategori = $conn->query("SELECT * FROM kategori")->fetch_all(MYSQLI_ASSOC);
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      font-size: 0.85rem;
    }

    .user-table th {
      font-weight: 600;
      background-color: #f8f9fa;
      font-size: 0.8rem;
    }

    .user-table td {
      font-size: 0.8rem;
      /* Menyesuaikan ukuran font pada cell tabel */
    }

    /* Styling untuk search bar dan tombol tambah user */
    .search-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    /* Menggunakan kelas Bootstrap untuk spacing dan ukuran elemen */
    .search-container .input-group {
      width: 40%;
    }

    .search-container .input-group input {
      font-size: 0.85rem;
      /* Mengurangi ukuran font pada input */
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
      /* Mengurangi ukuran font pada filter */
    }

    /* Tambahan CSS Khusus */
    .product-card {
      border: 1px solid #e9ecef;
      border-radius: 12px;
      overflow: hidden;
      position: relative;
      transition: all 0.3s;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .product-img {
      height: 150px;
      object-fit: cover;
    }

    .add-button {
      position: absolute;
      bottom: 10px;
      right: 10px;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: #198754;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .filter-btn.active {
      background: #198754 !important;
      color: white !important;
      border-color: #198754 !important;
    }

    #keranjangKosong {
      border: 1px dashed #ccc;
      margin-top: 20px;
      margin-bottom: 20px;
    }

    #alertContainer {
      position: absolute;
      top: 1rem;
      right: 1rem;
      z-index: 1050;
      width: 300px;
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
        <strong>Admin KedaiKito</strong><br />
        <small class="text-muted">admin@kedaikito.test</small>
      </div>
      <i class="bi bi-person-circle fs-4 text-secondary"></i>
    </div>

    <div class="row g-4">
      <!-- Kolom Kiri -->
      <div class="col-md-8">
        <!-- Search dan Filter -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <div class="search-container">
              <div class="input-group">
                <input
                  type="text"
                  class="form-control form-control-sm"
                  id="searchInput"
                  placeholder="Cari berdasarkan nama produk" />
                <button class="btn btn-success btn-sm">
                  <i class="bi bi-search"></i>
                </button>
              </div>

            </div>

            <div class="d-flex gap-2 mt-2 mb-2">
              <button class="btn btn-outline-success btn-sm filter-btn active" data-kategori="all">Semua</button>
              <?php foreach ($kategori as $kat): ?>
                <button class="btn btn-outline-success btn-sm filter-btn" data-kategori="<?= $kat['id'] ?>">
                  <?= $kat['nama_kategori'] ?>
                </button>
              <?php endforeach; ?>
            </div>

            <!-- Daftar Produk Dinamis -->
            <div class="row g-3 produk-list">
              <?php foreach ($produk as $p): ?>
                <div class="col-md-4 produk-item" data-kategori="<?= $p['kategori_id'] ?>">
                  <div class="product-card">
                    <?php if ($p['gambar']): ?>
                      <img src="uploads/<?= $p['gambar'] ?>" class="product-img w-100">
                    <?php else: ?>
                      <div class="product-img w-100 bg-secondary"></div>
                    <?php endif; ?>
                    <div class="p-3">
                      <h6 class="mb-1 fw-bold"><?= $p['nama_produk'] ?></h6>
                      <small class="text-muted">Rp <?= number_format($p['harga'], 0, ',', '.') ?></small><br>
                      <small class="text-muted">Stok: <?= $p['stok'] ?></small>
                    </div>

                    <?php if ($p['stok'] > 0): ?>
                      <div class="add-button" data-produk='<?= json_encode($p) ?>'>
                        <i class="bi bi-plus"></i>
                      </div>
                    <?php else: ?>
                      <div class="add-button disabled text-muted text-center py-2">Habis</div>
                    <?php endif; ?>
                  </div>

                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan - Keranjang -->
      <div class="col-md-4">
        <div id="alertContainer"></div>
        <div class="card shadow-sm sticky-top" style="top: 1rem;">
          <div class="card-body">

            <h5 class="fw-bold mb-4">Keranjang</h5>
            <form id="formPesanan" action="proses_pesanan.php" method="POST">
              <input type="hidden" name="total" id="inputTotal">
              <div id="keranjangItems" class="mb-3">
                <div id="keranjangKosong" class="text-center bg-light py-5 rounded text-muted">
                  <p class="m-0">Keranjang masih kosong</p>
                </div>
              </div>
              <div class="mb-0">
                <input type="text" name="nama_pelanggan" id="namaPelanggan" class="form-control" placeholder="Nama Pelanggan" required>
              </div>
              <div class="border-top pt-3">
                <div class="d-flex justify-content-between mb-2">
                  <span class="fw-bold">Total:</span>
                  <span class="fw-bold" id="totalHarga">Rp 0</span>
                </div>
                <button type="submit" class="btn btn-success w-100 mb-2" onclick="showConfirmationModal()">
                  Pesan Sekarang
                </button>
                <button type="button" class="btn btn-outline-success w-100" onclick="clearCart()">
                  Kosongkan Keranjang
                </button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Buat Pesanan</h5>
        </div>
        <div class="modal-body">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Total harga</th>
              </tr>
            </thead>
            <tbody id="modalItems"></tbody>
          </table>
          <div class="mt-3">
            <div class="d-flex justify-content-between">
              <strong>Nama Pelanggan:</strong>
              <span id="modalPelanggan"></span>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <strong>Total Harga:</strong>
              <strong id="modalTotal"></strong>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-success" onclick="submitOrder()">Konfirmasi Pesanan</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let keranjang = [];

    function formatRupiah(angka) {
      return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function hitungTotal() {
      return keranjang.reduce((acc, item) => acc + item.harga * item.qty, 0);
    }

    function renderHiddenInputs(cartItems) {
      const form = document.getElementById('formPesanan');

      // Hapus input lama
      const inputsLama = form.querySelectorAll('.item-hidden');
      inputsLama.forEach(input => input.remove());

      cartItems.forEach((item, index) => {
        const subtotal = item.harga * item.qty;

        form.insertAdjacentHTML('beforeend', `
      <input type="hidden" class="item-hidden" name="items[${index}][produk_id]" value="${item.id}">
      <input type="hidden" class="item-hidden" name="items[${index}][qty]" value="${item.qty}">
      <input type="hidden" class="item-hidden" name="items[${index}][harga]" value="${item.harga}">
      <input type="hidden" class="item-hidden" name="items[${index}][subtotal]" value="${subtotal}">
    `);
      });

      // Set total harga di input tersembunyi
      const total = cartItems.reduce((sum, item) => sum + item.harga * item.qty, 0);
      document.getElementById('inputTotal').value = total;
    }


    function updateKeranjang() {
      let container = $('#keranjangItems');
      let total = hitungTotal();
      container.empty();

      if (keranjang.length === 0) {
        container.html(`
      <div id="keranjangKosong" class="text-center bg-light py-5 rounded text-muted">
        <p class="m-0">Keranjang masih kosong</p>
      </div>
    `);
      } else {
        keranjang.forEach((item, index) => {
          const subtotal = item.harga * item.qty;

          container.append(`
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <strong>${item.nama_produk}</strong><br>
            <small>${item.qty} x ${formatRupiah(item.harga)}</small>
          </div>
          <div>
            <span>${formatRupiah(subtotal)}</span>
            <button type="button" class="btn btn-sm btn-danger ms-2 btn-hapus" data-index="${index}">x</button>
          </div>
        </div>
      `);
        });
      }

      $('#totalHarga').text(formatRupiah(total));

      // Panggil di akhir
      renderHiddenInputs(keranjang);
    }


    $(document).on('click', '.add-button', function() {
      const produk = $(this).data('produk');
      console.log("Produk yang ditambahkan:", produk);
      const existing = keranjang.find(p => p.id === produk.id);

      const totalQtyInCart = existing ? existing.qty + 1 : 1;

      if (totalQtyInCart > produk.stok) {
        showAlert(`Stok untuk ${produk.nama_produk} hanya tersedia ${produk.stok}.`, "warning");
        return;
      }

      if (existing) {
        existing.qty += 1;
      } else {
        keranjang.push({
          id: produk.id,
          nama_produk: produk.nama_produk,
          harga: parseInt(produk.harga),
          qty: 1,
          stok: produk.stok
        });
      }

      updateKeranjang();
    });


    $(document).on('click', '.btn-hapus', function() {
      const index = $(this).data('index');
      keranjang.splice(index, 1);
      updateKeranjang();
    });

    function clearCart() {
      keranjang = [];
      updateKeranjang();
    }

    function showAlert(message, type = "danger") {
      const alertContainer = document.getElementById('alertContainer');
      const alertId = "alert-" + Date.now();

      alertContainer.innerHTML = `
      <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;

      setTimeout(() => {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
          alertElement.classList.remove("show");
          alertElement.classList.add("hide");
        }
      }, 2000);
    }

    function showConfirmationModal() {

      console.log("Keranjang saat konfirmasi:", keranjang);
      const namaPelanggan = document.getElementById('namaPelanggan').value.trim();
      const alertContainer = document.getElementById('alertContainer');
      alertContainer.innerHTML = '';

      if (!namaPelanggan) {
        showAlert("Nama pelanggan wajib diisi sebelum memesan.", "danger");
        return;
      }

      if (keranjang.length === 0) {
        showAlert("Keranjang masih kosong.", "danger");
        return;
      }

      document.getElementById('modalPelanggan').textContent = namaPelanggan;

      let modalItems = document.getElementById('modalItems');
      modalItems.innerHTML = '';
      let totalHarga = hitungTotal();

      keranjang.forEach(item => {
        const subtotal = item.harga * item.qty;
        modalItems.innerHTML += `
        <tr>
          <td>${item.nama_produk}</td>
          <td>${item.qty}</td>
          <td>${formatRupiah(subtotal)}</td>
        </tr>
      `;
      });

      document.getElementById('modalTotal').textContent = formatRupiah(totalHarga);

      let myModal = new bootstrap.Modal(document.getElementById("confirmModal"));
      myModal.show();
    }

    function submitOrder() {
      let totalHarga = hitungTotal();
      document.getElementById('inputTotal').value = totalHarga;

      const myModal = bootstrap.Modal.getInstance(document.getElementById("confirmModal"));
      if (myModal) myModal.hide();

      document.getElementById('formPesanan').submit();
    }

    document.getElementById('formPesanan').addEventListener('submit', function(e) {
      e.preventDefault();
    });

    $(document).ready(function() {
      // Fungsi filter produk
      function filterProduk(kategoriId, searchTerm = '') {
        $('.produk-item').each(function() {
          const itemKategori = $(this).data('kategori');
          const namaProduk = $(this).find('h6').text().toLowerCase();

          const kategoriMatch = kategoriId === 'all' || itemKategori == kategoriId;
          const searchMatch = namaProduk.includes(searchTerm.toLowerCase());

          if (kategoriMatch && searchMatch) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }

      // Handle klik tombol filter
      $('.filter-btn').click(function() {
        const kategoriId = $(this).data('kategori');

        // Update tampilan tombol aktif
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        // Jalankan filter
        const searchTerm = $('#searchInput').val();
        filterProduk(kategoriId, searchTerm);
      });

      // Handle input pencarian
      $('#searchInput').on('input', function() {
        const searchTerm = $(this).val();
        const kategoriAktif = $('.filter-btn.active').data('kategori');
        filterProduk(kategoriAktif, searchTerm);
      });
    });
  </script>



</body>

</html>