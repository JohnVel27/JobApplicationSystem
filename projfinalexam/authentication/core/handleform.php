<?php
require_once "dbconfig.php";
require_once "datamodel.php";
require_once "validate.php";


// Registration Handler
if (isset($_POST['registerUserBtn'])) {
    // Retrieve and sanitize input data
    $firstname = sanitizeInput($_POST['firstname']);
    $lastname = sanitizeInput($_POST['lastname']);
    $address = sanitizeInput($_POST['address']);
    $email = sanitizeInput($_POST['email']);
    $userRole = sanitizeInput($_POST['user-role']);
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validate input fields
    if (!empty($firstname) && !empty($lastname) && !empty($address) && !empty($email) && !empty($userRole) && !empty($username) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            if (validatePassword($password)) {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Use the insertNewUser function to handle the insertion
                $insertSuccess = insertNewUser($pdo, $firstname, $lastname, $address, $email, $userRole, $username, $hashedPassword);

                if ($insertSuccess) {
                    $_SESSION['register_message'] = "Registration successful!";
                    $_SESSION['message_type'] = "success";
                    header("Location: ../register.php");
                    exit();
                } else {
                    $_SESSION['register_message'] = "Username or email already exists. Please try again.";
                    $_SESSION['message_type'] = "error";
                    header("Location: ../register.php");
                }
            } else {
                $_SESSION['register_message'] = "Password must contain uppercase, lowercase, numbers, and be at least 8 characters.";
                $_SESSION['message_type'] = "error";
                header("Location: ../register.php");
            }
        } else {
            $_SESSION['register_message'] = "Passwords do not match!";
            $_SESSION['message_type'] = "error";
            header("Location: ../register.php");
        }
    } else {
        $_SESSION['register_message'] = "All fields are required!";
        $_SESSION['message_type'] = "error";
        header("Location: ../register.php");
    }
}



if (isset($_POST['loginUserBtn'])) {
    // Sanitize user input
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];  // Plain text password

    // Validate input
    if (!empty($username) && !empty($password)) {
        // Attempt to login using the loginUser function
        $loginResult = loginUser($pdo, $username, $password);

        if (is_array($loginResult)) {
            // Login successful, set session variables
            $_SESSION['username'] = $loginResult['username']; // Store username in session
            $_SESSION['role'] = $loginResult['role'];          // Store role in session
            $_SESSION['firstname'] = $loginResult['firstname']; // Store first name in session
            $_SESSION['email'] = $loginResult['email'];         // Store email in session
            $_SESSION['UserID'] = $loginResult['UserID'];       // Store UserID in session

            // Redirect based on user role
            switch ($loginResult['role']) {
                case 'Applicant':
                    header("Location: ../../jobapplication/applicantdh.php");
                    break;
                case 'HR':
                    header("Location: ../../jobapplication/hrdh.php");
                    break;
                default:
                    $_SESSION['login_message'] = "Invalid role assigned. Contact the administrator.";
                    $_SESSION['message_type'] = "error";
                    header("Location: ../login.php");
                    break;
            }
            exit();
        } elseif ($loginResult === 'incorrect_password') {
            // Password did not match
            $_SESSION['login_message'] = "Incorrect password. Please try again.";
            $_SESSION['message_type'] = "error";
        } elseif ($loginResult === 'username_not_exist') {
            // Username not found
            $_SESSION['login_message'] = "Username does not exist.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['login_message'] = "All fields are required!";
        $_SESSION['message_type'] = "error";
    }

    // Redirect back to the login page after processing
    header("Location: ../login.php");
    exit();
}





// Logout handler
if (isset($_GET['logoutAUser'])) {
    // Unset all session variables
    session_unset();
    session_destroy();
    
    // Set headers to prevent back navigation after logout
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.

    // Redirect to the login page
    header('Location: ../home.php');
    exit();
}


?>