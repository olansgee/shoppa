<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'shoppa_db');

// Create connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Start session
session_start();

// Create tables if they don't exist
function initializeDatabase($conn) {
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        name VARCHAR(100),
        address TEXT,
        phone VARCHAR(20),
        is_admin BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        echo "Error creating users table: " . mysqli_error($conn);
    }
    
    // Categories table
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        image_path VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        echo "Error creating categories table: " . mysqli_error($conn);
    }
    
    // Products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        image_path VARCHAR(255),
        category_id INT,
        featured BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )";
    
    if (!mysqli_query($conn, $sql)) {
        echo "Error creating products table: " . mysqli_error($conn);
    }
    
    // Orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total_amount DECIMAL(10, 2) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        shipping_address TEXT,
        shipping_phone VARCHAR(20),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";

     // Add is_admin column if it doesn't exist
    $check_admin_column = "SHOW COLUMNS FROM users LIKE 'is_admin'";
    $result = mysqli_query($conn, $check_admin_column);
    if (mysqli_num_rows($result) == 0) {
        $add_column_sql = "ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE AFTER phone";
        if (!mysqli_query($conn, $add_column_sql)) {
            echo "Error adding is_admin column: " . mysqli_error($conn) . "<br>";
        }
    }
    
    if (!mysqli_query($conn, $sql)) {
        echo "Error creating orders table: " . mysqli_error($conn);
    }
    
    // Order items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    
    if (!mysqli_query($conn, $sql)) {
        echo "Error creating order_items table: " . mysqli_error($conn);
    }
    
    // Insert sample categories
    $categories = [
        ['Health & Beauty', '/images/Frame 14.png'],
        ['Phones & Tablets', '/images/Frame 15.png'],
        ['Mens Fashion', '/images/Frame 12.png'],
        ['Home & Office', '/images/Frame 13.png'],
        ['Automotive', '/images/Frame 10.png']
    ];
    
    foreach ($categories as $category) {
        $name = mysqli_real_escape_string($conn, $category[0]);
        $image_path = mysqli_real_escape_string($conn, $category[1]);
        
        $check_sql = "SELECT id FROM categories WHERE name = '$name'";
        $result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($result) == 0) {
            $insert_sql = "INSERT INTO categories (name, image_path) VALUES ('$name', '$image_path')";
            if (!mysqli_query($conn, $insert_sql)) {
                echo "Error inserting category: " . mysqli_error($conn) . "<br>";
            }
        }
    }
    
    // Insert sample products - FIXED THE TRAILING COMMA ISSUE
    $products = [
        ['Ergonomic Gaming Chair - Purple', 300450.00, '/images/Rectangle 10.png', 3, true],
        ['Ergonomic Gaming Chair - Red', 300450.00, '/images/Rectangle 10 (1).png', 3, true],
        ['Luxury Varsity Jacket - Blue/Red', 300450.00, '/images/Rectangle 10 (2).png', 3, true],
        ['Luxury Varsity Jacket - Black/Cream', 300450.00, '/images/Rectangle 10 (3).png', 3, true],
        ['Luxury Varsity Jacket - Blue/Red', 300450.00, '/images/Rectangle 10 (4).png', 3, true],
        ['Luxury Varsity Jacket - Blue/White', 300450.00, '/images/Rectangle 10 (5).png', 3, true],
        ['Unisex Varsity Leather Jacket - Black/Cream', 77580.00, '/images/Rectangle 16.png', 3, false]
    ];
    
    foreach ($products as $product) {
        $name = mysqli_real_escape_string($conn, $product[0]);
        $price = $product[1];
        $image_path = mysqli_real_escape_string($conn, $product[2]);
        $category_id = $product[3];
        $featured = $product[4] ? 'TRUE' : 'FALSE'; // Convert boolean to SQL format
        
        $check_sql = "SELECT id FROM products WHERE name = '$name'";
        $result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($result) == 0) {
            // FIXED: Removed the trailing comma and properly formatted the SQL
            $insert_sql = "INSERT INTO products (name, price, image_path, category_id, featured) 
                          VALUES ('$name', $price, '$image_path', $category_id, $featured)";
            
            if (!mysqli_query($conn, $insert_sql)) {
                echo "Error inserting product '$name': " . mysqli_error($conn) . "<br>";
                echo "SQL: " . $insert_sql . "<br>"; // Debug output
            }
        }
    }
}

// Initialize the database
initializeDatabase($conn);
?>