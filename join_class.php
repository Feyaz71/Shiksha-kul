<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: dashboard.php");
    exit;
}

$student_id = $_SESSION["user_id"];

// Handle class join request submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = $_POST["class_id"];

    // Check if request already exists
    $check = $conn->prepare("SELECT * FROM class_requests WHERE class_id = ? AND student_id = ?");
    $check->bind_param("ii", $class_id, $student_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        // Insert join request
        $stmt = $conn->prepare("INSERT INTO class_requests (class_id, student_id, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("ii", $class_id, $student_id);
        $stmt->execute();
        echo "<script>alert('Request sent to teacher. Wait for approval.'); window.location.href='student_dashboard.php';</script>";
    } else {
        echo "<script>alert('You have already requested to join this class.');</script>";
    }
}

// Fetch available classes (excluding deleted ones)
$query = "SELECT classes.id, classes.class_name, users.name AS teacher_name 
          FROM classes 
          JOIN users ON classes.teacher_id = users.id 
          WHERE classes.status != 'deleted'";

$classes = $conn->query($query);

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Class</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #e7ddff;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #4d2aa7;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s;
            margin-bottom: 10px;
        }

        /* .sidebar a:hover {
            background-color: #3949ab;
        } */
        
        .sidebar a.active{
            background-color:  white;
            color: #4d2aa7;
        }

        .content {
            margin-left: 270px;
            padding: 30px;
            width: 100%;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .join-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: auto;
            text-align: center;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
            background-color: #4d2aa7;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.568);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Student Panel</h2>
    <a href="student_dashboard.php" class="<?= ($current_page == 'student_dashboard.php') ? 'active' : '' ?>">🏠 Home</a>
    <a href="join_class.php" class="<?= ($current_page == 'join_class.php')?'active':'' ?>">📚 Join Class</a>
    <a href="profile.html" class="<?= ($current_page == 'profile.php')?'active':'' ?>">👤 Profile</a>
    <a href="logout.php" class="<?= ($current_page == 'logout.php')?'active':'' ?>">🚪 Logout</a>
</div>

<div class="content">
    <h1>Request to Join a Class</h1><br>
    <div class="join-form">
        <form method="post">
            <label for="class_id">Select Class:</label>
            <select name="class_id" required>
                <?php while ($row = $classes->fetch_assoc()) { ?>
                    <option value="<?= $row["id"] ?>">
                        <?= htmlspecialchars($row["class_name"]) ?> (👨‍🏫 <?= htmlspecialchars($row["teacher_name"]) ?>)
                    </option>
                <?php } ?>
            </select>
            <button type="submit">Send Join Request</button>
        </form>
    </div>
</div>

</body>
</html>
