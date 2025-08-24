<?php
// cart/checkout.php
//session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?redirect=checkout");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

// Delivery fee
$delivery_fee = 5200.00;
$total_amount = $cart_total + $delivery_fee;

// Process checkout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $delivery_method = mysqli_real_escape_string($conn, $_POST['delivery_method']);
    
    // Create order
    $order_sql = "INSERT INTO orders (user_id, total_amount, shipping_address, shipping_phone, delivery_method) 
                  VALUES ($user_id, $total_amount, '$address', '$phone', '$delivery_method')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Add order items
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $price = $item['price'];
            $quantity = $item['quantity'];
            
            $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                              VALUES ($order_id, $product_id, $quantity, $price)";
            mysqli_query($conn, $order_item_sql);
        }
        
        // Clear cart and redirect to success page
        unset($_SESSION['cart']);
        header("Location: order_success.php?order_id=$order_id");
        exit();
    } else {
        $error = "Error placing order: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Shoppa</title>
    <link rel="stylesheet" href="../css/global.css">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }

        .checkout-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .section-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #6E0202;
            margin: 0;
        }

        .edit-link {
            color: #C50000;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-link:hover {
            color: #6E0202;
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #6E0202;
            box-shadow: 0 0 0 3px rgba(110, 2, 2, 0.1);
        }

        textarea.form-input {
            min-height: 80px;
            resize: vertical;
        }

        .delivery-options {
            display: grid;
            gap: 15px;
        }

        .delivery-option {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delivery-option:hover {
            border-color: #C50000;
            transform: translateY(-2px);
        }

        .delivery-option input[type="radio"] {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .delivery-info {
            flex: 1;
        }

        .delivery-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .delivery-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .delivery-price {
            font-weight: bold;
            color: #6E0202;
        }

        .savings-banner {
            background: linear-gradient(135deg, #FFF3CD 0%, #FFEAA7 100%);
            border: 2px solid #FFD700;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .savings-title {
            color: #856404;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .savings-text {
            color: #856404;
            font-size: 14px;
            line-height: 1.5;
        }

        .order-summary {
            position: sticky;
            top: 20px;
        }

        .order-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }

        .item-quantity {
            color: #666;
            font-size: 14px;
        }

        .item-price {
            font-weight: bold;
            color: #6E0202;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            color: #6E0202;
        }

        .coupon-section {
            margin: 20px 0;
        }

        .coupon-form {
            display: flex;
            gap: 10px;
        }

        .coupon-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .coupon-btn {
            padding: 12px 20px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }

        .coupon-btn:hover {
            background: #5a6268;
        }

        .place-order-btn {
            width: 100%;
            padding: 15px;
            background: #C50000;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .place-order-btn:hover {
            background: #6E0202;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(110, 2, 2, 0.3);
        }

        .place-order-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .error-message {
            background: #FFEBEE;
            color: #C50000;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #FFCDD2;
        }

        .success-message {
            background: #E8F5E9;
            color: #2E7D32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #C8E6C9;
        }

        @media (max-width: 968px) {
            .checkout-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .order-summary {
                position: static;
            }
        }

        @media (max-width: 480px) {
            .checkout-section {
                padding: 20px;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .coupon-form {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="checkout-container">
            <!-- Checkout Form -->
            <div class="checkout-form">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="checkout.php" method="POST">
                    <!-- Customer Details -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <h2 class="section-title">1. Customer Details</h2>
                            <a href="../auth/register.php" class="edit-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                                Edit Details
                            </a>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-input" 
                                   value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="address" class="form-input" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-input" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <!-- Delivery Method -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <h2 class="section-title">2. Delivery Method</h2>
                        </div>
                        
                        <div class="delivery-options">
                            <label class="delivery-option">
                                <input type="radio" name="delivery_method" value="door_delivery" checked>
                                <div class="delivery-info">
                                    <div class="delivery-title">Door Delivery</div>
                                    <div class="delivery-description">Delivery between 7 days</div>
                                    <div class="delivery-price">#5,200.00</div>
                                </div>
                            </label>
                            
                            <label class="delivery-option">
                                <input type="radio" name="delivery_method" value="pickup_station">
                                <div class="delivery-info">
                                    <div class="delivery-title">Pickup Station</div>
                                    <div class="delivery-description">Save up to #4,100 - Choose from various locations</div>
                                    <div class="delivery-price">#2,200.00</div>
                                </div>
                            </label>
                        </div>
                        
                        <div class="savings-banner">
                            <div class="savings-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                SAVE UP TO #4,100
                            </div>
                            <p class="savings-text">
                                Choose a <strong>Pickup Station</strong> nearest to your location starting from #2,200. 
                                Delivery from 30 July to 15 August.
                            </p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <h2 class="section-title">Ordered Items</h2>
                        </div>
                        
                        <div class="order-items">
                            <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                                <div class="order-item">
                                    <img src="../images/Rectangle 16.png" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                                    <div class="item-details">
                                        <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                                    </div>
                                    <div class="item-price">#<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="checkout-section">
                        <h2 class="section-title">Order Summary</h2>
                        
                        <div class="summary-row">
                            <span>Subtotal (<?php echo count($_SESSION['cart']); ?> items)</span>
                            <span>#<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span>#<?php echo number_format($delivery_fee, 2); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Custom Fees</span>
                            <span>N/A</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Total</span>
                            <span>#<?php echo number_format($total_amount, 2); ?></span>
                        </div>

                        <!-- Coupon Section -->
                        <div class="coupon-section">
                            <div class="coupon-form">
                                <input type="text" placeholder="Enter coupon code" class="coupon-input">
                                <button type="button" class="coupon-btn">Apply</button>
                            </div>
                        </div>

                        <button type="submit" class="place-order-btn">Place Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>

    <script>
        // Delivery method selection
        const deliveryOptions = document.querySelectorAll('.delivery-option');
        deliveryOptions.forEach(option => {
            option.addEventListener('click', () => {
                deliveryOptions.forEach(opt => opt.style.borderColor = '#e0e0e0');
                option.style.borderColor = '#C50000';
                option.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Coupon application
        document.querySelector('.coupon-btn').addEventListener('click', function() {
            const couponInput = document.querySelector('.coupon-input');
            const couponCode = couponInput.value.trim();
            
            if (couponCode) {
                // Simulate coupon validation
                this.textContent = 'Applying...';
                this.disabled = true;
                
                setTimeout(() => {
                    alert('Coupon applied successfully! 10% discount added.');
                    this.textContent = 'Applied';
                    this.style.background = '#28a745';
                    couponInput.disabled = true;
                }, 1000);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#C50000';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>