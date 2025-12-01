<?php
session_start();
include 'config.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $year = (int)$_POST['year'];
    $duration = (int)$_POST['duration'];
    $description = trim($_POST['description']);
    $thumb = '';

    // Handle image upload
    if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/movies/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['thumb']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadFile)) {
            $thumb = $uploadFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO movies (title, year, duration, thumb, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiss", $title, $year, $duration, $thumb, $description);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Movie added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to add movie!";
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
}

header('Location: admin.php');
exit();
?>