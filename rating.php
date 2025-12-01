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
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

if ($movie_id == 0 || $rating < 1 || $rating > 5) {
    header('Location: index.php');
    exit();
}

// Update rating
updateMovieRating($conn, $user_id, $movie_id, $rating);

$_SESSION['message'] = "Rating submitted successfully!";
$_SESSION['message_type'] = "success";

// Redirect back
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit();
?>