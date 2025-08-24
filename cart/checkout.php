<?php
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Process checkout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Get user details
    $user_id = $_SESSION['user_id'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Create order
    $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, shipping_phone) 
            VALUES ($user_id, $total, '$address', '$phone')";
    
    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Add order items
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $price = $item['price'];
            $quantity = $item['quantity'];
            
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES ($order_id, $product_id, $quantity, $price)";
            mysqli_query($conn, $sql);
        }
        
        // Clear cart
        unset($_SESSION['cart']);
        
        // Redirect to success page
        header("Location: order_success.php?order_id=$order_id");
        exit();
    } else {
        $error = "Error placing order: " . mysqli_error($conn);
    }
}

// Get user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Shoppa</title>
    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
    <div class="checkhead">
        <div class="checka"><a href="../index.php"> &lt;</a></div>
        <div class="checkb">Checkout</div>
    </div>
    
    <div class="checkedout">
        <div class="checkout">
            <form action="checkout.php" method="POST">
                <div class="customers">
                    <div class="checkcustomers">
                        <div class="details">
                            <div class="customed">
                                <h2>1. Customer's Details</h2>
                            </div>
                            <div class="edited">
                                <div class="edit">
                                    <h3>Edit Details</h3>
                                </div> 
                            </div>
                        </div>
                        <hr>
                        
                        <div class="customer-info">
                            <div class="input-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            <div class="input-group">
                                <label for="address">Shipping Address:</label>
                                <textarea id="address" name="address" required><?php echo $user['address']; ?></textarea>
                            </div>
                            <div class="input-group">
                                <label for="phone">Phone Number:</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="checkcustomers2">
                        <div class="details">
                            <div class="customed">
                                <h2>2. Delivery Details</h2>
                            </div>
                        </div>
                        <hr>
                        
                        <div class="delivery-options">
                            <div class="delivery-option">
                                <input type="radio" id="door-delivery" name="delivery" value="door" checked>
                                <label for="door-delivery">
                                    <h3>Door Delivery</h3>
                                    <p>Delivery between 7 days</p>
                                    <p class="price">#5,200.00</p>
                                </label>
                            </div>
                            
                            <div class="delivery-option">
                                <input type="radio" id="pickup-station" name="delivery" value="pickup">
                                <label for="pickup-station">
                                    <h3>Pickup Station</h3>
                                    <p>Save up to #4,100</p>
                                    <p class="price">#2,200.00</p>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-summary">
                        <div class="orderhead">
                            <h2><b>Ordered Item(s)</b></h2>
                        </div>
                        
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $product_id => $item): 
                            $item_total = $item['price'] * $item['quantity'];
                            $total += $item_total;
                        ?>
                            <div class="ordered">
                                <div class="orderimg">
                                    <img src="../images/Rectangle 16.png" alt="product">
                                </div>
                                <div class="orderdetails">
                                    <div class="ordername">
                                        <h3><?php echo $item['name']; ?></h3>
                                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="orderamount">
                                        <h4>#<?php echo number_format($item_total, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="ordain">
                    <div class="orderform">
                        <div class="orderformhead">
                            <h2>Order Summary</h2>
                        </div>
                        <hr>
                        
                        <div class="orderformdetails">
                            <div class="orderformdeal">
                                <div class="orderfomdealstxt"><p>Total Item(s) - <?php echo count($_SESSION['cart']); ?></p></div>
                                <div class="orderformprice">#<?php echo number_format($total, 2); ?></div>
                            </div>
                            
                            <div class="orderformdeal">
                                <div class="orderfomdealstxt"><p>Delivery Fees</p></div>
                                <div class="orderformprice">#5,200.00</div>
                            </div>
                            
                            <div class="orderformdeal">
                                <div class="orderfomdealstxt"><p>Total</p></div>
                                <div class="orderformprice">#<?php echo number_format($total + 5200, 2); ?></div>
                            </div>
                            
                            <div class="redeem">
                                <div class="redeeminput">
                                    <input type="text" placeholder="Redeem Coupon">
                                </div>
                                <div class="redeembutton">
                                    <button type="button">Redeem</button>
                                </div>
                            </div>
                            
                            <button type="submit" class="place-order-btn">Place Order</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>