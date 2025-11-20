<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

if (!isset($_GET['test_id'])) {
    die("Error: Test ID not provided.");
}

$test_id = intval($_GET['test_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = $_POST['correct_option'];

    $stmt = $conn->prepare("INSERT INTO questions (test_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $test_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

    if ($stmt->execute()) {
        echo "<script>alert('Question added successfully!'); window.location.href='add_questions.php?test_id=$test_id';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e8f0fe;
            padding: 40px;
            display: flex;
            justify-content: center;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 500px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
    <h2>Add Question to Test ID: <?php echo $test_id; ?></h2>
    <form method="POST">
        <label>Question:</label>
        <textarea name="question_text" required></textarea>

        <label>Option A:</label>
        <input type="text" name="option_a" required>

        <label>Option B:</label>
        <input type="text" name="option_b" required>

        <label>Option C:</label>
        <input type="text" name="option_c" required>

        <label>Option D:</label>
        <input type="text" name="option_d" required>

        <label>Correct Option (A/B/C/D):</label>
        <select name="correct_option" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>

        <button type="submit">Add Question</button>
    </form>

    <br>
    <a href="view_tests.php">← Back to Tests</a>
</div>

</body>
</html>
