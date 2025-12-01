<?php
require 'config.php';
if(!isset($_GET['id'])) { header('Location: index.php'); exit; }
$id = (int)$_GET['id'];
$stmt = $conn->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$movie = $res->fetch_assoc();
if(!$movie) { echo 'Movie not found'; exit; }
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=htmlspecialchars($movie['title'])?> — Flictix</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header><a href="index.php">← Back</a></header>
<main class="detail">
    <img class="poster" src="<?=htmlspecialchars($movie['thumb'])?>" alt="poster">
    <div class="info">
        <h1><?=htmlspecialchars($movie['title'])?></h1>
        <p><strong>Year:</strong> <?=$movie['year']?></p>
        <p><strong>Duration:</strong> <?=$movie['duration']?> minutes</p>
        <p><?=nl2br(htmlspecialchars($movie['description']))?></p>
        <a class="btn" href="#">Play</a>
    </div>
</main>
</body>
</html>
