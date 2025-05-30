-- Create database
CREATE DATABASE IF NOT EXISTS supermarket_billing;
USE supermarket_billing;

-- Create materials table
CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample materials
INSERT INTO materials (name, price) VALUES
('Sample Material 1', 100.00),
('Sample Material 2', 150.00),
('Sample Material 3', 200.00); 