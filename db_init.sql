-- Database initialization for CCDI Visitor Inquiry Logging System
CREATE DATABASE IF NOT EXISTS visitor_db;
USE visitor_db;
-- users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- visitors table
CREATE TABLE IF NOT EXISTS visitors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  visitor_name VARCHAR(255) NOT NULL,
  visit_date DATE NOT NULL,
  visit_time TIME NOT NULL,
  address VARCHAR(255),
  contact VARCHAR(50),
  school_office VARCHAR(255),
  purpose ENUM('Inquiry', 'Exam', 'Visit', 'Other') DEFAULT 'Inquiry',
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE
  SET NULL
);
-- sample admin user (password: Password123!)
INSERT IGNORE INTO users (fullname, email, password_hash)
VALUES (
    'Administrator',
    'admin@example.com',
    '$2y$10$e0NRKxv1oZk6Y1sQeGZKkOSy9i3nX1E2f0c5oG8q6zKqf0JYJ6f6e'
  );
-- sample visitors
INSERT INTO visitors (
    visitor_name,
    visit_date,
    visit_time,
    address,
    contact,
    school_office,
    purpose,
    created_by
  )
VALUES (
    'BonJohn Mayores',
    CURDATE(),
    CURTIME(),
    'Pangpang, Sorsogon City',
    '09171234567',
    'CCDI',
    'Exam',
    1
  ),
  (
    'Joshua Hidalgo',
    CURDATE(),
    CURTIME(),
    'Sorsogon, Sorsogon City',
    '09179876543',
    'Local Office',
    'Inquiry',
    1
  );