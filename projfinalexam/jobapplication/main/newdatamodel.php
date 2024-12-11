<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/projfinalexam/authentication/core/dbconfig.php'; // Absolute path


// Function to insert a new job post
function insertJob($pdo, $jobTitle, $jobDescription, $companyName, $location, $salaryRange, $expiryDate, $employmentType, $categoryID, $userID) {
    // SQL query to insert the job data into the Jobs table
    $sql = "INSERT INTO Jobs (JobTitle, JobDescription, CompanyName, Location, SalaryRange, PostingDate, ExpiryDate, EmploymentType, Status, CategoryID, UserID)
            VALUES (?, ?, ?, ?, ?, CURRENT_DATE, ?, ?, 'Active', ?, ?)";

    // Prepare the SQL query
    $stmt = $pdo->prepare($sql);
    
    // Execute the query with the provided data
    $executeQuery = $stmt->execute([$jobTitle, $jobDescription, $companyName, $location, $salaryRange, $expiryDate, $employmentType, $categoryID, $userID]);
    
    // Return the last inserted JobID or false on failure
    return $executeQuery ? $pdo->lastInsertId() : false; 
}

function saveJobApplication($pdo, $data) {
    // Check if the applicant exists in the Users table
    $userCheckQuery = "SELECT COUNT(*) FROM Users WHERE UserID = :created_by_applicant";
    $stmt = $pdo->prepare($userCheckQuery);
    $stmt->bindParam(':created_by_applicant', $data['created_by_applicant']);
    $stmt->execute();
    $applicantExists = $stmt->fetchColumn();

    // If the applicant doesn't exist, return false
    if ($applicantExists == 0) {
        echo "Applicant not found. Please ensure you're logged in and try again.";
        return false;
    }

    // Check if the HR user exists in the Users table
    $hrCheckQuery = "SELECT COUNT(*) FROM Users WHERE UserID = :created_by_hr_job";
    $stmt = $pdo->prepare($hrCheckQuery);
    $stmt->bindParam(':created_by_hr_job', $data['created_by_hr_job']);
    $stmt->execute();
    $hrExists = $stmt->fetchColumn();

    // If the HR user doesn't exist, return false
    if ($hrExists == 0) {
        echo "HR user not found. Please contact the administrator.";
        return false;
    }

    // Insert job application into the JobApplicationForm table
    $query = "INSERT INTO JobApplicationForm (
                  Firstname, Lastname, Email, PhoneNumber, AppliedPosition,
                  EarliestStartDate, CoverLetter, ResumePath, DateApplied,
                  JobID, CreatedByApplicant, CreatedByHRJob
              )
              VALUES (
                  :firstname, :lastname, :email, :phone, :position,
                  :start_date, :cover_letter, :resume_path, CURRENT_TIMESTAMP,
                  :job_id, :created_by_applicant, :created_by_hr_job
              )";

    $stmt = $pdo->prepare($query);

    // Bind parameters from $data array
    $stmt->bindParam(':firstname', $data['firstname']);
    $stmt->bindParam(':lastname', $data['lastname']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':position', $data['position']);
    $stmt->bindParam(':start_date', $data['start_date']);
    $stmt->bindParam(':cover_letter', $data['cover_letter']);
    $stmt->bindParam(':resume_path', $data['resume_path']);
    $stmt->bindParam(':job_id', $data['job_id']);
    $stmt->bindParam(':created_by_applicant', $data['created_by_applicant']);
    $stmt->bindParam(':created_by_hr_job', $data['created_by_hr_job']);

    // Execute the insert statement
    if ($stmt->execute()) {
        echo "Job application submitted successfully.";
        return true;
    } else {
        echo "Error saving application. Please try again.";
        return false;
    }
}


function getJobDetails($pdo) {
    // SQL query to get job details
    $sql = "SELECT 
                j.JobID, 
                j.JobTitle, 
                j.CompanyName, 
                j.Location, 
                j.JobDescription, 
                j.SalaryRange, 
                j.PostingDate, 
                j.ExpiryDate, 
                j.EmploymentType, 
                j.Status, 
                c.CategoryName, 
                u.UserID
            FROM Jobs j
            LEFT JOIN JobCategory c ON j.CategoryID = c.CategoryID
            LEFT JOIN Users u ON j.UserID = u.UserID";

    // Prepare the query
    $stmt = $pdo->prepare($sql);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch all the results as an associative array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the results
    return $result;
}






