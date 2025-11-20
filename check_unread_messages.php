<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["unread_count" => 0]);
    exit;
}

$student_id = $_SESSION["user_id"];  // Logged-in user
$class_id = isset($_GET["class_id"]) ? intval($_GET["class_id"]) : 0;
$sender_id = isset($_GET["sender_id"]) ? intval($_GET["sender_id"]) : 0; // Get sender ID

if ($class_id == 0 || $sender_id == 0) {
    echo json_encode(["unread_count" => 0]);
    exit;
}

// Check unread messages **only from this specific sender**
$stmt = $conn->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM messages 
    WHERE class_id = ? 
    AND receiver_id = ? 
    AND sender_id = ? 
    AND seen = 0
");

$stmt->bind_param("iii", $class_id, $student_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode(["unread_count" => $result['unread_count']]);
?>
