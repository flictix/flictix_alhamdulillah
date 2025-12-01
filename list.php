<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$page_title = "My List";

// Get user's watchlist with movie details
$stmt = $conn->prepare("
    SELECT 
        m.*, 
        w.added_at,
        (SELECT rating FROM reviews WHERE user_id = ? AND movie_id = m.id) as user_rating,
        (SELECT COUNT(*) FROM favorites WHERE user_id = ? AND movie_id = m.id) as is_favorite
    FROM watchlist w 
    JOIN movies m ON w.movie_id = m.id 
    WHERE w.user_id = ? 
    ORDER BY w.added_at DESC
");
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$watchlist_result = $stmt->get_result();
$watchlist = $watchlist_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Flictix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: #0f0f0f;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: #1a1a1a !important;
            border-bottom: 1px solid #333;
        }
        
        .container {
            margin-top: 30px;
            margin-bottom: 50px;
        }
        
        .page-title {
            color: #e50914;
            margin-bottom: 30px;
            font-weight: bold;
        }
        
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .movie-card {
            background: #1a1a1a;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #333;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .movie-card:hover {
            transform: translateY(-5px);
            border-color: #e50914;
            box-shadow: 0 10px 25px rgba(229, 9, 20, 0.2);
        }
        
        .movie-poster {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-bottom: 1px solid #333;
        }
        
        .movie-info {
            padding: 20px;
        }
        
        .movie-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #fff;
        }
        
        .movie-meta {
            color: #888;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .movie-description {
            color: #ccc;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .movie-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .rating-stars {
            display: flex;
            gap: 2px;
        }
        
        .star-btn {
            background: none;
            border: none;
            color: #666;
            font-size: 16px;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 0;
        }
        
        .star-btn.active {
            color: #ffc107;
        }
        
        .star-btn:hover {
            color: #ffc107;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s ease;
        }
        
        .btn-remove:hover {
            background: #c82333;
        }
        
        .btn-favorite {
            background: none;
            border: none;
            color: #666;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .btn-favorite.active {
            color: #e50914;
        }
        
        .btn-favorite:hover {
            color: #e50914;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }
        
        .empty-state h2 {
            color: #ccc;
            margin-bottom: 15px;
        }
        
        .btn-browse {
            background: #e50914;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 15px;
            transition: background 0.3s ease;
        }
        
        .btn-browse:hover {
            background: #ff0000;
            color: white;
        }
        
        .added-date {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.8);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            color: #ccc;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" style="color: #e50914;">
                <i class="bi bi-film"></i> FLICTIX
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Home</a>
                <a class="nav-link active" href="list.php"><i class="bi bi-bookmark"></i> My List</a>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">
            <i class="bi bi-bookmark-fill"></i> My Watchlist
        </h1>
        
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo $_SESSION['message_type']; ?>">
                <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($watchlist) > 0): ?>
            <div class="movies-grid">
                <?php foreach ($watchlist as $movie): ?>
                    <div class="movie-card">
                        <div class="added-date">
                            Added: <?php echo date('M d, Y', strtotime($movie['added_at'])); ?>
                        </div>
                        
                        <?php if (!empty($movie['thumb'])): ?>
                            <img src="<?php echo htmlspecialchars($movie['thumb']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                 class="movie-poster"
                                 onerror="this.src='assets/default-poster.jpg'">
                        <?php else: ?>
                            <div class="movie-poster bg-secondary d-flex align-items-center justify-content-center">
                                <i class="bi bi-image text-white" style="font-size: 48px;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="movie-info">
                            <h3 class="movie-title">
                                <?php echo htmlspecialchars($movie['title']); ?> 
                                (<?php echo $movie['year']; ?>)
                            </h3>
                            
                            <div class="movie-meta">
                                <span><?php echo $movie['duration']; ?> minutes</span>
                            </div>
                            
                            <p class="movie-description">
                                <?php 
                                $description = htmlspecialchars($movie['description'] ?? 'No description available.');
                                echo strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description;
                                ?>
                            </p>
                            
                            <div class="movie-actions">
                                <!-- Rating System -->
                                <div class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <form method="POST" action="rating.php" style="display: inline;">
                                            <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                            <input type="hidden" name="rating" value="<?php echo $i; ?>">
                                            <button type="submit" class="star-btn <?php echo $i <= ($movie['user_rating'] ?? 0) ? 'active' : ''; ?>">
                                                <i class="bi bi-star<?php echo $i <= ($movie['user_rating'] ?? 0) ? '-fill' : ''; ?>"></i>
                                            </button>
                                        </form>
                                    <?php endfor; ?>
                                </div>
                                
                                <!-- Favorite Button -->
                                <form method="POST" action="favorites.php" style="display: inline;">
                                    <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                    <input type="hidden" name="action" value="<?php echo $movie['is_favorite'] ? 'remove' : 'add'; ?>">
                                    <button type="submit" class="btn-favorite <?php echo $movie['is_favorite'] ? 'active' : ''; ?>" 
                                            title="<?php echo $movie['is_favorite'] ? 'Remove from favorites' : 'Add to favorites'; ?>">
                                        <i class="bi bi-heart<?php echo $movie['is_favorite'] ? '-fill' : ''; ?>"></i>
                                    </button>
                                </form>
                                
                                <!-- Remove from Watchlist Button -->
                                <form method="POST" action="remove_watchlist.php" style="display: inline;">
                                    <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                    <button type="submit" class="btn-remove" 
                                            onclick="return confirm('Remove from your watchlist?')">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-bookmark-x" style="font-size: 64px; color: #666; margin-bottom: 20px;"></i>
                <h2>Your watchlist is empty</h2>
                <p>Start adding movies and shows to your personal watchlist!</p>
                <a href="index.php" class="btn-browse">
                    <i class="bi bi-film"></i> Browse Movies
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 FLICTIX. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide messages after 5 seconds
        setTimeout(function() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>