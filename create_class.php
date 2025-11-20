<?php
include "config.php";
session_start();

// Redirect if not a teacher
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "teacher") {
    header("Location: dashboard.php");
    exit;
}

// Handle Form Submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = trim($_POST["class_name"]);
    $teacher_id = $_SESSION["user_id"];

    if (!empty($class_name)) {
        // Check if class already exists for this teacher
        $stmt = $conn->prepare("SELECT id FROM classes WHERE class_name = ? AND teacher_id = ?");
        $stmt->bind_param("si", $class_name, $teacher_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "⚠️ A class with this name already exists!";
        } else {
            // Insert new class if it doesn't exist
            $stmt = $conn->prepare("INSERT INTO classes (class_name, teacher_id) VALUES (?, ?)");
            $stmt->bind_param("si", $class_name, $teacher_id);

            if ($stmt->execute()) {
                $message = "✅ Class created successfully! <a href='dashboard.php'>Go to Dashboard</a>";
            } else {
                $message = "❌ Error: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $message = "⚠️ Please enter a valid class name!";
    }
}
$current_page = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Class</title>
    <style>
        /* General Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #e7ddff;
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #4d2aa7; /* Google Classroom-style blue */
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
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
/* 
        .sidebar a:hover {
            background-color: #3949ab;
        } */
        
        .sidebar a.active{
            background-color:  white;
            color: #4d2aa7;
        }

        /* Content Area */
        .content {
            margin-left: 270px;
            padding: 40px;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Form Container */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            margin-top: 20px;
            font-size: 14px;
            color: #d32f2f;
        }

        .dashboard-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #4d2aa7;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Teacher Panel</h2>
    <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php')?'active':'' ?>">🏠 Home</a>
    <a href="create_class.php" class="<?= ($current_page == 'create_class.php')?'active':'' ?>">✒️ Create Class</a>
    <a href="profile.html" class="<?= ($current_page == 'profile.html')?'active':'' ?>">👤 Profile</a>
    <a href="logout.php" class="<?= ($current_page == 'logout.php')?'active':'' ?>">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="form-container">
        <h1>Create a New Class</h1>
        <form action="create_class.php" method="POST">
            <label for="class_name">Class Name:</label>
            <input type="text" name="class_name" id="class_name" placeholder="Enter Class Name" required>
            <button type="submit">Create Class</button>
        </form>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <a href="dashboard.php" class="dashboard-link">Go to Dashboard</a>
    </div>
</div>

</body>
</html>
