<?php

// Include necessary files for database configuration and data handling
require_once $_SERVER['DOCUMENT_ROOT'] . '/projfinalexam/authentication/core/dbconfig.php'; // Absolute path
require_once 'main/newdatamodel.php';
require_once 'main/newhandleform.php';


if (isset($_SESSION['UserID'])) {
    // User is logged in, retrieve the UserID from the session
    $user_id = $_SESSION['UserID'];
} else {
    // If UserID is not set in the session, redirect to the login page
    $_SESSION['login_message'] = "You need to log in first.";
    $_SESSION['message_type'] = "error"; // Optionally store the message type for styling
    header("Location: ../login.php"); // Redirect to login page
    exit();
}

// Retrieve jobID and userID from the URL query string
$job_id = isset($_GET['jobID']) ? $_GET['jobID'] : null;
$created_by_hr_job = isset($_GET['userID']) ? $_GET['userID'] : null;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/jobapplication.css">
</head>

<body>
<div class="container">
    <h1>Job Application Form</h1>
    <form action="main/newhandleform.php" method="POST" enctype="multipart/form-data">
        <!-- Hidden input for job_id -->
        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">

        <!-- Hidden input for user_id for applicant -->
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

        <!-- Hidden input for created_by_hr_job -->
        <input type="hidden" name="created_by_hr_job" value="<?php echo htmlspecialchars($created_by_hr_job); ?>">

        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="position">Applied Position:</label>
            <input type="text" id="position" name="position" placeholder="Enter the position you're applying for" value="<?php echo isset($_POST['position']) ? htmlspecialchars($_POST['position']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="start-date">Earliest Possible Start Date:</label>
            <input type="date" id="start-date" name="start-date" value="<?php echo isset($_POST['start-date']) ? htmlspecialchars($_POST['start-date']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="cover-letter">Cover Letter (max 500 words):</label>
            <textarea id="cover-letter" name="cover-letter" placeholder="Write your cover letter" rows="5" maxlength="2000" required><?php echo isset($_POST['cover-letter']) ? htmlspecialchars($_POST['cover-letter']) : ''; ?></textarea>
            <small>Max 500 words</small>
        </div>

        <div class="form-group">
            <label for="resume">Upload Resume:</label>
            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
        </div>

        <button type="submit" name="jobapplicationbutton" class="submit-btn">Submit Application</button>
    </form>
</div>



</body>

</html>