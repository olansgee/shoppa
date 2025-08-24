<?php
// about.php
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Shoppa</title>
    <link rel="stylesheet" href="css/about.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="aboutpage">
        <div class="abouthead">
            <div class="aba"><a href="index.php"> &lt;</a></div> 
            <div class="ab">About Us</div>
        </div>
        <div class="aboutdetails">
            <!-- ... about us content from your original about.html ... -->
            <div class="welcome">
               <h2> <b> Welcome to Shoppa — Where Quality Meets Convenience. </b></h2>
            </div>
            <div class="welcomedetails1">
                <p>At Shoppa, we believe online shopping should be easy, trustworthy, and enjoyable. That's why we've built a platform that brings you the best products from around the world — all in one place, just a click away.</p>
            </div>
            <!-- ... rest of the about content ... -->
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>