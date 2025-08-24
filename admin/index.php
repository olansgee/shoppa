<?php
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit();
}

// Get stats for dashboard
$users_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$products_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$orders_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$revenue = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;

// Get recent orders
$recent_orders = mysqli_query($conn, "SELECT o.*, u.username FROM orders o 
                                     JOIN users u ON o.user_id = u.id 
                                     ORDER BY o.created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Shoppa</title>
    <style>
        .admin-dashboard {
            padding: 20px;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0;
            color: #6E0202;
        }
        .stat-card p {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0 0;
        }
        .recent-orders {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .recent-orders h2 {
            margin-top: 0;
            color: #6E0202;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th, .order-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-table th {
            background-color: #f8f8f8;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .admin-menu {
            background: #6E0202;
            padding: 15px;
            margin-bottom: 20px;
        }
        .admin-menu a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            padding: 10px;
        }
        .admin-menu a:hover {
            background-color: #C50000;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="admin-menu">
        <a href="index.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
    </div>
    
    <div class="admin-dashboard">
        <h1>Admin Dashboard</h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?php echo $users_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $products_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p><?php echo $orders_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p>#<?php echo number_format($revenue, 2); ?></p>
            </div>
        </div>
        
        <div class="recent-orders">
            <h2>Recent Orders</h2>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($recent_orders)): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo $order['username']; ?></td>
                            <td>#<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>