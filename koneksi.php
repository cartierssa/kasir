<?php
$host = "localhost";
$user = "root"; // Sesuaikan dengan username database
$pass = ""; // Sesuaikan dengan password database
$db = "kedaikito";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>