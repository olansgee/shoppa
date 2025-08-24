<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<div class="headshoppa">
    <div class="logo">
        <a href="/index.php"><img src="/images/Vector (1).png" alt="shoppa logo"></a>
    </div>
    <div class="shoppasearch">
        <form action="/products/explore.php" method="GET">
            <input type="text" id="searchInput" name="q" placeholder="Search item,brand,category...">
        </form>
    </div>
    <div class="Search">
        <button onclick="document.querySelector('form').submit()">Search</button>
    </div>
    <div class="cart">
        <div class="cartimg">
            <img src="/images/bag.png" alt="cart">
        </div>
        <div class="carttext">
            <a href="/cart/cart.php">Cart
                <?php 
                if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                    echo '<span>('.count($_SESSION['cart']).')</span>';
                }
                ?>
            </a>
        </div>
    </div>
    <div class="About">
        <a href="/about.php">About Us</a>
    </div>
    <?php if($isLoggedIn): ?>
        <div class="user-menu">
            <span>Hello, <?php echo $username; ?></span>
            <div class="dropdown">
                <a href="/auth/logout.php">Logout</a>
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="/admin/index.php">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="Login">
            <a href="/auth/login.php">Login</a>
        </div>
        <div class="Register">
            <a href="/auth/register.php">Register</a>
        </div>
    <?php endif; ?>
</div>