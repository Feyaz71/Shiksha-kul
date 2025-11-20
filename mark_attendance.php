<?php
session_start();
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($conn) || !$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) { 
    echo "<script>alert('Error: Student not logged in. Please log in again.'); window.location.href='login.php';</script>";
    exit();
}

$student_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Class ID is missing.");
}

$class_id = $_GET['id'];

$checkStatus = $conn->query("SELECT is_open FROM attendance_status WHERE id = 1");
if (!$checkStatus) {
    die("Error fetching attendance status: " . $conn->error);
}

$status = $checkStatus->fetch_assoc();
$is_open = $status['is_open'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    if (!$is_open) {
        echo "<script>alert('Attendance is currently closed.'); window.location.href='mark_attendance.php?id=$class_id';</script>";
        exit();
    }

    $date = date("Y-m-d");

    // Check if attendance is already marked for today
    $check_sql = "SELECT 1 FROM attendance WHERE student_id = ? AND class_id = ? AND date = ?";
    $stmt = $conn->prepare($check_sql);
    if (!$stmt) {
        die("Error preparing attendance check query: " . $conn->error);
    }

    $stmt->bind_param("iis", $student_id, $class_id, $date);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('You have already marked attendance today.'); window.location.href='mark_attendance.php?id=$class_id';</script>";
        exit();
    }

    $month_year = date("Y-m");

    // Insert attendance record
    $insert_sql = "INSERT INTO attendance (student_id, class_id, date, month_year, status) VALUES (?, ?, ?, ?, 'Present')";
    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        die("Error preparing insert query: " . $conn->error);
    }

    $stmt->bind_param("iiss", $student_id, $class_id, $date, $month_year);

    if (!$stmt->execute()) {
        die("Error inserting attendance: " . $stmt->error);
    }

    echo "<script>alert('Attendance marked successfully!'); window.location.href='view_attendance_st.php?id=$class_id';</script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

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

        .main-content {
            margin-left: 270px;
            width: calc(100% - 270px);
            text-align: center;
            padding-top: 60px;
        }

        .attendance-header {
            border-radius: 25px;
            border: 2px solid #1a237e;
            background-color: #e3f2fd;
            padding: 10px 20px;
            display: inline-block;
            color: #1a237e;
        }

        .btn {
            background-color: #1a237e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            border: 2px solid #1a237e;
            cursor: pointer;
            display: inline-block;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #0d1b56;
        }

        p {
            font-size: 18px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>📅 Attendance</h2>
        <a href="mark_attendance.php?id=<?php echo $class_id ?>">📌 Mark Attendance</a>
        <a href="view_attendance_st.php?id=<?php echo $class_id; ?>">📜 View Attendance</a>
        <a href="view_class_st.php?id=<?php echo $class_id; ?>">🔙 Back</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="attendance-header">✅ Mark Your Attendance</h2>

        <?php if ($is_open): ?>
            <p style="color: green; margin-top: 20px;">✔️ Attendance is OPEN</p>
            <form method="POST">
                <button type="submit" name="mark_attendance" class="btn">✅ Mark Attendance</button>
            </form>
        <?php else: ?>
            <p style="color: red; margin-top: 20px;">❌ Attendance is CLOSED</p>
        <?php endif; ?>
    </div>

</body>
</html>
