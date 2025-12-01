<?php
require '../config.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header('Location: ../login.php'); exit;
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = $_POST['title'];
    $year = (int)$_POST['year'];
    $duration = (int)$_POST['duration'];
    $thumb = $_POST['thumb'];
    $desc = $_POST['description'];
    $stmt = $conn->prepare('INSERT INTO movies (title,year,duration,thumb,description) VALUES (?,?,?,?,?)');
    $stmt->bind_param('siiss',$title,$year,$duration,$thumb,$desc);
    if($stmt->execute()){
        header('Location: ../index.php'); exit;
    } else {
        $error = 'Could not add movie';
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add Movie - Admin</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<main class="center">
    <h2>Add New Movie</h2>
    <?php if(isset($error)) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
    <form method="post">
        <input name="title" required placeholder="Title">
        <input name="year" type="number" required placeholder="Year">
        <input name="duration" type="number" required placeholder="Duration (mins)">
        <input name="thumb" required placeholder="Thumbnail URL">
        <textarea name="description" placeholder="Description"></textarea>
        <button type="submit">Add Movie</button>
    </form>
    <p><a href="../index.php">Back to home</a></p>
</main>
</body></html>
