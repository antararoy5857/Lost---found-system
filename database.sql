-- Create database
CREATE DATABASE IF NOT EXISTS campus_lost_found;
USE campus_lost_found;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('student', 'admin') DEFAULT 'student',
    phone VARCHAR(20),
    student_id VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Lost items table
CREATE TABLE lost_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    location VARCHAR(255),
    date_lost DATE,
    image_path VARCHAR(255),
    status ENUM('pending', 'found', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Found items table
CREATE TABLE found_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    location VARCHAR(255),
    date_found DATE,
    image_path VARCHAR(255),
    status ENUM('pending', 'claimed', 'returned') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Claims table
CREATE TABLE claims (
    id INT PRIMARY KEY AUTO_INCREMENT,
    found_item_id INT,
    user_id INT,
    claim_description TEXT,
    proof_details TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_remarks TEXT,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (found_item_id) REFERENCES found_items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, user_type) 
VALUES ('Admin', 'admin@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample student (password: student123)
INSERT INTO users (name, email, password, phone, student_id) 
VALUES ('John Doe', 'student@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', '22-12345-3');

-- Sample data
INSERT INTO lost_items (user_id, item_name, category, description, location, date_lost, status) VALUES
(2, 'iPhone 13', 'Electronics', 'Black iPhone 13 with blue case, lost in library', 'Main Library', '2024-01-15', 'pending'),
(2, 'Calculus Textbook', 'Books', 'Calculus 9th Edition by James Stewart', 'Mathematics Building', '2024-01-10', 'found');

INSERT INTO found_items (user_id, item_name, category, description, location, date_found, status) VALUES
(1, 'Red Water Bottle', 'Accessories', 'Red metal water bottle with stickers', 'Cafeteria', '2024-01-20', 'pending'),
(1, 'Wireless Earbuds', 'Electronics', 'White wireless earbuds in black case', 'Gym', '2024-01-18', 'pending');

INSERT INTO claims (found_item_id, user_id, claim_description, proof_details, status) VALUES
(1, 2, 'This is my water bottle, I put stickers of my favorite bands on it', 'Has stickers of Metallica and Pink Floyd', 'pending');