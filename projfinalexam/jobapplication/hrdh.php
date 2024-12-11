<?php

// Include necessary files for database configuration and data handling
require_once $_SERVER['DOCUMENT_ROOT'] . '/projfinalexam/authentication/core/dbconfig.php'; // Absolute path
require_once 'main/newdatamodel.php';


// Check if the user is logged in by validating session
if (!isset($_SESSION['firstname']) || !isset($_SESSION['email'])) {
    // Redirect to login page if user is not logged in
    header("Location: ../authentication/login.php");
    exit();
}

// Fetch user details from the session
$firstname = $_SESSION['firstname'];
$email = $_SESSION['email'];

$categories = getAllJobCategories($pdo);
$jobs = getAllJobs($pdo);

// Fetch the applicants for the logged-in HR user
$applicants = fetchApplicantsForHR($pdo, $currentUserID);

// Get the logged-in HR's user ID
$currentUserID = $_SESSION['UserID'] ?? null;

$HrId = $_SESSION['UserID']; // Retrieve HR ID from session
$ApplicantContacts = getApplicantUserIdForFollowUp($pdo, $HrId);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/hr.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            HR
        </div>
        <div class="profile">
            <img src="https://via.placeholder.com/80" alt="Profile Picture">
            <h4><?php echo htmlspecialchars($firstname); ?></h4>
            <p><?php echo htmlspecialchars($email); ?></p>
        </div>
        <ul>
            <li><a href="#" onclick="showContent('job-post-section')"><i class="fas fa-briefcase"></i> Job Post</a></li>
            <li><a href="#" onclick="showContent('job-application')"><i class="fas fa-file-alt"></i> Applications</a></li>
            <li><a href="#" onclick="showContent('chat')"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="../authentication/core/handleform.php?logoutAUser=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

 <!-- Main Content -->
 <div class="main-content">
        <div id="job-post-section" class="content-section">

<!-- Add Job Button -->
<button class="add-job-btn" id="openModalBtn" style="
    background-color: #007bff; 
    color: white; 
    border: none; 
    padding: 10px 20px; 
    border-radius: 5px; 
    cursor: pointer;">Add Job</button>

<!-- Modal Structure -->
<div class="modal" id="jobModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create a Job Post</h2>
            <button class="close-btn" id="closeModalBtn" style="
                background: none; 
                border: none; 
                font-size: 1.5em; 
                cursor: pointer; 
                color: #888;">&times;</button>
        </div>
        <div class="modal-body">
            <form action="main/newhandleform.php" method="POST">
                <label for="job-title">Job Title:</label>
                <input type="text" id="job-title" name="job-title" required>

                <!-- Job Category Dropdown -->
                <label for="job-category">Job Category:</label>
                <select id="job-category" name="categoryID" required>
                    <option value="" disabled selected>Select a job category</option>
                    <?php
                    // Loop through each category and display it as an option
                    foreach ($categories as $category) {
                        echo "<option value='" . htmlspecialchars($category['CategoryID']) . "'>" . htmlspecialchars($category['CategoryName']) . "</option>";
                    }
                    ?>
                </select>

                <label for="job-description">Job Description:</label>
                <textarea id="job-description" name="job-description" rows="5" required></textarea>

                <label for="company-name">Company Name:</label>
                <input type="text" id="company-name" name="company-name" required>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>

                <label for="salary-range">Salary Range:</label>
                <input type="text" id="salary-range" name="salary-range" required>

                <label for="posting-date">Posting Date:</label>
                <input type="date" id="posting-date" name="posting-date" disabled required>

                <label for="expiry-date">Expiry Date (Optional):</label>
                <input type="date" id="expiry-date" name="expiry-date">

                <label for="employment-type">Employment Type:</label>
                <select id="employment-type" name="employment-type" required>
                    <option value="Full-Time">Full-Time</option>
                    <option value="Part-Time">Part-Time</option>
                    <option value="Contract">Contract</option>
                    <option value="Internship">Internship</option>
                </select>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Closed">Closed</option>
                </select>

                <button type="submit" class="submit-btn" name="submit">Post Job</button>
            </form>
        </div>
    </div>
