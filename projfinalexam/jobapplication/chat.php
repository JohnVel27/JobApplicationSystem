<?php
// Include necessary files for database configuration and data handling
require_once '../authentication/core/dbconfig.php'; // Database connection
require_once '../authentication/core/datamodel.php'; // Data model functions
require_once '../authentication/core/handleform.php'; // Handle form
require_once 'main/newdatamodel.php';
// Get HR and Applicant IDs
$applicantID = $_SESSION['UserID']; // Retrieve applicant ID from session

// Check for the HR contact ID in the GET parameter
if (isset($_GET['CreatedByHRJob']) && is_numeric($_GET['CreatedByHRJob'])) {
    $hrID = (int)$_GET['CreatedByHRJob'];
    // Get HR's name by calling the function
    $hrName = getHRNameByID($hrID, $pdo);
    $hrFirstname = $hrName['Firstname'];
    $hrLastname = $hrName['Lastname'];
    $messages = getConversation($applicantID, $hrID, $pdo);
} else {
    $messages = [];
    $hrID = null;
    // If 'CreatedByHRJob' is not passed, set default values
    $hrFirstname = "Unknown";
    $hrLastname = "Unknown";
}
?>

<!-- HTML for the chat interface -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interface</title>
    <link rel="stylesheet" href="styles/chat.css">
</head>
<body>
<div class="abovecontainter" style="text-align: center; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 30px; width: 300px; max-width: 100%; margin-bottom: 20px; position: absolute; top: 20px; left: 50%; transform: translateX(-50%);">
    <h1 style="font-size: 24px; color: #333; margin-bottom: 20px;">Chat with HR</h1>
    <p style="font-size: 18px; color: #555; font-weight: bold;">HR Name: <?php echo htmlspecialchars($hrFirstname . " " . $hrLastname); ?></p>
</div>


    <div class="chat-container">
        <div class="chat-window">
            <?php if ($messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= $message['SenderUserID'] == $applicantID ? 'sender' : 'receiver' ?>">
                        <p><?= htmlspecialchars($message['MessageContent']) ?></p>
                        <span class="time"><?= htmlspecialchars($message['DateSent']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No messages yet. Start a conversation!</p>
            <?php endif; ?>
        </div>

        <div class="chat-input">
        <form method="POST" action="main/newhandleform.php">
            <textarea name="messageContent" id="messageInput" placeholder="Type your message..." required></textarea>
            <input type="hidden" name="receiverUserID" value="<?= htmlspecialchars($hrID) ?>">
            <button type="submit" name="sendbtn" id="sendMessage">Send</button>
        </form>
        </div>
    </div>

    <script src="scripts/chat.js"></script>
</body>
</html>