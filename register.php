<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Jika sudah login dan bukan dari form POST, redirect ke index
if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}
$error = '';
$success = '';
$fullname = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config.php';
    
    // Ambil dan bersihkan input
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } 
    elseif (strlen($password) < 4 || strlen($password) > 60) {
        $error = 'Password must be between 4 and 60 characters.';
    } 
    elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } 
    else {
        // Cek apakah email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'This email is already registered. Please login instead.';
            $stmt->close();
        } else {
            $stmt->close();
            
            // Hash password dan insert user baru
            // PERBAIKAN: Gunakan 'name' bukan 'fullname' karena itu nama kolom di database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = 'Registration successful! Redirecting to login page...';
                $fullname = '';
                $email = '';
                
                // Redirect ke login setelah 2 detik
                header("refresh:2;url=login.php");
            } else {
                $error = 'Registration failed: ' . $stmt->error;
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Flictix</title>
    <link rel="stylesheet" href="assets/register.css">
</head>
<body>
    <div class="logo">
        <span>🎬</span>
        <span>FLICTIX</span>
    </div>

    <div class="register-container">
        <h1 class="register-header">Sign Up</h1>

        <?php if ($error): ?>
        <div class="error-banner"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="success-banner"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input 
                    type="text" 
                    name="fullname" 
                    placeholder="Full Name" 
                    value="<?= htmlspecialchars($fullname) ?>" 
                    required
                    <?= $success ? 'disabled' : '' ?>
                >
            </div>

            <div class="form-group">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email Address" 
                    value="<?= htmlspecialchars($email) ?>" 
                    required
                    <?= $success ? 'disabled' : '' ?>
                >
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password (min. 4 characters)" 
                    required
                    <?= $success ? 'disabled' : '' ?>
                >
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="confirm_password" 
                    placeholder="Confirm Password" 
                    required
                    <?= $success ? 'disabled' : '' ?>
                >
            </div>

            <button type="submit" class="register-btn" <?= $success ? 'disabled' : '' ?>>
                Sign Up
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>