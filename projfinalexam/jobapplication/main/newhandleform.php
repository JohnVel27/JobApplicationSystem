<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/projfinalexam/authentication/core/dbconfig.php'; // Absolute path
require_once $_SERVER['DOCUMENT_ROOT'] . '/projfinalexam/authentication/core/datamodel.php'; // Absolute path
require_once $_SERVER['DOCUMENT_ROOT'] . '/projfinalexam/authentication/core/handleform.php'; // Absolute path
require_once 'newdatamodel.php';


// Debug session variables
if (!isset($_SESSION['UserID'])) {
    echo "User not logged in. Session data: ";
    print_r($_SESSION); // Print session for debugging
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize and validate input
    $jobTitle = !empty($_POST['job-title']) ? htmlspecialchars(trim($_POST['job-title'])) : null;
    $jobDescription = !empty($_POST['job-description']) ? htmlspecialchars(trim($_POST['job-description'])) : null;
    $companyName = !empty($_POST['company-name']) ? htmlspecialchars(trim($_POST['company-name'])) : null;
    $location = !empty($_POST['location']) ? htmlspecialchars(trim($_POST['location'])) : null;
    $salaryRange = !empty($_POST['salary-range']) ? htmlspecialchars(trim($_POST['salary-range'])) : null;
    $expiryDate = !empty($_POST['expiry-date']) ? htmlspecialchars(trim($_POST['expiry-date'])) : null;
    $employmentType = !empty($_POST['employment-type']) ? htmlspecialchars(trim($_POST['employment-type'])) : null;
    $categoryID = !empty($_POST['categoryID']) ? intval($_POST['categoryID']) : null;

    $userID = intval($_SESSION['UserID']); // Get UserID from session

    // Validate required fields
    if (empty($jobTitle) || empty($jobDescription) || empty($companyName) || empty($location) || empty($employmentType) || empty($categoryID)) {
        echo "Please fill in all required fields.";
        exit();
    }


    // Insert job into the database
    $jobPosted = insertJob($pdo, $jobTitle, $jobDescription, $companyName, $location, $salaryRange, $expiryDate, $employmentType, $categoryID, $userID);

    if ($jobPosted) {
        header("Location: ../hrdh.php?message=Job+Posted+Successfully");
        exit();
    } else {
        echo "Failed to post the job. Please try again.";
    }
} else {
    echo "Invalid request.";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jobapplicationbutton'])) {
    try {
        // Retrieve and sanitize form inputs
        $firstname = htmlspecialchars(trim($_POST['firstname']));
        $lastname = htmlspecialchars(trim($_POST['lastname']));
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $phone = htmlspecialchars(trim($_POST['phone']));
        $position = htmlspecialchars(trim($_POST['position']));
        $start_date = htmlspecialchars(trim($_POST['start-date']));
        $cover_letter = htmlspecialchars(trim($_POST['cover-letter']));

        // Validate email
        if (!$email) {
            throw new Exception("Invalid email address. Please enter a valid email.");
        }

        // Retrieve job_id and user_id (created_by_applicant)
        $job_id = $_POST['job_id'] ?? null;
        $user_id = $_POST['user_id'] ?? null;
        $created_by_hr_job = $_POST['created_by_hr_job'] ?? null;

        if (!$job_id || !$user_id || !$created_by_hr_job) {
            throw new Exception("Missing job ID, user ID, or HR information. Please try again.");
        }

        // Handle file upload
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $resumeName = uniqid() . '_' . basename($_FILES['resume']['name']);
            $resumePath = $uploadDir . $resumeName;

            // Ensure upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Validate file type
            $allowedTypes = ['pdf', 'doc', 'docx'];
            $fileType = strtolower(pathinfo($resumePath, PATHINFO_EXTENSION));

            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Invalid file type. Please upload a PDF or Word document.");
            }

            // Attempt to move the uploaded file
            if (!move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath)) {
                throw new Exception("Error uploading resume. Please try again.");
            }
        } else {
            throw new Exception("Invalid resume file. Please upload a valid file.");
        }

        // Prepare data for saving
        $data = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'position' => $position,
            'start_date' => $start_date,
            'cover_letter' => $cover_letter,
            'resume_path' => $resumePath,
            'job_id' => $job_id,
            'created_by_applicant' => $user_id, // Link to applicant
            'created_by_hr_job' => $created_by_hr_job // Link to HR user
        ];

        // Call saveJobApplication function
        if (saveJobApplication($pdo, $data)) {
            // Redirect with success message
            header("Location: ../applicantdh.php?message=Job+Application+Sent+Successfully");
            exit;
        } else {
            throw new Exception("Error saving application. Please try again.");
        }
    } catch (Exception $e) {
        echo $e->getMessage(); // Display error message
    }
} else {
    echo "Invalid form submission.";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get POST data
        $messageContent = $_POST['messageContent'] ?? '';  // Get message content
        $receiverUserID = $_POST['receiverUserID'] ?? '';  // Get receiver user ID
        $senderUserID = $_SESSION['UserID'];  // Get sender (applicant) ID

        // Validate message content
        if (empty($messageContent)) {
            echo json_encode(['success' => false, 'error' => 'Message content cannot be empty']);
            exit;
        }

        // Call sendMessage function to insert the message
        $messageSent = sendMessage($senderUserID, $receiverUserID, $messageContent, $pdo);

        if ($messageSent) {
            // Respond with success (JSON format)
            echo json_encode(['success' => true]);
        } else {
            // Handle message insertion failure
            echo json_encode(['success' => false, 'error' => 'Message could not be sent']);
        }

    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Check if the file parameter is provided in the URL
if (isset($_GET['file'])) {
    // Get the file path from the URL parameter
    $filePath = urldecode($_GET['file']);
    
    // Define the full path to the file (make sure it's a valid path on your server)
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $filePath;

    // Check if the file exists
    if (file_exists($fullPath)) {
        // Set headers to force the download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($fullPath));
        
        // Read the file and send it to the browser
        readfile($fullPath);

        // Exit to ensure no further output is sent
        exit();
    } else {
        // If the file doesn't exist, show an error message
        echo "File not found!";
    }
} else {
    // If no file is specified, show an error
    echo "No file specified!";
}



?>
