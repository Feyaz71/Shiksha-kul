<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$student_id = $_SESSION['user_id'];

$query = "
    SELECT t.* FROM tests t
    JOIN class_students cs ON t.class_id = cs.class_id
    WHERE cs.student_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Tests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #e7ddff;
        }
        .test-box {
            border: 2px solid #4d2aaf;
            border-radius: 10px;
            padding: 15px;
            background-color: rgb(240, 234, 255);
            margin-bottom: 15px;
        }
        .test-box h3 {
            color: #4d2aaf;
        }
        .start-btn {
            padding: 8px 15px;
            background-color: #4d2aaf;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .start-btn:hover {
            background-color: #0d1b5c;
        }
    </style>
</head>
<body>

<h2>📝 Available Tests</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="test-box">
        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        <a class="start-btn" href="attempt_test.php?test_id=<?php echo $row['id']; ?>">Start Test</a>
    </div>
<?php endwhile; ?>

</body>
</html>
