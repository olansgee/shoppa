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
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .product-card {
            border: 1px solid #D9D9D9;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .add-to-cart {
            background-color: #C50000;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-family: Satoshi, sans-serif;
        }
        .add-to-cart:hover {
            background-color: #6E0202;
        }
        .categories {
            background: #C50000;
            padding-top: 3rem;
            list-style: none;
            height: 100%;
        }
        .categories li {
            margin-top: 2rem;
            margin-right: 4rem;
            margin-left: 2rem;
        }
        .categories a {
            display: block;
        }
        .categories ol {
            font-family: Satoshi;
            font-weight: 1000;
            font-style: Black;
            font-size: 24px;
            line-height: 100%;
            letter-spacing: 0%;
            color: #FFFFFF;
            width: 100%;
            margin-left: 1rem;
        }
        .page-title {
            font-family: Satoshi;
            font-weight: 700;
            font-style: Bold;
            font-size: 32px;
            color: #6E0202;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="items">
        <div class="categories">
            <ol><h2>Categories</h2></ol>
            <?php while($category = mysqli_fetch_assoc($categories_result)): ?>
                <li>
                    <a href="explore.php?category=<?php echo $category['id']; ?>">
                        <img src="/shoppa-main/<?php echo $category['image_path']; ?>" alt="<?php echo $category['name']; ?>">
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
                                <img src="/shoppa-main/<?php echo $product['image_path']; ?>" alt="<?php echo $product['name']; ?>">
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