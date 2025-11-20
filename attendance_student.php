<?php
session_start();
include 'config.php'; // Database connection file

// Debugging: Print the entire $_GET array to check what's coming in the URL
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Check if class_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Class ID not provided in the URL.");
}

$class_id = $_GET['id']; // Get class ID from URL

// Fetch distinct months for attendance based on class ID
$sql = "SELECT DISTINCT DATE_FORMAT(date, '%Y-%m') AS month FROM attendance WHERE class_id = ? ORDER BY month DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$months = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance</title>
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
            background-color: #4d2aaf;
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
            background-color: #4d2aaf;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 270px; /* Adjusting for sidebar width */
            width: calc(100% - 270px); /* Taking the remaining width */
            text-align: center;
            padding-top: 30px;
        }

        .attendance-header {
            border-radius: 25px; 
            border: 2px solid #4d2aaf; 
            background-color: rgb(240, 234, 255); 
            padding: 10px 20px; 
            display: inline-block; 
            color: #4d2aaf;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }

        ul li {
            background: white;
            padding: 10px;
            margin: 5px auto;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            width: 60%;
        }

        ul li a {
            text-decoration: none;
            color: #4d2aaf;
            font-weight: bold;
        }

        ul li a:hover {
            text-decoration: underline;
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
    
</body>
</html>