// Function to fetch all job categories from the database
function getAllJobCategories($pdo) {
    // Prepare the SQL query to get all job categories
    $query = "SELECT * FROM jobcategory"; // Adjust the table name if it's different
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}

// Function to fetch job listings from the database
function getJobListings($conn) {
    $sql = "SELECT JobTitle, CompanyName, Location, JobDescription, SalaryRange, PostingDate, ExpiryDate, EmploymentType, Status FROM Jobs";
    $result = $conn->query($sql);
    return $result;
}


function fetchApplicantsForHR($pdo, $hrUserID) {
    try {
        // Query to fetch applicants for jobs created by the logged-in HR user
        $query = "SELECT JA.ApplicationID, JA.Firstname, JA.Lastname, JA.Email,JA.PhoneNumber, JA.AppliedPosition,JA.EarliestStartDate,JA.CoverLetter,JA.ResumePath,JA.DateApplied,JA.CreatedByApplicant,JA.CreatedByHRJob
                  FROM JobApplicationForm JA
                  JOIN Jobs J ON JA.JobID = J.JobID
                  WHERE JA.CreatedByHRJob = :hrUserID";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['hrUserID' => $hrUserID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching applicants: " . $e->getMessage());
    }
}

// Get the logged-in HR's user ID
$currentUserID = $_SESSION['UserID'] ?? null;

if (!$currentUserID) {
    die("You are not authorized to view this page.");
}

function getApplicationDetails($application_id) {
    global $pdo;
    try {
        $query = "SELECT JA.ApplicationID, JA.Firstname, JA.Lastname, JA.Email, JA.PhoneNumber, JA.AppliedPosition, JA.EarliestStartDate, JA.CoverLetter, JA.ResumePath, JA.Status
                  FROM JobApplicationForm JA
                  WHERE JA.ApplicationID = :application_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['application_id' => $application_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching application details: " . $e->getMessage());
    }
}

function updateApplicationStatus($application_id, $status) {
    global $pdo;
    try {
        $update_query = "UPDATE JobApplicationForm SET Status = :status WHERE ApplicationID = :application_id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute(['application_id' => $application_id, 'status' => $status]);
    } catch (PDOException $e) {
        die("Error updating application status: " . $e->getMessage());
    }
}

function handleFormSubmission($application_id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['accept'])) {
            // Update the status to "Approved"
            updateApplicationStatus($application_id, 'Approved');
        } elseif (isset($_POST['decline'])) {
            // Update the status to "Not Approved"
            updateApplicationStatus($application_id, 'Not Approved');
        }
        // Refresh the page to reflect the updated status
        header("Location: hrdh.php");
        exit;
    }
}

function getJobApplicationsByApplicant($pdo, $userId) {
    try {
        // Query to fetch applied positions and their status for the logged-in user
        $query = "SELECT AppliedPosition, Status 
                  FROM JobApplicationForm 
                  WHERE CreatedByApplicant = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch all matching records
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log the error message
        error_log("Database Query Error: " . $e->getMessage());
        return [];
    }
}

