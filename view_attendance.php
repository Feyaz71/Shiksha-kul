<?php
session_start();
include 'config.php';

if (!isset($_GET['id'])) {
    die("Error: Class ID not provided.");
}

$class_id = $_GET['id'];
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');


// Fetch distinct months for dropdown
$monthsQuery = $conn->prepare("
    SELECT DISTINCT DATE_FORMAT(date, '%Y-%m') AS month 
    FROM attendance 
    WHERE class_id = ? 
    ORDER BY month DESC
");
$monthsQuery->bind_param("i", $class_id);
$monthsQuery->execute();
$monthsResult = $monthsQuery->get_result();

// Fetch all students in this class
$studentsQuery = $conn->prepare("
    SELECT students.id, students.name 
    FROM students 
    JOIN class_students ON students.id = class_students.student_id 
    WHERE class_students.class_id = ?
");
$studentsQuery->bind_param("i", $class_id);
$studentsQuery->execute();
$studentsResult = $studentsQuery->get_result();

$students = [];
while ($row = $studentsResult->fetch_assoc()) {
    $students[$row['id']] = $row['name'];
}

asort($students); // Sort students alphabetically

// Fetch attendance for the selected month
$attendanceQuery = $conn->prepare("
    SELECT student_id, DATE_FORMAT(date, '%d-%m-%Y') AS formatted_date, status
    FROM attendance 
    WHERE class_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?
");
$attendanceQuery->bind_param("is", $class_id, $selectedMonth);
$attendanceQuery->execute();
$attendanceResult = $attendanceQuery->get_result();

$attendanceData = [];
$allDates = [];

while ($row = $attendanceResult->fetch_assoc()) {
    $attendanceData[$row['student_id']][$row['formatted_date']] = $row['status'];
    $allDates[$row['formatted_date']] = true;
}

ksort($allDates); // Sort dates chronologically
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
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
            padding-top: 30px;
        }

        select {
            padding: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 95%;
            margin: 0 auto;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #1a237e;
            color: white;
        }

        .present {
            color: green;
        }

        .absent {
            color: red;
        }

        .summary {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>📅 Attendance</h2>
        <a href="open_attendance.php?id=<?php echo $class_id ?>">✅ Open Attendance</a>
        <a href="view_attendance.php?id=<?php echo $class_id; ?>">✅ View Attendance</a>
        <a href="attendance.php?id=<?php echo $class_id; ?>">🔙 Back</a>
    </div>

    <div class="main-content">
        <h2>📜 Attendance Records</h2>

        <form method="GET">
            <input type="hidden" name="id" value="<?php echo $class_id; ?>">
            <label for="month">Select Month:</label>
            <select name="month" id="month" onchange="this.form.submit()">
                <?php while ($month = $monthsResult->fetch_assoc()): ?>
                    <option value="<?php echo $month['month']; ?>" <?php echo ($selectedMonth == $month['month']) ? 'selected' : ''; ?>>
                        <?php echo date('F Y', strtotime($month['month'])); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <!-- Matrix Table -->
        <table>
            <tr>
                <th>Student Name</th>
                <?php foreach ($allDates as $date => $_): ?>
                    <th><?php echo date('d M', strtotime($date)); ?></th>
                <?php endforeach; ?>
                <th>Present</th>
                <th>Attendance %</th>
            </tr>

            <?php foreach ($students as $sid => $name): ?>
                <tr>
                    <td><?php echo htmlspecialchars($name); ?></td>
                    <?php 
                        $presentCount = 0;
                        foreach ($allDates as $date => $_): 
                            if (isset($attendanceData[$sid][$date])) {
                                echo "<td class='present'>✔️</td>";
                                $presentCount++;
                            } else {
                                echo "<td class='absent'>❌</td>";
                            }
                        endforeach; 
                        $totalDays = count($allDates);
                        $percentage = $totalDays ? round(($presentCount / $totalDays) * 100) : 0;
                    ?>
                    <td><?php echo $presentCount; ?></td>
                    <td><?php echo $percentage; ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>
