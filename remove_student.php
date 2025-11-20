<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access");
}

if (!isset($_GET["class_id"]) || !isset($_GET["student_id"])) {
    die("Invalid request");
}

$class_id = intval($_GET["class_id"]);
$student_id = intval($_GET["student_id"]);

// Remove student from class
$stmt = $conn->prepare("DELETE FROM class_students WHERE class_id = ? AND student_id = ?");
$stmt->bind_param("ii", $class_id, $student_id);
$stmt->execute();
$stmt->close();

// Redirect back to class page
header("Location: view_class.php?id=$class_id");
exit;
?>
