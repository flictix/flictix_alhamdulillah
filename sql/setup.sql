-- Create database and tables for flictix_web
CREATE DATABASE IF NOT EXISTS flictix_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE flictix_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    year SMALLINT NOT NULL,
    duration SMALLINT DEFAULT 90,
    thumb VARCHAR(500) DEFAULT '',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default admin and sample movies
INSERT IGNORE INTO users (id,name,email,password,role) VALUES
(1,'Admin','admin@flictix.test','$2y$10$e0NR9K0K1q6jv1o8Xl6VSe3p0aY0e6Oq6W8QmQ1rY6KfXwZlQeG9u','admin');
-- password is 'admin123' (bcrypt)

INSERT IGNORE INTO movies (title,year,duration,thumb,description) VALUES
('The Lost Horizon',2021,120,'https://via.placeholder.com/400x600?text=Lost+Horizon','An epic journey.'),
('City of Lights',2019,95,'https://via.placeholder.com/400x600?text=City+of+Lights','A romantic city tale.'),
('The Last Stand',2020,110,'https://via.placeholder.com/400x600?text=The+Last+Stand','Action-packed thriller.');
