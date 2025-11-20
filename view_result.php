<?php
session_start();
include 'config.php';

if (!isset($_GET['test_id'])) {
    echo "<script>alert('Test ID is missing.'); window.location.href='student_test_dashboard.php';</script>";
    exit();
}

$student_id = $_SESSION['user_id'];
$test_id = $_GET['test_id'];

$query = $conn->prepare("SELECT score, total_questions FROM test_results WHERE student_id = ? AND test_id = ?");
$query->bind_param("ii", $student_id, $test_id);
$query->execute();
$query->bind_result($score, $total);
$query->fetch();
?>

<h2>📊 Test Result</h2>
<p>Your Score: <strong><?php echo $score; ?> / <?php echo $total; ?></strong></p>
<a href="available_tests.php?id=<?php echo $_GET['class_id'] ?? ''; ?>">🔙 Back to Tests</a>
