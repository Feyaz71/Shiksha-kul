<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION["user_id"]);

$assignment_id = isset($_GET["assignment_id"]) ? intval($_GET["assignment_id"]) : (isset($_POST["assignment_id"]) ? intval($_POST["assignment_id"]) : 0);
$class_id = isset($_GET["class_id"]) ? intval($_GET["class_id"]) : (isset($_POST["class_id"]) ? intval($_POST["class_id"]) : 0);

if ($assignment_id === 0 || $class_id === 0) {
    die("❌ Invalid access. Assignment or class ID missing.");
}

$message = "";

// Process the form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["submission_file"])) {
    $filename = basename($_FILES["submission_file"]["name"]);
    $target_dir = "uploads/Assignment_submissions/";

    // Ensure target folder exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $filepath = $target_dir . $filename;

    // Check due date from files table
    $stmt = $conn->prepare("SELECT due_date FROM files WHERE id = ?");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $message = "❌ Assignment not found.";
    } else {
        $assignment = $res->fetch_assoc();
        $stmt->close();

        if (!empty($assignment['due_date']) && new DateTime() > new DateTime($assignment['due_date'])) {
            $message = "❌ Submission deadline has passed.";
        } else {
            if (move_uploaded_file($_FILES["submission_file"]["tmp_name"], $filepath)) {
                $stmt = $conn->prepare("INSERT INTO submissions (file_id, student_id, file_name, file_path, submitted_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("iiss", $assignment_id, $user_id, $filename, $filepath);

                if ($stmt->execute()) {
                    $message = "✅ Submission successful!";
                } else {
                    $message = "❌ DB Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "❌ Error uploading file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Assignment</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #111827;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-weight: 500;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }

        button {
            padding: 12px;
            background: #3b82f6;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #2563eb;
        }

        .message {
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📤 Submit Assignment</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">

            <label for="submission_file">Select file to submit:</label>
            <input type="file" name="submission_file" id="submission_file" required>

            <button type="submit">Submit 📎</button>
        </form>

        <a class="back-link" href="view_class_st.php?id=<?php echo $class_id; ?>">⬅ Back to Class</a>
    </div>
</body>
</html>
