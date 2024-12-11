<?php  
require_once 'core/datamodel.php'; 
require_once 'core/handleform.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <link rel="stylesheet" href="styles/register.css">
</head>
<body>

    <div class="container">
    <h2>Register</h2>
    <form action="core/handleform.php" method="POST">
        <!-- Firstname -->
        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" 
                   aria-label="Enter your first name" minlength="2" maxlength="50" required>
        </div>
        <!-- Lastname -->
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" 
                   aria-label="Enter your last name" minlength="2" maxlength="50" required>
        </div>
        <!-- Address -->
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="3" placeholder="Enter your address" 
                      aria-label="Enter your address" required></textarea>
        </div>
        <!-- Email -->
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" 
                   aria-label="Enter your email address" required>
        </div>
        <!-- User Role -->
        <div class="form-group">
            <label for="user-role">User Role:</label>
            <select id="user-role" name="user-role" aria-label="Select your role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="Applicant">Applicant</option>
                <option value="HR">HR</option>
            </select>
        </div>
        <!-- Username -->
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter a username" 
                   aria-label="Enter your username" minlength="4" maxlength="20" 
                   pattern="^[a-zA-Z0-9_]+$" title="Username can only contain letters, numbers, and underscores" required>
        </div>
        <!-- Password -->
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter a password" 
                   aria-label="Enter your password" minlength="8" 
                   pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}" 
                   title="Password must contain at least one uppercase, one lowercase, one digit, and be at least 8 characters long" required>
        </div>
        <!-- Confirm Password -->
        <div class="form-group">
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter your password" 
                   aria-label="Confirm your password" minlength="8" required>
        </div>
        <!-- Submit Button -->
        <div class="btn-container">
            <button type="submit" name="registerUserBtn" class="btn">Register</button>
        </div>
    </form>
</div>

</body>
</html>

