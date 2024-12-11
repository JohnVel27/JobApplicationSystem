<?php
// Include necessary files for database configuration and data handling
require_once '../authentication/core/dbconfig.php'; // Database connection
require_once '../authentication/core/datamodel.php'; // Data model functions
require_once '../authentication/core/handleform.php'; // Handle form
require_once 'main/newdatamodel.php';

// Fetch user details from the session
$firstname = $_SESSION['firstname'];
$email = $_SESSION['email'];
$userId = $_SESSION['UserID'];



// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    // If not logged in, redirect to login page
    header("Location: ../login.php");
    exit();
}

$jobDetails = getJobDetails($pdo);


// Fetch job application details for the logged-in user
$jobApplications = getJobApplicationsByApplicant($pdo, $userId);

$applicantID = $_SESSION['UserID']; // Retrieve applicant ID from session
$hrContacts = getHRUserIdForFollowUp($pdo, $applicantID); // Fetch HR contacts

// Check if a search term is submitted
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Get job details based on the search term
$jobDetails = [];
if ($searchTerm) {
    $jobDetails = searchJobs($searchTerm);
} else {
    // Default: Fetch all active jobs
    $stmt = $pdo->query("SELECT * FROM Jobs WHERE Status = 'Active'");
    $jobDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/applicant.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            Applicant 
        </div>
        <div class="profile">
            <img src="https://via.placeholder.com/80" alt="Profile Picture">
            <h4><?php echo htmlspecialchars($firstname); ?></h4>
            <p><?php echo htmlspecialchars($email); ?></p>
        </div>
        <ul>
            <li><a href="#" onclick="showContent('job-listings')"><i class="fas fa-search"></i> Job Listings</a></li>
            <li><a href="#" onclick="showContent('approval-status')"><i class="fas fa-clipboard-check"></i> Approval Status</a></li>
            <li><a href="#" onclick="showContent('chat')"><i class="fas fa-comments"></i> Chat</a></li>
            <li><a href="../authentication/core/handleform.php?logoutAUser=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

<!-- Display the job listings -->
<div id="job-listings" class="main-content">
    <div class="job-listing-container">
        <h2 class="text-center mb-4">Job Listings</h2>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET" action="applicantdh.php">
                <input type="text" name="search" id="searchInput" placeholder="Search for a job title or company..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Job List -->
        <ul class="job-list" id="jobList">
            <?php
            // Loop through the job listings and display each one
            if ($jobDetails) {
                foreach ($jobDetails as $job) {
                    echo '<li class="job-item">';
                    echo '<h3 class="job-title">' . htmlspecialchars($job['JobTitle']) . '</h3>';
                    echo '<p class="job-company">' . htmlspecialchars($job['CompanyName']) . '</p>';
                    echo '<p class="job-location">' . htmlspecialchars($job['Location']) . '</p>';
                    echo '<button class="apply-btn" onclick="openModal(\'' . htmlspecialchars($job['JobID']) . '\', \'' . htmlspecialchars($job['JobTitle']) . '\', \'' . htmlspecialchars($job['CategoryID']) . '\', \'' . htmlspecialchars($job['Location']) . '\', \'' . htmlspecialchars($job['CompanyName']) . '\', \'' . htmlspecialchars($job['JobDescription']) . '\', \'' . htmlspecialchars($job['SalaryRange']) . '\', \'' . htmlspecialchars($job['PostingDate']) . '\', \'' . htmlspecialchars($job['ExpiryDate']) . '\', \'' . htmlspecialchars($job['EmploymentType']) . '\', \'' . htmlspecialchars($job['Status']) . '\', \'' . htmlspecialchars($job['UserID']) . '\')">View</button>';
                    echo '</li>';
                }
            } else {
                echo "<p>No job listings found.</p>";
            }
            ?>
        </ul>
    </div>
</div>

            <!-- Modal Overlay -->
            <div class="modal-overlay" id="modalOverlay" style="display: none;">
                <div class="modal-content">
                    <button class="close-btn" onclick="closeModal()">X</button>
                    <h2 class="job-title" id="modalJobTitle"></h2>
                    <p><strong>Job Category:</strong> <span id="modalJobCategory"></span></p>
                    <p><strong>Job Description:</strong> <span id="modalJobDescription"></span></p>
                    <p><strong>Company:</strong> <span id="modalCompanyName"></span></p>
                    <p><strong>Location:</strong> <span id="modalJobLocation"></span></p>
                    <p><strong>Salary Range:</strong> <span id="modalSalaryRange"></span></p>
                    <p><strong>Posting Date:</strong> <span id="modalPostingDate"></span></p>
                    <p><strong>Expiry Date:</strong> <span id="modalExpiryDate"></span></p>
                    <p><strong>Employment Type:</strong> <span id="modalEmploymentType"></span></p>
                    <p><strong>Status:</strong> <span id="modalStatus"></span></p>

                    <!-- Hidden fields for jobID and userID -->
                    <input type="hidden" name="jobID" id="modalJobID" value="">
                    <input type="hidden" name="userID" id="modalUserID" value="">

                    <!-- Apply Now button -->
                    <a href="jobapplicationform.php" id="applyNowLink" class="apply-btn">Apply Now</a>
                </div>
            </div>




        <!-- Approval Status Section -->
    <div id="approval-status" class="main-content" style="display: none;">
    <h2 class="text-center mb-4">Approval Status</h2>
        <div class="container content-container">
            
            <?php if (!empty($jobApplications)) : ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Applied Position</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobApplications as $application) : ?>
                        <tr>
                            <td><?= htmlspecialchars($application['AppliedPosition']) ?></td>
                            <td>
                                <span class="badge <?= $application['Status'] === 'Approved' ? 'badge-approved' : 'badge-not-approved' ?>">
                                    <?= htmlspecialchars($application['Status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>You have not applied for any jobs yet.</p>
        <?php endif; ?>
        </div>
    </div>



    <div id="chat" class="main-content" style="display: none;">
    <h1>Chat Section</h1>
    <div class="container">
        <!-- Sidebar -->
        <div class="left">
            <div class="people">
                <?php if (!empty($hrContacts)): ?>
                    <?php foreach ($hrContacts as $person): ?>
                        <a 
                            href="http://localhost/projfinalexam/jobapplication/chat.php?CreatedByHRJob=<?= htmlspecialchars($person['CreatedByHRJob']) ?>#chat" 
                            class="person" 
                            data-chat="person<?= htmlspecialchars($person['CreatedByHRJob']) ?>">
                            <span class="name"><?= htmlspecialchars($person['hr_name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No HR contacts available for follow-up.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="scripts/applicantdh.js"></script>
</body>
</html>