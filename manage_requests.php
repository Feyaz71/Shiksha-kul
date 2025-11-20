<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "teacher") {
    header("Location: dashboard.php");
    exit;
}

$teacher_id = $_SESSION["user_id"];

// Fetch pending join requests for classes managed by this teacher
$query = "SELECT cr.id, cr.student_id, u.name AS student_name, c.class_name 
          FROM class_requests cr
          JOIN users u ON cr.student_id = u.id
          JOIN classes c ON cr.class_id = c.id
          WHERE c.teacher_id = ? AND cr.status = 'pending'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$requests = $stmt->get_result();

// Handle Approve or Reject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["request_id"])) {
    $request_id = $_POST["request_id"];
    $action = $_POST["action"]; // 'approve' or 'reject'

    // Fetch student_id and class_name before updating
    $fetch_query = "SELECT cr.student_id, c.class_name FROM class_requests cr
                    JOIN classes c ON cr.class_id = c.id WHERE cr.id = ?";
    $stmt = $conn->prepare($fetch_query);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "<script>alert('Error: Request not found!'); window.location.href='manage_requests.php';</script>";
        exit;
    }

    // Prepare notification message
    $message = ($action == "approve") 
        ? "Your join request for " . htmlspecialchars($row["class_name"]) . " has been approved!" 
        : "Your join request for " . htmlspecialchars($row["class_name"]) . " was rejected.";

    // Store notification in users table
    $notify_query = "UPDATE users SET notification = ? WHERE id = ?";
    $stmt = $conn->prepare($notify_query);
    $stmt->bind_param("si", $message, $row["student_id"]);
    $stmt->execute();

    if ($action == "approve") {
        // Approve request: Update status and add student to class_students table
        $update_query = "UPDATE class_requests SET status = 'approved' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        $insert_query = "INSERT INTO class_students (class_id, student_id) 
                         SELECT class_id, student_id FROM class_requests WHERE id = ?";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    } else {
        // Reject request: Just update the request status
        $update_query = "UPDATE class_requests SET status = 'rejected' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }

    echo "<script>alert('Request processed successfully!'); window.location.href='manage_requests.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Join Requests</title>
    <style>
        * { font-family: Arial, sans-serif; }
        body { background-color: #e7ddff; padding: 20px; }
        h1 { color: #333; }
        .request-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: auto;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        button { padding: 8px 15px; border: none; cursor: pointer; font-size: 14px; border-radius: 5px; }
        .approve { background-color: green; color: white; }
        .reject { background-color: red; color: white; }
    </style>
</head>
<body>

<h1>Manage Join Requests</h1>

<div class="request-container">
    <table>
        <tr>
            <th>Student Name</th>
            <th>Class Name</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $requests->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row["student_name"]) ?></td>
                <td><?= htmlspecialchars($row["class_name"]) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="action" value="approve" class="approve">✅ Approve</button>
                        <button type="submit" name="action" value="reject" class="reject">❌ Reject</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
