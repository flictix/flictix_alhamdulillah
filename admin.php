<?php
session_start();
include 'config.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit();
}

// Get data from database
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$movies = $conn->query("SELECT * FROM movies ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$watchlist = $conn->query("
    SELECT w.*, u.name as user_name, m.title as movie_title 
    FROM watchlist w 
    JOIN users u ON w.user_id = u.id 
    JOIN movies m ON w.movie_id = m.id 
    ORDER BY w.added_at DESC
")->fetch_all(MYSQLI_ASSOC);
$favorites = $conn->query("
    SELECT f.*, u.name as user_name, m.title as movie_title 
    FROM favorites f 
    JOIN users u ON f.user_id = u.id 
    JOIN movies m ON f.movie_id = m.id 
    ORDER BY f.added_at DESC
")->fetch_all(MYSQLI_ASSOC);
$reviews = $conn->query("
    SELECT r.*, u.name as user_name, m.title as movie_title 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    JOIN movies m ON r.movie_id = m.id 
    ORDER BY r.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Get statistics
$users_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$movies_count = $conn->query("SELECT COUNT(*) as count FROM movies")->fetch_assoc()['count'];
$reviews_count = $conn->query("SELECT COUNT(*) as count FROM reviews")->fetch_assoc()['count'];
$favorites_count = $conn->query("SELECT COUNT(*) as count FROM favorites")->fetch_assoc()['count'];
$watchlist_count = $conn->query("SELECT COUNT(*) as count FROM watchlist")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Flictix Admin</title>
    <link rel="stylesheet" href="assets/admin.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        /* ... (CSS styles dari sebelumnya) ... */
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="bi bi-film"></i>
            <div>
                <h3>FLICTIX</h3>
                <p>Admin Panel</p>
            </div>
        </div>
        
        <div class="nav-menu">
            <a class="nav-item active" data-page="users">
                <i class="bi bi-people-fill"></i>
                <span>Users</span>
            </a>
            <a class="nav-item" data-page="movies">
                <i class="bi bi-film"></i>
                <span>Movies</span>
            </a>
            <a class="nav-item" data-page="watchlist">
                <i class="bi bi-bookmark-fill"></i>
                <span>Watchlist</span>
            </a>
            <a class="nav-item" data-page="favorites">
                <i class="bi bi-heart-fill"></i>
                <span>Favorites</span>
            </a>
            <a class="nav-item" data-page="reviews">
                <i class="bi bi-star-fill"></i>
                <span>Reviews</span>
            </a>
        </div>
        
        <div class="sidebar-footer">
            <a href="index.php" class="nav-item">
                <i class="bi bi-house-fill"></i>
                <span>Back to Home</span>
            </a>
            <a href="logout.php" class="nav-item" onclick="return confirm('Logout?')">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <i class="bi bi-person-circle"></i>
                <span>Welcome, <?php echo $_SESSION['user_email']; ?></span>
            </div>
        </div>

        <!-- Stats Cards -->
        

        <!-- Content Area -->
        <div class="content-area">
            <!-- Users Section -->
            <div id="users-section" class="content-section active">
                <div class="content-header">
                    <h2><i class="bi bi-people-fill me-2"></i>Users Management</h2>
                    <!-- NO ADD BUTTON FOR USERS -->
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><strong>#<?php echo $user['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y, H:i', strtotime($user['created_at'])); ?></td>
                                <td class="text-center">
                                    <?php if ($user['role'] !== 'admin'): ?>
                                    <form method="POST" action="delete_user.php" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Delete this user?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-muted">Protected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Movies Section -->
            <div id="movies-section" class="content-section">
                <div class="content-header">
                    <h2><i class="bi bi-film me-2"></i>Movies Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMovieModal">
                        <i class="bi bi-plus-circle"></i> Add Movie
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Thumbnail</th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Duration</th>
                                <th>Description</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td><strong>#<?php echo $movie['id']; ?></strong></td>
                                <td>
                                    <?php if (!empty($movie['thumb'])): ?>
                                        <img src="<?php echo htmlspecialchars($movie['thumb']); ?>" class="image-preview" alt="Movie Thumbnail">
                                    <?php else: ?>
                                        <div class="bg-secondary d-flex align-items-center justify-content-center image-preview">
                                            <i class="bi bi-image text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($movie['title']); ?></strong></td>
                                <td><?php echo $movie['year']; ?></td>
                                <td><?php echo $movie['duration']; ?> mins</td>
                                <td><?php echo substr(htmlspecialchars($movie['description']), 0, 50) . '...'; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editMovieModal"
                                            data-id="<?php echo $movie['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($movie['title']); ?>"
                                            data-year="<?php echo $movie['year']; ?>"
                                            data-duration="<?php echo $movie['duration']; ?>"
                                            data-description="<?php echo htmlspecialchars($movie['description']); ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form method="POST" action="delete_movie.php" style="display: inline;">
                                        <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Delete this movie?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Watchlist Section -->
            <div id="watchlist-section" class="content-section">
                <div class="content-header">
                    <h2><i class="bi bi-bookmark-fill me-2"></i>Watchlist Management</h2>
                    <!-- NO ADD BUTTON FOR WATCHLIST -->
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Movie</th>
                                <th>Added At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($watchlist as $item): ?>
                            <tr>
                                <td><strong>#<?php echo $item['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($item['user_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($item['movie_title']); ?></strong></td>
                                <td><?php echo date('d M Y, H:i', strtotime($item['added_at'])); ?></td>
                                <td class="text-center">
                                    <form method="POST" action="delete_watchlist.php" style="display: inline;">
                                        <input type="hidden" name="watchlist_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Delete this watchlist item?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Favorites Section -->
            <div id="favorites-section" class="content-section">
                <div class="content-header">
                    <h2><i class="bi bi-heart-fill me-2"></i>Favorites Management</h2>
                    <!-- NO ADD BUTTON FOR FAVORITES -->
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Movie</th>
                                <th>Added At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($favorites as $item): ?>
                            <tr>
                                <td><strong>#<?php echo $item['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($item['user_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($item['movie_title']); ?></strong></td>
                                <td><?php echo date('d M Y, H:i', strtotime($item['added_at'])); ?></td>
                                <td class="text-center">
                                    <form method="POST" action="delete_favorite.php" style="display: inline;">
                                        <input type="hidden" name="favorite_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Delete this favorite?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews-section" class="content-section">
                <div class="content-header">
                    <h2><i class="bi bi-star-fill me-2"></i>Reviews Management</h2>
                    <!-- NO ADD BUTTON FOR REVIEWS -->
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Movie</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><strong>#<?php echo $review['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($review['movie_title']); ?></strong></td>
                                <td>
                                    <span class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?php echo $i <= $review['rating'] ? '-fill' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </span>
                                </td>
                                <td><?php echo substr(htmlspecialchars($review['comment']), 0, 50) . '...'; ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($review['created_at'])); ?></td>
                                <td class="text-center">
                                    <form method="POST" action="delete_review.php" style="display: inline;">
                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Delete this review?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Forms -->
    <!-- Add Movie Modal -->
    <div class="modal fade" id="addMovieModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-film"></i> Add New Movie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="add_movie.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Movie Title *</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Release Year *</label>
                                    <input type="number" class="form-control" name="year" min="1900" max="2030" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Duration (minutes) *</label>
                                    <input type="number" class="form-control" name="duration" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Thumbnail Image</label>
                                    <input type="file" class="form-control" name="thumb" accept="image/*">
                                    <div class="mt-2">
                                        <img id="thumbnailPreview" src="" class="img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Movie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Movie Modal -->
    <div class="modal fade" id="editMovieModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-film"></i> Edit Movie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="edit_movie.php">
                    <div class="modal-body">
                        <input type="hidden" name="movie_id" id="edit_movie_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Movie Title *</label>
                                    <input type="text" class="form-control" name="title" id="edit_title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Release Year *</label>
                                    <input type="number" class="form-control" name="year" id="edit_year" min="1900" max="2030" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Duration (minutes) *</label>
                                    <input type="number" class="form-control" name="duration" id="edit_duration" min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Movie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Navigation functionality
        document.querySelectorAll('.nav-item[data-page]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.nav-item').forEach(nav => {
                    nav.classList.remove('active');
                });
                
                this.classList.add('active');
                
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                const page = this.getAttribute('data-page');
                document.getElementById(page + '-section').classList.add('active');
            });
        });

        // Image preview for movie thumbnail
        document.querySelector('input[name="thumb"]')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('thumbnailPreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Edit movie modal data
        document.querySelectorAll('[data-bs-target="#editMovieModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const year = this.getAttribute('data-year');
                const duration = this.getAttribute('data-duration');
                const description = this.getAttribute('data-description');
                
                document.getElementById('edit_movie_id').value = id;
                document.getElementById('edit_title').value = title;
                document.getElementById('edit_year').value = year;
                document.getElementById('edit_duration').value = duration;
                document.getElementById('edit_description').value = description;
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>