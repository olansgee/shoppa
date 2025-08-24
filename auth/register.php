<?php
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($name)) {
        $error = "All fields marked with * are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if username or email already exists
        $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username or email already exists.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO users (username, email, password, name, address, phone) 
                    VALUES ('$username', '$email', '$hashed_password', '$name', '$address', '$phone')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                // Clear form
                $_POST = array();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Shoppa</title>
  <link rel="stylesheet" href="../css/register.css">
  <style>
    .error {
      color: #C50000;
      margin-bottom: 15px;
      text-align: center;
      padding: 10px;
      background-color: #FFEBEE;
      border-radius: 5px;
      border: 1px solid #C50000;
    }
    .success {
      color: #2E7D32;
      margin-bottom: 15px;
      text-align: center;
      padding: 10px;
      background-color: #E8F5E9;
      border-radius: 5px;
      border: 1px solid #2E7D32;
    }
    .register-container {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      width: 400px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.2);
      text-align: center;
    }
    .register-container h2 {
      margin-bottom: 20px;
      color: #6E0202;
      font-family: Satoshi, sans-serif;
    }
    .input-group {
      text-align: left;
      margin-bottom: 15px;
    }
    .input-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
      color: #555;
      font-family: Satoshi, sans-serif;
    }
    .input-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      font-family: Satoshi, sans-serif;
    }
    .input-group input:focus {
      outline: none;
      border-color: #6E0202;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #C50000;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
      font-family: Satoshi, sans-serif;
      font-weight: 500;
    }
    button:hover {
      background: #6E0202;
    }
    .login-text {
      margin-top: 15px;
      font-size: 14px;
      font-family: Satoshi, sans-serif;
    }
    .login-text a {
      color: #C50000;
      text-decoration: none;
    }
    .login-text a:hover {
      text-decoration: underline;
    }
    body {
      margin: 0;
      padding: 0;
      font-family: Satoshi, sans-serif;
      background: linear-gradient(135deg, #6E0202 0%, #C50000 100%);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .required::after {
      content: " *";
      color: #C50000;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Create Account</h2>
    
    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <div class="input-group">
        <label for="name" class="required">Full Name</label>
        <input type="text" id="name" name="name" placeholder="Enter your full name" 
               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
      </div>
      
      <div class="input-group">
        <label for="username" class="required">Username</label>
        <input type="text" id="username" name="username" placeholder="Choose a username" 
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
      </div>
      
      <div class="input-group">
        <label for="email" class="required">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" 
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
      </div>
      
      <div class="input-group">
        <label for="password" class="required">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>
      </div>
      
      <div class="input-group">
        <label for="confirm_password" class="required">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
      </div>
      
      <div class="input-group">
        <label for="address">Address</label>
        <input type="text" id="address" name="address" placeholder="Enter your address" 
               value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
      </div>
      
      <div class="input-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" 
               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
      </div>
      
      <button type="submit">Register</button>
      <p class="login-text">Already have an account? <a href="login.php">Login here</a></p>
    </form>
  </div>

  <script>
    // Client-side validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }
      
      if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long!');
        return false;
      }
    });
  </script>
</body>
</html>