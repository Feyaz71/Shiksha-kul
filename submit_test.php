<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='login.php';</script>";
    exit();
}

$student_id = $_SESSION['user_id'];
$test_id = $_POST['test_id'];
$answers = $_POST['answers'];

// Check if the student is enrolled in the class for this test
$check_enrollment = $conn->prepare("
    SELECT c.id FROM classes c
    JOIN tests t ON c.id = t.class_id
    JOIN class_students sc ON sc.class_id = c.id
    WHERE t.id = ? AND sc.student_id = ?
");

$check_enrollment->bind_param("ii", $test_id, $student_id);
$check_enrollment->execute();
$check_enrollment->store_result();

if ($check_enrollment->num_rows === 0) {
    echo "<script>alert('Access Denied! You are not enrolled in this class.'); window.location.href='student_test_dashboard.php';</script>";
    exit();
}
$check_enrollment->close();

// Check if the student has already submitted the test
$check_attempt = $conn->prepare("SELECT id FROM test_results WHERE student_id = ? AND test_id = ?");
$check_attempt->bind_param("ii", $student_id, $test_id);
$check_attempt->execute();
$check_attempt->store_result();

if ($check_attempt->num_rows > 0) {
    echo "<script>alert('You have already submitted this test!'); window.location.href='view_result.php?test_id=$test_id';</script>";
    exit();
}
$check_attempt->close();

$total = count($answers);
$correct = 0;

foreach ($answers as $question_id => $selected) {
    $query = $conn->prepare("SELECT correct_option FROM questions WHERE id = ?");
    $query->bind_param("i", $question_id);
    $query->execute();
    $query->bind_result($correct_option);
    $query->fetch();
    $query->close();

    $is_correct = strtoupper($selected) === strtoupper($correct_option) ? 1 : 0;
    if ($is_correct) $correct++;

    $insert = $conn->prepare("
        INSERT INTO student_answers (student_id, test_id, question_id, selected_option, is_correct) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("iiisi", $student_id, $test_id, $question_id, $selected, $is_correct);
    $insert->execute();
}

// Insert result into `test_results`
$insert_result = $conn->prepare("
    INSERT INTO test_results (student_id, test_id, score, total_questions)
    VALUES (?, ?, ?, ?)
");
$insert_result->bind_param("iiii", $student_id, $test_id, $correct, $total);
$insert_result->execute();

echo "<script>alert('Test submitted successfully! Your score: $correct / $total'); window.location.href='view_result.php?test_id=$test_id';</script>";
?>
