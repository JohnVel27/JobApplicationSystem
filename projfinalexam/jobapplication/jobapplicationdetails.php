<?php
// Include necessary files for database configuration and data handling
require_once '../authentication/core/dbconfig.php'; // Database connection
require_once '../authentication/core/datamodel.php'; // Data model functions
require_once '../authentication/core/handleform.php'; // Handle form
require_once 'main/newdatamodel.php';


// Check if the application_id is passed via the URL
if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];
    $application = getApplicationDetails($application_id); // Get application details

    // Handle Accept or Decline action
    handleFormSubmission($application_id);
} else {
    echo "Application ID not provided.";
    exit;
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Details</title>
    <link rel="stylesheet" href="styles/jobapplicationdetails.css">
</head>
<body>

<?php if (!empty($application)): ?>
    <div class="container">
        <h2>Job Application Details</h2>
        <div class="form-group">
            <label>Firstname:</label>
            <p><?= htmlspecialchars($application['Firstname']) ?></p>
        </div>
        <div class="form-group">
            <label>Lastname:</label>
            <p><?= htmlspecialchars($application['Lastname']) ?></p>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <p><?= htmlspecialchars($application['Email']) ?></p>
        </div>
        <div class="form-group">
            <label>Phone Number:</label>
            <p><?= htmlspecialchars($application['PhoneNumber']) ?></p>
        </div>
        <div class="form-group">
            <label>Applied Position:</label>
            <p><?= htmlspecialchars($application['AppliedPosition']) ?></p>
        </div>
        <div class="form-group">
            <label>Earliest Possible Start Date:</label>
            <p><?= htmlspecialchars($application['EarliestStartDate']) ?></p>
        </div>
        <div class="form-group">
            <label>Cover Letter:</label>
            <p><?= nl2br(htmlspecialchars($application['CoverLetter'])) ?></p>
        </div>
        <div class="form-group">
            <label>Resume:</label>
            <a href="main/newhandleform.php?file=<?= urlencode($application['ResumePath']) ?>" class="btn download">Download Resume</a>
        </div>
        <form method="POST">
        <div class="btn-container">
            <button type="submit" name="accept" class="btn accept">Accept</button>
            <button type="submit" name="decline" class="btn decline">Decline</button>
        </div>
</form>
    </div>
<?php else: ?>
    <p>No application found.</p>
<?php endif; ?>

</body>
</html>