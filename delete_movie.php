<?php
session_start();
include 'config.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['movie_id'];
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['message'] = "Movie deleted successfully!";
    $_SESSION['message_type'] = "success";
}

header('Location: admin.php');
exit();
?>