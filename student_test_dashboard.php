<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$student_id = $_SESSION['user_id'];

// Get list of classes the student is enrolled in
$query = "
    SELECT c.id, c.class_name 
    FROM classes c 
    JOIN class_students cs ON c.id = cs.class_id 
    WHERE cs.student_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Test Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e7ddff;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .dashboard-container {
            background-color: white;
            width: 80%;
            max-width: 800px;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4d2aaf;
            margin-bottom: 25px;
            text-align: center;
        }

        .class-card {
            background-color: rgb(240, 234, 255);
            border: 2px solid #4d2aaf;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .class-card h3 {
            margin: 0 0 15px;
            color: #4d2aaf;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #4d2aaf;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
/* 
        .btn:hover {
            background-color: #0d1b5c;
        } */

        .btn:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h2>📚 Welcome to Your Test Dashboard</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="class-card">
            <h3>📘 <?php echo htmlspecialchars($row['class_name']); ?></h3>
            <a class="btn" href="available_tests.php?id=<?php echo $row['id']; ?>">📝 Available Tests</a>
            <a class="btn" href="view_result.php?test_id=<?php echo $row['id']; ?>">📊 My Results</a>


        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
