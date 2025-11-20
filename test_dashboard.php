<?php
session_start();
include "config.php"; // ✅ Make sure this file defines $conn

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch the latest test_id from the tests table
$test_id = null;
$sql = "SELECT id FROM tests ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $test_id = $row['id'];
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Test Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            text-align: center;
            padding-top: 60px;
        }

        .card-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .card {
            background-color: #e3f2fd;
            color: #1a237e;
            padding: 25px 30px;
            border-radius: 20px;
            border: 2px solid #1a237e;
            cursor: pointer;
            width: 250px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, background-color 0.3s ease;
        }

        .card:hover {
            background-color: #bbdefb;
            transform: translateY(-5px);
        }

        h2 {
            margin-bottom: 40px;
            color: #1a237e;
        }

        .card h3 {
            margin: 0;
        }
    </style>
</head>
<body>

    <h2>🧪 Test Management Dashboard</h2>

    <div class="card-container">
        <div class="card" onclick="window.location.href='create_test.php'">
            <h3>➕ Create New Test</h3>
        </div>

        <div class="card" onclick="window.location.href='view_tests.php'">
            <h3>📋 View / Manage Tests</h3>
        </div>

        <div class="card" onclick="window.location.href='view_result_teacher.php?test_id=<?php echo $test_id; ?>'">
            <h3>📋 Tests Results</h3>
        </div>


    </div>

</body>
</html>
