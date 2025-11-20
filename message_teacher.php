<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to send messages.");
}

$class_id = isset($_GET["class_id"]) ? intval($_GET["class_id"]) : 0;
$sender_id = $_SESSION["user_id"];

if ($class_id === 0) {
    die("Invalid request.");
}

// Get teacher's ID for the class
$teacher_stmt = $conn->prepare("SELECT teacher_id FROM classes WHERE id = ?");
$teacher_stmt->bind_param("i", $class_id);
$teacher_stmt->execute();
$teacher = $teacher_stmt->get_result()->fetch_assoc();
$teacher_stmt->close();

if (!$teacher) {
    die("Error: Teacher not found.");
}

$receiver_id = $teacher['teacher_id'];

// Fetch teacher name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$receiver = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$receiver) {
    die("Error: User not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["message"])) {
    $message = trim($_POST["message"]);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (class_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $class_id, $sender_id, $receiver_id, $message);
        $stmt->execute();
        $stmt->close();
    }
    exit;
}

$stmt = $conn->prepare("SELECT * FROM messages WHERE class_id = ? AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) ORDER BY sent_at ASC");
$stmt->bind_param("iiiii", $class_id, $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$messages = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($receiver['name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .chat-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .message-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .message-box.sent {
            background: #e3f2fd;
            text-align: right;
        }
        .message-box.received {
            background: #f1f8e9;
            text-align: left;
        }
        .message-form {
            display: flex;
            margin-top: 10px;
        }
        .message-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .send-button {
            background: #1a237e;
            color: white;
            border: none;
            padding: 10px;
            margin-left: 5px;
            border-radius: 5px;
            cursor: pointer;
        }
        .send-button:hover {
            background: #3949ab;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <h2 style="font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif">Chat with Guruji</h2>
    
    <div id="message-list">
        <?php while ($msg = $messages->fetch_assoc()): ?>
            <div class="message-box <?php echo ($msg['sender_id'] == $sender_id) ? 'sent' : 'received'; ?>">
                <p><?php echo htmlspecialchars($msg['message']); ?></p>
                <small><?php echo date("H:i, d M", strtotime($msg['sent_at'])); ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <form class="message-form" id="message-form">
        <input type="text" name="message" id="message-input" class="message-input" placeholder="Type your message..." required>
        <button type="submit" class="send-button">Send</button>
    </form>
</div>

<script>
document.getElementById("message-form").addEventListener("submit", function(event) {
    event.preventDefault();
    
    let messageInput = document.getElementById("message-input");
    let message = messageInput.value.trim();
    
    if (message === "") return;

    let formData = new FormData();
    formData.append("message", message);

    fetch(window.location.href, {
        method: "POST",
        body: formData
    }).then(() => {
        messageInput.value = "";
        loadMessages();
    });
});

function loadMessages() {
    fetch(window.location.href)
    .then(response => response.text())
    .then(html => {
        let parser = new DOMParser();
        let doc = parser.parseFromString(html, "text/html");
        document.getElementById("message-list").innerHTML = doc.getElementById("message-list").innerHTML;
    });
}

setInterval(loadMessages, 5000);
</script>

</body>
</html>