function getHRUserIdForFollowUp($pdo, $applicantUserId) {
    try {
        $query = "
            SELECT DISTINCT j.CreatedByHRJob, 
                            CONCAT(u.Firstname, ' ', u.Lastname) AS hr_name
            FROM JobApplicationForm j
            JOIN Users u ON j.CreatedByHRJob = u.UserID
            WHERE j.CreatedByApplicant = :applicantUserId
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['applicantUserId' => $applicantUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getHRUserIdForFollowUp: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}

function getApplicantUserIdForFollowUp($pdo, $HrUserId) {
    try {
        $query = "
            SELECT DISTINCT j.CreatedByApplicant, 
                            CONCAT(u.Firstname, ' ', u.Lastname) AS applicant_name
            FROM JobApplicationForm j
            JOIN Users u ON j.CreatedByApplicant = u.UserID
            WHERE j.CreatedByHRJob = :hrUserId
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['hrUserId' => $HrUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getApplicantUserIdForFollowUp: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}

function sendMessage($applicantID, $receiverUserID, $messageContent, $pdo) {
    try {
        $query = "INSERT INTO messages (SenderUserID, ReceiverUserID, MessageContent, DateSent) 
                  VALUES (:senderID, :receiverID, :messageContent, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':senderID', $applicantID, PDO::PARAM_INT);
        $stmt->bindParam(':receiverID', $receiverUserID, PDO::PARAM_INT);
        $stmt->bindParam(':messageContent', $messageContent, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Message insertion failed: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    } catch (PDOException $e) {
        error_log("Error in sendMessage: " . $e->getMessage());
        return false;
    }
}


function getConversation($applicantID, $hrID, $pdo) {
    try {
        $sql = "SELECT m.MessageID, m.SenderUserID, m.ReceiverUserID, m.MessageContent, m.DateSent 
                FROM Messages m
                JOIN JobApplicationForm j 
                    ON j.CreatedByApplicant = :applicantID AND j.CreatedByHRJob = :hrID
                WHERE (m.SenderUserID = :applicantID AND m.ReceiverUserID = :hrID)
                OR (m.SenderUserID = :hrID AND m.ReceiverUserID = :applicantID)
                ORDER BY m.DateSent ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':applicantID', $applicantID, PDO::PARAM_INT);
        $stmt->bindParam(':hrID', $hrID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getConversation: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}

function getConversationHr($hrID, $applicantID, $pdo) {
    try {
        $sql = "
            SELECT m.MessageID, 
                   m.SenderUserID, 
                   m.ReceiverUserID, 
                   m.MessageContent, 
                   m.DateSent 
            FROM Messages m
            JOIN JobApplicationForm j 
                ON j.CreatedByHRJob = :hrID AND j.CreatedByApplicant = :applicantID
            WHERE (m.SenderUserID = :hrID AND m.ReceiverUserID = :applicantID)
               OR (m.SenderUserID = :applicantID AND m.ReceiverUserID = :hrID)
            ORDER BY m.DateSent ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':hrID', $hrID, PDO::PARAM_INT);
        $stmt->bindParam(':applicantID', $applicantID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getConversation: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}


// Function to search jobs based on title or company
function searchJobs($searchTerm) {
    global $pdo;
    
    // Sanitize the search term to prevent SQL injection
    $searchTerm = '%' . $searchTerm . '%';
    
    // Prepare the SQL query to search by job title or company name
    $sql = "SELECT * FROM Jobs WHERE (JobTitle LIKE :searchTerm OR CompanyName LIKE :searchTerm) AND Status = 'Active'";  // You can adjust the status or other conditions as needed
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch all results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHRNameByID($hrID, $pdo) {
    try {
        $query = "SELECT Firstname, Lastname FROM Users WHERE UserID = :hrID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':hrID', $hrID, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return [
                'Firstname' => $user['Firstname'],
                'Lastname'  => $user['Lastname']
            ];
        } else {
            return [
                'Firstname' => 'Not found',
                'Lastname'  => 'Not found'
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching HR Name: " . $e->getMessage());
        return [
            'Firstname' => 'Error',
            'Lastname'  => 'Error'
        ];
    }
}

function getApplicantNameByID($applicantId, $pdo) {
    try {
        $query = "SELECT Firstname, Lastname FROM Users WHERE UserID = :applicantId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return [
                'Firstname' => $user['Firstname'],
                'Lastname'  => $user['Lastname']
            ];
        } else {
            return [
                'Firstname' => 'Not found',
                'Lastname'  => 'Not found'
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching HR Name: " . $e->getMessage());
        return [
            'Firstname' => 'Error',
            'Lastname'  => 'Error'
        ];
    }
}

function getAllJobs($pdo) {
    try {
        // SQL query to join Jobs, JobCategory, and Users tables
        $sql = "
            SELECT 
                j.JobID, 
                j.JobTitle, 
                j.JobDescription, 
                j.CompanyName, 
                j.Location, 
                j.SalaryRange, 
                j.PostingDate, 
                j.ExpiryDate, 
                j.EmploymentType, 
                j.Status, 
                jc.CategoryName, 
                u.FirstName AS PostedByFirstName, 
                u.LastName AS PostedByLastName
            FROM 
                Jobs j
            LEFT JOIN 
                JobCategory jc ON j.CategoryID = jc.CategoryID
            LEFT JOIN 
                Users u ON j.UserID = u.UserID
        ";

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // Fetch all results as an associative array
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $jobs;
    } catch (PDOException $e) {
        // Handle any errors that occur during the query execution
        echo "Error: " . $e->getMessage();
        return [];
    }
}





?>