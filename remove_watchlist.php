<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'])) {
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];
    
    // Remove from watchlist
    $stmt = $conn->prepare("DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("ii", $user_id, $movie_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Movie removed from your watchlist!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error removing movie from watchlist!";
        $_SESSION['message_type'] = "error";
    }
    
    $stmt->close();
}

header('Location: list.php');
exit();
?>