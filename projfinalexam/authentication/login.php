<?php  
require_once 'core/datamodel.php'; 
require_once 'core/handleform.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="core/handleform.php" method="POST">
            <!-- Username -->
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <!-- Password -->
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <!-- Submit Button -->
            <div class="btn-container">
                <button type="submit" name= "loginUserBtn"  class="btn">Login</button>
            </div>
        </form>
    </div>
</body>
</html>