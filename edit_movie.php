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
    $title = trim($_POST['title']);
    $year = (int)$_POST['year'];
    $duration = (int)$_POST['duration'];
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE movies SET title = ?, year = ?, duration = ?, description = ? WHERE id = ?");
    $stmt->bind_param("siisi", $title, $year, $duration, $description, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Movie updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to update movie!";
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
}

header('Location: admin.php');
exit();
?>