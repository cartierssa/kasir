<?php
// Password untuk admin dan karyawan
$password_admin = "admin123";
$password_karyawan = "karyawan123";

// Generate hash
$hash_admin = password_hash($password_admin, PASSWORD_DEFAULT);
$hash_karyawan = password_hash($password_karyawan, PASSWORD_DEFAULT);

// Tampilkan SQL
echo "SQL untuk dimasukkan ke database:<br><br>";

echo "INSERT INTO users (nama, email, password, telepon, role, status) VALUES
('Admin', 'admin@kedaikito.com', '$hash_admin', '081234567890', 'admin', 'aktif'),
('Karyawan', 'karyawan@kedaikito.com', '$hash_karyawan', '087654321098', 'karyawan', 'aktif');";
?>