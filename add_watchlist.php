<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please login to add movies to your watchlist";
    $_SESSION['message_type'] = "error";
    header('Location: login.php');
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'])) {
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];
    
    // Check if movie already in watchlist
    $check_stmt = $conn->prepare("SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?");
    $check_stmt->bind_param("ii", $user_id, $movie_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Remove from watchlist
        $delete_stmt = $conn->prepare("DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $movie_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['message'] = "Movie removed from watchlist!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error removing movie from watchlist!";
            $_SESSION['message_type'] = "error";
        }
        $delete_stmt->close();
    } else {
        // Add to watchlist
        $insert_stmt = $conn->prepare("INSERT INTO watchlist (user_id, movie_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $user_id, $movie_id);
        
        if ($insert_stmt->execute()) {
            $_SESSION['message'] = "Movie added to watchlist!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding movie to watchlist!";
            $_SESSION['message_type'] = "error";
        }
        $insert_stmt->close();
    }
    
    $check_stmt->close();
}

// Redirect back to the previous page
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>