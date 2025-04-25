<?php
session_start();
require 'koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Validasi ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = (int)$_GET['id'];

  // Query hapus
  $query = "DELETE FROM produk WHERE id = $id";
  if (mysqli_query($conn, $query)) {
    header("Location: dataproduk.php?success=deleted");
    exit;
  } else {
    echo "Gagal menghapus data.";
  }
} else {
  echo "ID tidak valid.";
}
?>
