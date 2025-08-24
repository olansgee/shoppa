<?php
require_once '../config/database.php';

// Get search query and category filter
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Build the SQL query
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%' OR c.name LIKE '%$search%')";
}

if ($category_id > 0) {
    $sql .= " AND p.category_id = $category_id";
}

$result = mysqli_query($conn, $sql);

// Get categories for the sidebar
$categories_sql = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_sql);

// Page title
if ($category_id > 0) {
    $category_sql = "SELECT name FROM categories WHERE id = $category_id";
    $category_result = mysqli_query($conn, $category_sql);
    $category_name = mysqli_fetch_assoc($category_result)['name'];
    $page_title = $category_name;
} else if (!empty($search)) {
    $page_title = "Search Results for '$search'";
} else {
    $page_title = "All Products";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Shoppa</title>
    <link rel="stylesheet" href="../css/explore.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="items">
        <div class="categories">
            <ol><h2>Categories</h2></ol>
            <?php while($category = mysqli_fetch_assoc($categories_result)): ?>
                <li>
                    <a href="explore.php?category=<?php echo $category['id']; ?>">
                        <img src="<?php echo $category['image_path']; ?>" alt="<?php echo $category['name']; ?>" class="category-icon">
                    </a>
                </li>
            <?php endwhile; ?>
        </div> 
        
        <div class="varsityitem">
            <div class="varsitytext">
                <h2><?php echo $page_title; ?></h2>
            </div>
            
            <div class="product-grid">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($product = mysqli_fetch_assoc($result)): ?>
                        <div class="product-card">
                            <div class="pageimg">
                                <img src="<?php echo $product['image_path']; ?>" alt="<?php echo $product['name']; ?>">
                            </div>
                            <div class="pagetext">
                                <h3><?php echo $product['name']; ?></h3>
                                <p>#<?php echo number_format($product['price'], 2); ?></p>
                                <form action="../cart/cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="add-to-cart">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>