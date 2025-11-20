<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['file_id'])) {
    die("❌ File ID not specified.");
}

$file_id = intval($_GET['file_id']);

// Fetch file info including due_date
$stmt = $conn->prepare("SELECT file_name, due_date FROM files WHERE id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$stmt->bind_result($file_name, $due_date);
$stmt->fetch();
$stmt->close();

// Fetch submissions linked to this file
$sql = "SELECT s.id, u.name AS student_name, s.submitted_at, s.file_name 
        FROM submissions s 
        JOIN users u ON s.student_id = u.id 
        WHERE s.file_id = ?
        ORDER BY s.submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submissions for <?php echo htmlspecialchars($file_name); ?></title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            font-size: 26px;
            margin-bottom: 10px;
            color: #222;
        }

        p {
            font-size: 16px;
            margin-bottom: 25px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            text-align: left;
            padding: 14px 12px;
        }

        th {
            background: #007bff;
            color: #fff;
            border: none;
        }

        td {
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #f1f9ff;
        }

        .ontime {
            color: #28a745;
            font-weight: bold;
        }

        .late {
            color: #dc3545;
            font-weight: bold;
        }

        a.file-link {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        a.file-link:hover {
            text-decoration: underline;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .back:hover {
            background-color: #0056b3;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            background: #eee;
            display: inline-block;
        }

        .ontime.status-badge {
            background: #d4edda;
            color: #155724;
        }

        .late.status-badge {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📥 Submissions for "<?php echo htmlspecialchars($file_name); ?>"</h2>
        <p>🗓️ Due Date: <strong><?php echo htmlspecialchars($due_date); ?></strong></p>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Submitted At</th>
                    <th>Status</th>
                    <th>File</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): 
                    $status_class = (strtotime($row['submitted_at']) <= strtotime($due_date)) ? "ontime" : "late";
                    $status_label = (strtotime($row['submitted_at']) <= strtotime($due_date)) ? "On Time" : "Late";
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span></td>
                        <td><a class="file-link" href="uploads/<?php echo urlencode($row['file_name']); ?>" target="_blank">📄 View</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No submissions yet for this assignment.</p>
        <?php endif; ?>

        <a href="javascript:history.back()" class="back">⬅ Back</a>
    </div>
</body>
</html>
