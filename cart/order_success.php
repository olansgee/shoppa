<?php
// cart/order_success.php
//session_start();
require_once '../config/database.php';

if (!isset($_GET['order_id'])) {
    header("Location: ../index.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$order_sql = "SELECT o.*, u.username FROM orders o 
              JOIN users u ON o.user_id = u.id 
              WHERE o.id = $order_id";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Shoppa</title>
    <link rel="stylesheet" href="../css/global.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            text-align: center;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        .success-title {
            color: #4CAF50;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .order-number {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 500;
        }

        .order-details {
            text-align: left;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #6E0202;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #C50000;
            color: white;
        }

        .btn-primary:hover {
            background: #6E0202;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="success-container">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
            </div>
            
            <h1 class="success-title">Order Confirmed!</h1>
            <p>Thank you for your purchase. Your order has been successfully placed.</p>
            
            <div class="order-number">
                Order Number: <strong>#SH<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></strong>
            </div>
            
            <div class="order-details">
                <div class="detail-row">
                    <span>Order Date:</span>
                    <span><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="detail-row">
                    <span>Total Amount:</span>
                    <span>#<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="detail-row">
                    <span>Delivery Method:</span>
                    <span><?php echo ucfirst(str_replace('_', ' ', $order['delivery_method'])); ?></span>
                </div>
                <div class="detail-row">
                    <span>Status:</span>
                    <span style="color: #4CAF50;">Confirmed</span>
                </div>
            </div>
            
            <p>We've sent a confirmation email to your registered email address. 
               You'll receive tracking information once your order ships.</p>
            
            <div class="action-buttons">
                <a href="../products/explore.php" class="btn btn-primary">Continue Shopping</a>
                <a href="../index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>