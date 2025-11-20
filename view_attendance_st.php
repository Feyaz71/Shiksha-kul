<?php
session_start();
include 'config.php';

// ✅ Ensure student is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Error: Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$student_id = $_SESSION['user_id'];

// ✅ Ensure class_id is passed via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Class ID missing.");
}
$class_id = $_GET['id'];

// ✅ Check if attendance is open
$attendance_status = false;
$status_query = "SELECT * FROM attendance_status WHERE id = 1";
$result = $conn->query($status_query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $attendance_status = ($row['is_open'] == 1);
}

// ✅ Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $date_today = date("Y-m-d");

    // Check if already marked
    $check_query = "SELECT * FROM attendance WHERE student_id = ? AND class_id = ? AND date = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("iis", $student_id, $class_id, $date_today);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows == 0) {
        $month_year = date("Y-m");
        $insert_query = "INSERT INTO attendance (class_id, student_id, date, month_year, status) VALUES (?, ?, ?, ?, 'Present')";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiss", $class_id, $student_id, $date_today, $month_year);
        if ($stmt->execute()) {
            echo "<script>alert('Attendance Marked!'); window.location.href='view_attendance_st.php?id=$class_id';</script>";
        } else {
            echo "<script>alert('Failed to mark attendance.');</script>";
        }
    } else {
        echo "<script>alert('You already marked attendance today.');</script>";
    }
}

// ✅ Attendance summary
$total_classes = 0;
$present_count = 0;

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM attendance WHERE student_id = ? AND class_id = ?");
$stmt->bind_param("ii", $student_id, $class_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_classes = $total_result->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as present FROM attendance WHERE student_id = ? AND class_id = ? AND status = 'Present'");
$stmt->bind_param("ii", $student_id, $class_id);
$stmt->execute();
$present_result = $stmt->get_result();
$present_count = $present_result->fetch_assoc()['present'];

$absent_count = $total_classes - $present_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Main Content */
        .main-content {
            margin-left: 270px;
            width: calc(100% - 270px);
            padding: 30px;
            text-align: center;
        }

        .attendance-header {
            border-radius: 25px;
            border: 2px solid #1a237e;
            background-color: #e3f2fd;
            padding: 10px 20px;
            display: inline-block;
            color: #1a237e;
        }

        .container {
            width: 50%;
            margin: 30px auto;
        }

        .attendance-btn {
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            border-radius: 5px;
        }

        .attendance-btn:disabled {
            background-color: gray;
            cursor: not-allowed;
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
        <a href="mark_attendance.php?id=<?php echo $class_id; ?>">📌 Mark Attendance</a>
        <a href="view_attendance_st.php?id=<?php echo $class_id; ?>">📜 View Attendance</a>
        <a href="view_class_st.php?id=<?php echo $class_id; ?>">🔙 Back</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="attendance-header">📊 Your Attendance Report</h2>

        <p>Total Classes: <?php echo $total_classes; ?></p>
        <p>Days Present: <?php echo $present_count; ?></p>

        <div class="container">
            <canvas id="attendanceChart"></canvas>
        </div>

        <form method="post">
            <button type="submit" name="mark_attendance" class="attendance-btn" <?php echo !$attendance_status ? 'disabled' : ''; ?>>
                <?php echo $attendance_status ? "✅ Give Attendance" : "❌ Attendance Closed"; ?>
            </button>
        </form>
    </div>

    <!-- Chart Script -->
    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [<?php echo $present_count; ?>, <?php echo $absent_count; ?>],
                    backgroundColor: ['#4CAF50', '#FF5733']
                }]
            }
        });
    </script>

</body>
</html>
