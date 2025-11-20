<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$teacher_id = $_SESSION['user_id'];

$sql = "SELECT * FROM tests WHERE created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Tests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            padding: 40px;
            text-align: center;
        }

        table {
            width: 90%;
            margin: auto;
            background: white;
            border-collapse: collapse;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4d2aaf;
            color: white;
        }

        tr:hover {
            background-color: #e7ddff;
        }

        a.button {
            padding: 8px 16px;
            background-color: #4d2aaf;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        a.button:hover {
            background-color: #0d1b5c;
        }
    </style>
</head>
<body>

    <h2>📋 Your Created Tests</h2>

    <table>
        <tr>
            <th>Test ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <a class="button" href="add_questions.php?test_id=<?php echo $row['id']; ?>">➕ Add Questions</a>
                    <a class="button" href="profile.html?test_id=<?php echo $row['id']; ?>">📊 View Responses</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="create_test.php" class="button">➕ Create New Test</a>

</body>
</html>
