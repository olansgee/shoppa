<?php
// index.php - FIXED INCLUDE PATH
require_once 'config/database.php';

// Fetch featured products
$featured_products_sql = "SELECT * FROM products WHERE featured = TRUE LIMIT 6";
$featured_products_result = mysqli_query($conn, $featured_products_sql);

// Fetch categories
$categories_sql = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoppa - Online Store</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
   <div class="homepage">
     <header>
        <div class="headimage">
            <img src="images/Frame 20.png" alt="headimage">
        </div>
     </header>
     
     <?php include 'includes/header.php'; ?>
     
     <div class="bodycat">
         <div class="categories">
             <ol>Categories</ol>
             <?php while($category = mysqli_fetch_assoc($categories_result)): ?>
                 <li>
                     <a href="products/explore.php?category=<?php echo $category['id']; ?>">
                         <img src="<?php echo $category['image_path']; ?>" alt="<?php echo $category['name']; ?>" class="category-icon">
                     </a>
                 </li>
             <?php endwhile; ?>
          </div> 
          <div class="Explored">
            <div class="versatile">
                <div class="versatiletext">
                    <h2>Get Versatile with Varsity!!</h2>
                </div>
                <div class="versatileimg">
                    <div class="image">
                        <img src="images/Group 2.png" alt="jacket">
                    </div>
                    <div class="images">
                       <img src="images/Frame 19.png" alt="dots">
                    </div>
                </div>
                <div class="explorebutton">
                    <a href="products/explore.php"><button>Explore &rarr;</button></a>
                </div>
            </div>

            <div class="product-grid">
                <?php while($product = mysqli_fetch_assoc($featured_products_result)): ?>
                    <div class="product-card">
                        <div class="pageimg">
                            <img src="<?php echo $product['image_path']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="pagetext">
                            <h3><?php echo $product['name']; ?></h3>
                            <p>#<?php echo number_format($product['price'], 2); ?></p>
                            <form action="cart/cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="add-to-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
          </div>
     </div>
   </div>
   
   <?php include 'includes/footer.php'; ?>
</body>
</html>