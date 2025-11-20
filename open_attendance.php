<?php
session_start();
include 'config.php';

// Check if class_id is set in URL
if (!isset($_GET['id'])) {
    die("Error: Class ID not provided."); // Stop execution if id is missing
}

$class_id = $_GET['id']; // Get class ID from URL

// Check if attendance is open
$checkStatus = $conn->query("SELECT * FROM attendance_status WHERE id=1");
$status = $checkStatus->fetch_assoc();
$is_open = $status['is_open']; // 1 = Open, 0 = Closed

// If "Open Attendance" button is clicked
if (isset($_POST['open_attendance'])) {
    $conn->query("UPDATE attendance_status SET is_open = 1 WHERE id=1");
    header("Location: open_attendance.php?id=$class_id"); // Pass class_id
    exit();
}

// If "Close Attendance" button is clicked
if (isset($_POST['close_attendance'])) {
    $conn->query("UPDATE attendance_status SET is_open = 0 WHERE id=1");
    header("Location: open_attendance.php?id=$class_id"); // Pass class_id
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #1a237e;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin: 5px 0;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #0d1b56;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 270px; /* Adjusting for sidebar width */
            width: calc(100% - 270px); /* Taking the remaining width */
            text-align: center;
            padding-top: 30px;
        }

        .btn {
            background-color: #1a237e;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            border: 2px solid #1a237e;
            cursor: pointer;
            display: inline-block;
            margin: 10px;
        }

        .btn:hover {
            background-color: #0d1b56;
        }
    </style>
</head>
<body>

    <!-- Sidebar (Same as attendance.php) -->
    <div class="sidebar">
        <h2>📅 Attendance</h2>
        <a href="open_attendance.php?id=<?php echo $class_id ?>">✅ Open Attendance</a>
        <a href="view_attendance.php?id=<?php echo $class_id; ?>">✅ View Attendance</a>
        <a href="attendance.php?id=<?php echo $class_id; ?>">🔙 Back</a>

    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>📅 Manage Attendance</h2>

        <form method="post">
            <?php if ($is_open): ?>
                <button type="submit" name="close_attendance" class="btn">❌ Close Attendance</button>
                <p style="color: green;">✔️ Attendance is currently OPEN</p>
            <?php else: ?>
                <button type="submit" name="open_attendance" class="btn">✅ Open Attendance</button>
                <p style="color: red;">❌ Attendance is currently CLOSED</p>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>
