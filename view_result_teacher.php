<?php
session_start();
include 'config.php';

// Check if the user is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Unauthorized access!'); window.location.href='dashboard.php';</script>";
    exit();
}

// Ensure test_id is provided
if (!isset($_GET['test_id'])) {
    echo "<script>alert('Test ID is missing.'); window.location.href='dashboard.php';</script>";
    exit();
}

$test_id = $_GET['test_id'];

// Get test details
$test_query = $conn->prepare("SELECT title, created_by FROM tests WHERE id = ?");
$test_query->bind_param("i", $test_id);
$test_query->execute();
$test_query->bind_result($test_title, $created_by);
$test_query->fetch();
$test_query->close();

// Fetch students who submitted the test
$query = $conn->prepare("
    SELECT s.id, s.name, tr.score, tr.total_questions 
    FROM test_results tr
    JOIN students s ON tr.student_id = s.id
    WHERE tr.test_id = ?
    ORDER BY s.name ASC
");
$query->bind_param("i", $test_id);
$query->execute();
$query->store_result();
$query->bind_result($student_id, $student_name, $score, $total);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        a { text-decoration: none; color: #007bff; font-weight: bold; }
        .back-btn { display: block; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h2>📜 Test Results for "<strong><?php echo htmlspecialchars($test_title); ?></strong>"</h2>
    <p>👤 Created by: <?php echo htmlspecialchars($created_by); ?></p>

    <table>
        <tr>
            <th>👨‍🎓 Student Name</th>
            <th>✅ Score</th>
            <th>❓ Total Questions</th>
            <th>📜 View Responses</th>
        </tr>

        <?php if ($query->num_rows > 0): ?>
            <?php while ($query->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student_name); ?></td>
                    <td><?php echo htmlspecialchars($score); ?></td>
                    <td><?php echo htmlspecialchars($total); ?></td>
                    <td><a href="view_student_response.php?student_id=<?php echo $student_id; ?>&test_id=<?php echo $test_id; ?>">📄 View</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No students have submitted this test yet.</td></tr>
        <?php endif; ?>
    </table>

    <a href="dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
</div>

</body>
</html>

<?php $query->close(); ?>
