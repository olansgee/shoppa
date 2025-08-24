<?php
// auth/login.php
require_once '../config/database.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    try {
        // First try to select with is_admin column
        $sql = "SELECT id, username, password, is_admin FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'] ?? false; // Use false if column doesn't exist
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that username.";
        }
    } catch (mysqli_sql_exception $e) {
        // If is_admin column doesn't exist, try without it
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            $sql = "SELECT id, username, password FROM users WHERE username = '$username'";
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_admin'] = false; // Default to false
                    header("Location: ../index.php");
                    exit();
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No user found with that username.";
            }
        } else {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="../css/login.css"> 
  <style>
    .error {
      color: red;
      margin-bottom: 15px;
      text-align: center;
      padding: 10px;
      background-color: #ffeeee;
      border: 1px solid red;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
      </div>
      
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>
      
      <button type="submit">Login</button>
      <p class="signup-text">Don't have an account? <a href="register.php">Sign up</a></p>
    </form>
  </div>
</body>
</html>