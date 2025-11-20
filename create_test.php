<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$teacher_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $class_id = intval($_POST['class_id']);

    $stmt = $conn->prepare("INSERT INTO tests (title, description, class_id, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $title, $description, $class_id, $teacher_id);

    if ($stmt->execute()) {
        $test_id = $stmt->insert_id;
        echo "<script>alert('Test created successfully! Now add questions.'); window.location.href='add_questions.php?test_id=$test_id';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding-top: 60px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        input, textarea, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            background-color: #1a237e;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0d1b5c;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create New Test</h2>
        <form method="POST">
            <label>Test Title:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Class ID:</label>
            <input type="number" name="class_id" required>

            <button type="submit">Create Test</button>
        </form>
    </div>
</body>
</html>
