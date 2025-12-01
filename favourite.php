<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($movie_id == 0) {
    header('Location: index.php');
    exit();
}

if ($action === 'add') {
    // Add to watchlist
    $stmt = $conn->prepare("INSERT IGNORE INTO watchlist (user_id, movie_id, added_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $movie_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Added to My List!";
        $_SESSION['message_type'] = "success";
    }
    
} elseif ($action === 'remove') {
    // Remove from watchlist
    $stmt = $conn->prepare("DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("ii", $user_id, $movie_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Removed from My List!";
        $_SESSION['message_type'] = "success";
    }
}

$stmt->close();
$conn->close();

// Redirect back
header('Location: list.php');
exit();
?>