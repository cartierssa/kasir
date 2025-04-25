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
  $query = "DELETE FROM kategori WHERE id = $id";
  if (mysqli_query($conn, $query)) {
    header("Location: datakategori.php?success=deleted");
    exit;
  } else {
    echo "Gagal menghapus data.";
  }
} else {
  echo "ID tidak valid.";
}
?>
