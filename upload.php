<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$class_id = intval($_GET["class_id"]); // Ensure it's an integer
$user_id = intval($_SESSION["user_id"]); // Ensure it's an integer
$is_teacher = $_SESSION["role"] == "teacher"; // Assuming you have a role system

// Redirect if user is not a teacher
if (!$is_teacher) {
    die("❌ Access denied. Only teachers can upload assignments.");
}

$message = ""; // To store upload message

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"]) && isset($_POST["due_date"])) {
    $filename = basename($_FILES["file"]["name"]);
    $filepath = "uploads/" . $filename;
    $due_date_raw = $_POST["due_date"];
    $due_date = date("Y-m-d H:i:s", strtotime($due_date_raw));

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $filepath)) {
        // ✅ Only insert into files table
        $stmt_file = $conn->prepare("INSERT INTO files (class_id, file_name, file_path, uploaded_at, user_id, due_date) VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt_file->bind_param("issis", $class_id, $filename, $filepath, $user_id, $due_date);

        if ($stmt_file->execute()) {
            $message = "✅ Assignment uploaded and saved in 'files' table!";
        } else {
            $message = "❌ Database error (files): " . $stmt_file->error;
        }

        $stmt_file->close();
    } else {
        $message = "❌ Error uploading file.";
    }
}
$current_page = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Assignment</title>
    <style>
        /* General Styling */
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

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #4d2aaf;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
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


        /* Main Content */
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

        h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: black;
        }

        /* Upload Form */
        .upload-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }

        input[type="file"], input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4d2aaf;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
/* 
        button:hover {
            background-color: #3949ab;
        } */

        .message {
            margin-top: 15px;
            font-size: 16px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2 style="color: white;" >Teacher Panel</h2>
    <a href="dashboard.php" class="<?= ($current_page == 'upload.php')?'active':'' ?>">🏠 Home</a>
    <a href="create_class.php">✒️ Create Class</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h1>Upload Assignment</h1>
    <h2>Choose a file and set the submission deadline</h2>

    <div class="upload-container">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file" required><br>
            <label>Submission Deadline:</label>
            <input type="datetime-local" name="due_date" required><br><br>
            <button type="submit">Upload</button>
        </form>

        <?php if ($message): ?>
            <p class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
    </div>

    <br>
    <a href="view_class.php?id=<?php echo $class_id; ?>">⬅ Go Back</a>
</div>

</body>
</html>
