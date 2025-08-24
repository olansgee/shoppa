<?php
require_once '../config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Add item to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $product_id = intval($_POST['product_id']);
    
    // Check if product exists
    $sql = "SELECT id, name, price FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        
        // Add to cart or update quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            );
        }
        
        header("Location: cart.php");
        exit();
    }
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit();
}

// Update quantities
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $product_id = intval($product_id);
        $quantity = intval($quantity);
        
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Shoppa</title>
    <link rel="stylesheet" href="../css/home.css">
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .cart-item-details {
            flex: 2;
        }
        .cart-item-price {
            flex: 1;
            text-align: right;
        }
        .cart-item-quantity {
            flex: 1;
            text-align: center;
        }
        .cart-total {
            text-align: right;
            font-size: 1.2em;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }
        .checkout-btn {
            background-color: #C50000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            margin-top: 10px;
        }
        .checkout-btn:hover {
            background-color: #6E0202;
        }
        .continue-shopping {
            display: inline-block;
            margin-top: 10px;
            color: #C50000;
            text-decoration: none;
        }
        .continue-shopping:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
            <a href="../products/explore.php" class="continue-shopping">Continue Shopping</a>
        <?php else: ?>
            <form action="cart.php" method="POST">
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $item): 
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;
                ?>
                    <div class="cart-item">
                        <div class="cart-item-details">
                            <h3><?php echo $item['name']; ?></h3>
                        </div>
                        <div class="cart-item-price">
                            <p>#<?php echo number_format($item['price'], 2); ?> each</p>
                        </div>
                        <div class="cart-item-quantity">
                            <input type="number" name="quantities[<?php echo $product_id; ?>]" 
                                   value="<?php echo $item['quantity']; ?>" min="1" style="width: 60px;">
                        </div>
                        <div class="cart-item-actions">
                            <a href="cart.php?remove=<?php echo $product_id; ?>">Remove</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="cart-actions">
                    <button type="submit" name="update_cart">Update Cart</button>
                    <a href="../products/explore.php" class="continue-shopping">Continue Shopping</a>
                </div>
            </form>
            
            <div class="cart-total">
                <h3>Total: #<?php echo number_format($total, 2); ?></h3>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>