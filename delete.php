<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "teacher") {
    header("Location: dashboard.php");
    exit;
}

$file_id = $_GET["id"];
$file = $conn->query("SELECT * FROM files WHERE id = $file_id")->fetch_assoc();
$class_id = $file["class_id"];

$message = ""; // Store success/error message

if ($file && unlink($file["filepath"])) {
    $conn->query("DELETE FROM files WHERE id = $file_id");
    $message = "✅ File deleted successfully!";
} else {
    $message = "❌ Error deleting file.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete File</title>
    <style>
        /* General Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #1a237e;
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
            color: white; /* Ensuring white text */
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

        .sidebar a:hover {
            background-color: #3949ab;
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

        /* Message Box */
        .message-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }

        .message {
            font-size: 16px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        /* Button */
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #1a237e;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #3949ab;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Teacher Panel</h2>
    <a href="dashboard.php">🏠 Home</a>
    <a href="create_class.php">✒️ Create Class</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h1>Delete File</h1>
    <h2>File Deletion Status</h2>

    <div class="message-box">
        <p class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>
    </div>

    <br>
    <a href="view_class.php?id=<?php echo $class_id; ?>" class="btn">⬅ Go Back</a>
</div>

</body>
</html>
