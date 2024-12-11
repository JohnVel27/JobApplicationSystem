<?php
require_once "dbconfig.php";


function insertNewUser($pdo, $firstname, $lastname, $address, $email, $userRole, $username, $hashedPassword) {
    // Check if the username or email already exists
    $checkUserSql = "SELECT * FROM users WHERE username = ? OR EmailAddress = ?";
    $checkUserStmt = $pdo->prepare($checkUserSql);
    $checkUserStmt->execute([$username, $email]);

    if ($checkUserStmt->rowCount() == 0) {
        // Insert new user account
        $sql = "INSERT INTO Users (Firstname, Lastname, Address, EmailAddress, UserRole, Username, Password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // Execute the query with the hashed password
        $executeQuery = $stmt->execute([$firstname, $lastname, $address, $email, $userRole, $username, $hashedPassword]);

        if ($executeQuery) {
            return true; // Return true if the insertion is successful
        } else {
            $_SESSION['register_message'] = "An error occurred while creating the account"; // Error message if query fails
        }
    } else {
        $_SESSION['register_message'] = "Username or email already exists"; // Message if the username or email already exists
    }
    return false; // Return false if the insertion fails or user exists
}


function loginUser($pdo, $username, $password) {
    // SQL query to select user data, including UserID
    $query = "SELECT UserID, Firstname, EmailAddress, UserRole, Username, Password FROM users WHERE Username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);

    // Fetch user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if ($user) {
        // Verify the password
        if (password_verify($password, $user['Password'])) {
            // Return user details including UserID
            return [
                'UserID' => $user['UserID'], // Include UserID in the return array
                'firstname' => $user['Firstname'],
                'email' => $user['EmailAddress'],
                'role' => $user['UserRole'],
                'username' => $user['Username']
            ];
        } else {
            return 'incorrect_password';
        }
    } else {
        return 'username_not_exist';
    }
}


function getAllUsers($pdo) {
    $sql = "SELECT * FROM Users"; // Select all users from the Users table
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute();

    if ($executeQuery) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array
    }

    return []; // Return an empty array if the query fails
}


function getUserByID($pdo, $user_id) {
    $sql = "SELECT * FROM Users WHERE UserID = ?"; // Select user by ID
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$user_id]);

    if ($executeQuery) {
        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single result as an associative array
    }

    return null; // Return null if the query fails or no record is found
}

?>