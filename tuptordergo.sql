-- Create the database
CREATE DATABASE IF NOT EXISTS TUPTOrderGo; 
USE TUPTOrderGo; 

-- Create the users table with additional columns: fullname and profile
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,         
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'vendor') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
