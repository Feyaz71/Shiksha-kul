<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "teacher") {
    header("Location: dashboard.php");
    exit;
}

if (!isset($_GET["id"])) {
    die("Error: Class ID not provided.");
}

$class_id = intval($_GET["id"]);

// First, fetch files to delete them from storage
$file_stmt = $conn->prepare("SELECT filename FROM files WHERE class_id = ?");
$file_stmt->bind_param("i", $class_id);
$file_stmt->execute();
$file_result = $file_stmt->get_result();
while ($file = $file_result->fetch_assoc()) {
    $file_path = "uploads/" . $file['filename']; // Adjust folder path if needed
    if (file_exists($file_path)) {
        unlink($file_path); // Delete the file
    }
}
$file_stmt->close();

// Delete related records from class_students
$stmt = $conn->prepare("DELETE FROM class_students WHERE class_id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$stmt->close();

// Delete related files from files table
$stmt = $conn->prepare("DELETE FROM files WHERE class_id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$stmt->close();

// Finally, delete the class itself
$stmt = $conn->prepare("DELETE FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$stmt->close();

// Redirect to dashboard after deletion
header("Location: dashboard.php?message=Class deleted successfully");
exit;
?>
