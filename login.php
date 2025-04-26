<?php
session_start();

if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit();
}
require_once 'koneksi.php'; 

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Query ke database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password (pastikan password di database di-hash)
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect ke dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Kedai Kito</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-image: url('assets/background/kedai.jpg');  background-size: cover;  min-height: 100vh; }
    .login-card { max-width: 400px; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
    .password-toggle { cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); z-index: 5; }
  </style>
  
</head>
<body class="d-flex align-items-center">
  <div class="container">
    <div class="login-card bg-white mx-auto p-4">
      <div class="text-center mb-5">
        <h2 class="fw-bold mb-1">Kedai Kito</h2>
        <p class="text-muted">Aplikasi Point of Sale</p>
      </div>

      <?php if (!empty($error)) : ?>
        <div class="alert alert-danger py-2 px-3 small"> <?= $error ?> </div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-4">
          <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
          <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="Masukkan email" required style="font-size: 0.9rem;">
        </div>

        <div class="mb-4 position-relative">
          <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
          <input type="password" class="form-control form-control-sm pe-5" id="password" name="password" placeholder="Masukkan password" required style="font-size: 0.9rem;">
          <i class="bi bi-eye-slash password-toggle text-secondary" id="togglePassword" style="top: 60%; right: 12px;"></i>
        </div>

        <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">Login</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', function() {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      this.classList.toggle('bi-eye');
      this.classList.toggle('bi-eye-slash');
    });
  </script>
</body>
</html>
