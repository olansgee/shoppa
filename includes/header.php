<?php
// includes/header.php
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>

<header>
    <div class="headimage">
        <img src="/shoppa-main/images/Frame 20.png" alt="Shoppa Banner">
    </div>
</header>

<div class="headshoppa">
    <div class="logo">
        <a href="/shoppa-main/index.php">
            <img src="/shoppa-main/images/Vector (1).png" alt="Shoppa Logo">
        </a>
    </div>
    
    <div class="shoppasearch">
        <form action="/shoppa-main/products/explore.php" method="GET">
            <input type="text" id="searchInput" name="q" placeholder="Search item, brand, category...">
        </form>
    </div>
    
    <div class="Search">
        <button type="button" onclick="document.querySelector('.shoppasearch form').submit()">Search</button>
    </div>
    
    <div class="cart">
        <div class="cartimg">
            <img src="/shoppa-main/images/bag.png" alt="Shopping Cart">
        </div>
        <div class="carttext">
            <a href="/shoppa-main/cart/cart.php">Cart 
                <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <span>(<?php echo count($_SESSION['cart']); ?>)</span>
                <?php endif; ?>
            </a>
        </div>
    </div>
    
    <div class="About">
        <a href="/shoppa-main/about.php">About Us</a>
    </div>
    
    <?php if($isLoggedIn): ?>
        <div class="user-menu">
            <span>Hello, <?php echo htmlspecialchars($username); ?></span>
            <div class="dropdown-content">
                <a href="/shoppa-main/auth/logout.php">Logout</a>
                <?php if($isAdmin): ?>
                    <a href="/shoppa-main/admin/index.php">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="Login">
            <a href="/shoppa-main/auth/login.php">Login</a>
        </div>
        <div class="Register">
            <a href="/shoppa-main/auth/register.php">Register</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .user-menu {
        position: relative;
        display: inline-block;
        color: white;
        cursor: pointer;
    }
    
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1000;
        right: 0;
    }
    
    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 14px;
    }
    
    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }
    
    .user-menu:hover .dropdown-content {
        display: block;
    }
</style>