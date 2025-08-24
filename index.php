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
    </style>
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
                         <img src="<?php echo $category['image_path']; ?>" alt="<?php echo $category['name']; ?>">
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