</div>
        <h2>Job Listings</h2>
<table>
    <thead>
        <tr>
            <th>Job Title</th>
            <th>Company</th>
            <th>Location</th>
            <th>Salary</th>
            <th>Employment Type</th>
            <th>Status</th>
            <th>Category</th>
            <th>Posted By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jobs as $job): ?>
            <tr>
                <td><?= htmlspecialchars($job['JobTitle']) ?></td>
                <td><?= htmlspecialchars($job['CompanyName']) ?></td>
                <td><?= htmlspecialchars($job['Location']) ?></td>
                <td><?= htmlspecialchars($job['SalaryRange']) ?></td>
                <td><?= htmlspecialchars($job['EmploymentType']) ?></td>
                <td><?= htmlspecialchars($job['Status']) ?></td>
                <td><?= htmlspecialchars($job['CategoryName']) ?></td>
                <td><?= htmlspecialchars($job['PostedByFirstName'] . ' ' . $job['PostedByLastName']) ?></td>
                <td>
                <div class="action-buttons" style="
                    display: flex; 
                    gap: 10px; 
                    justify-content: flex-start; 
                    align-items: center;">
                    <form action="edit_job.php" method="GET" style="display: inline;">
                        <input type="hidden" name="JobID" value="<?= $job['JobID'] ?>">
                        <button type="submit" class="edit-btn" style="
                            background-color: #ffc107; 
                            color: white; 
                            border: none; 
                            padding: 5px 10px; 
                            border-radius: 5px; 
                            cursor: pointer; 
                            font-size: 0.9em;">Edit</button>
                    </form>
                    <form action="delete_job.php" method="POST" style="display: inline;">
                        <input type="hidden" name="JobID" value="<?= $job['JobID'] ?>">
                        <button type="submit" class="delete-btn" style="
                            background-color: #dc3545; 
                            color: white; 
                            border: none; 
                            padding: 5px 10px; 
                            border-radius: 5px; 
                            cursor: pointer; 
                            font-size: 0.9em;" 
                            onclick="return confirm('Are you sure you want to delete this job?');">Delete</button>
                    </form>
                </div>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>



        <div id="job-application" class="content-section" style="display: none;">
        <div class="table-container">
            <h2>Job Applicants</h2>
            <table>
                <thead>
                    <tr>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Email Address</th>
                        <th>Applied Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applicants)): ?>
                        <?php foreach ($applicants as $applicant): ?>
                            <tr>
                                <td><?= htmlspecialchars($applicant['Firstname']) ?></td>
                                <td><?= htmlspecialchars($applicant['Lastname']) ?></td>
                                <td><?= htmlspecialchars($applicant['Email']) ?></td>
                                <td><?= htmlspecialchars($applicant['AppliedPosition']) ?></td>
                                <td><button 
                                    class="view-button"
                                    onclick="viewApplication('<?= htmlspecialchars($applicant['ApplicationID']) ?>')">View</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No applicants for the jobs you created.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chat Section -->
    <div id="chat" class="content-section" style="display: none;">
    <h1>Chat Inbox</h1>
    <div class="container">
        <!-- Sidebar -->
        <div class="left">
            <div class="people">
                <?php if (!empty($ApplicantContacts)): ?>
                    <?php foreach ($ApplicantContacts as $person): ?>
                        <a 
                            href="http://localhost/projfinalexam/jobapplication/hrchatinbox.php?CreatedByApplicant=<?= htmlspecialchars($person['CreatedByApplicant']) ?>#chat" 
                            class="person" 
                            data-chat="person<?= htmlspecialchars($person['CreatedByApplicant']) ?>">
                            <span class="name"><?= htmlspecialchars($person['applicant_name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No applicants available for follow-up.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="scripts/hrdh.js"></script>
</body>

</html>