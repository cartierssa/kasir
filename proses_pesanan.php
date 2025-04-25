<?php
session_start();
require_once 'koneksi.php';



// FUNGSI GENERATE INVOICE ID
function generateInvoiceId($conn)
{
  $prefix = 'INV';
  $stmt = $conn->query("SELECT MAX(id) as last_id FROM pesanan");
  $last_id = $stmt->fetch_assoc()['last_id'] + 1;
  return $prefix . str_pad($last_id, 8, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $conn->begin_transaction();

    // Generate invoice ID
    $invoice_id = generateInvoiceId($conn);

    // Simpan pesanan utama
    $stmt = $conn->prepare("INSERT INTO pesanan 
      (kode_invoice, user_id, nama_pelanggan, total, status) 
      VALUES (?, ?, ?, ?, 'menunggu pembayaran')");
    $stmt->bind_param(
      "sisi",
      $invoice_id,
      $_SESSION['user_id'],
      $_POST['nama_pelanggan'],
      $_POST['total']
    );
    $stmt->execute();
    $pesanan_id = $conn->insert_id;

    // Simpan detail pesanan
    foreach ($_POST['items'] as $item) {

      // Simpan detail pesanan
      $stmt = $conn->prepare("INSERT INTO detail_pesanan 
    (pesanan_id, produk_id, jumlah, harga_satuan, total_harga) 
    VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param(
        "iiiid",
        $pesanan_id,
        $item['produk_id'],
        $item['qty'],
        $item['harga'],
        $item['subtotal']
      );
      $stmt->execute();
    }

    $conn->commit();

    header("Location: detail_pesanan.php?id=$pesanan_id");
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
  }
}
