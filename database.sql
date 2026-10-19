CREATE DATABASE cafe_db;
USE cafe_db;
CREATE TABLE cafe (
   cafe_id INT AUTO_INCREMENT PRIMARY KEY,
   cafe_name VARCHAR(100) NOT NULL,
   address VARCHAR(255)
);
CREATE TABLE orders (
   order_id INT AUTO_INCREMENT PRIMARY KEY,
   order_date DATE NOT NULL,
   customer_name VARCHAR(100) NOT NULL,
   total_amount DECIMAL(10,2) NOT NULL,
   cafe_id INT NOT NULL,
   FOREIGN KEY (cafe_id) REFERENCES cafe(cafe_id)
);