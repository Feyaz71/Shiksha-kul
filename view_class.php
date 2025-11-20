<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    die("Error: Class ID not provided.");
}

$class_id = intval($_GET["id"]);

// Fetch class details
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$class) {
    die("Error: Class not found.");
}

// Fetch uploaded files
$files_stmt = $conn->prepare("SELECT * FROM files WHERE class_id = ?");
$files_stmt->bind_param("i", $class_id);
$files_stmt->execute();
$files = $files_stmt->get_result();

// Fetch enrolled students
$students_stmt = $conn->prepare("
    SELECT users.id, users.name 
    FROM users 
    JOIN class_students ON users.id = class_students.student_id 
    WHERE class_students.class_id = ? 
    ORDER BY users.name ASC
");
$students_stmt->bind_param("i", $class_id);
$students_stmt->execute();
$students = $students_stmt->get_result();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Class</title>
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
            background-color: #4d2aa7;
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

        /* Class Details */
        .class-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: whitesmoke;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        /* File List */
        .file-list {
            list-style: none;
            padding: 0;
        }

        .file-list li {
            padding: 12px 20px;
            background-color: #fff;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-list a {
            text-decoration: none;
            color: #4d2aa7;
            font-weight: bold;
            margin-left: 10px;
            transition: color 0.3s;
        }

        .file-list a:hover {
            color:rgb(165, 129, 206);
        }

        /* Student List */
        .student-list {
            list-style: none;
            padding: 0;
        }

        .student-list li {
            display: flex;
            justify-content: space-between; /* Pushes the Remove button to the right */
            align-items: center;
            padding: 12px;
            background-color: #fff;
            margin-bottom: 8px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            margin-bottom: 15px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color:rgb(165, 129, 206);
        }

        .btn-danger {
            background-color: #ff6f61;
        }

        .btn-danger:hover {
            background-color: #e6574d;
        }

        /* Remove button styling */
        .student-list .remove-btn {
            margin-left: auto; /* Pushes button to the right */
            background: none;  /* Removes background color */
            color: black;       /* Makes text color black */
            border: none;       /* Removes border */
            cursor: pointer;    /* Adds a pointer on hover */
            text-decoration: none; /* Removes underline */
            padding: 5px 10px;
        }

        .student-list .remove-btn:hover {
            text-decoration: underline; /* Adds underline on hover */
        }
        
        .student-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-message {
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 6px 6px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-message:hover {
            background-color: #45a049;
        }
        
        .btn-remove {
            background-color: #ff6f61;
            color: white;
            padding: 10px 15px;
            margin: 15px 0px 0px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-remove:hover {
            background-color: #e6574d;
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2 style="color: white;">Teacher Panel</h2>
    <a href="dashboard.php" class="<?= ($current_page == 'view_class.php')?'active':'' ?>">🏠 Home</a>
    <a href="create_class.php" class="<?= ($current_page == 'create_class.php')?'active':'' ?>">✒️ Create Class</a>
    <a href="profile.php" class="<?= ($current_page == 'profile.php')?'active':'' ?>">👤 Profile</a>
    <a href="logout.php" class="<?= ($current_page == 'logout.php')?'active':'' ?>">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="class-details">
        <h1><?php echo htmlspecialchars($class['class_name']); ?></h1>

        <!-- Uploaded Assignments -->
        <h2 onclick="toggleAssignments()" 
            style="cursor: pointer; 
            border-radius: 25px; 
            border: 2px solid #4d2aa7; 
            background-color: rgb(240, 234, 255);; 
            padding: 10px 20px; 
            display: inline-block; 
            color: #4d2aa7;">
            ✒️ Assignments
        </h2>

        <ul class="file-list" id="assignmentList" style="display: none;">
            <?php while ($file = $files->fetch_assoc()): ?>
                <li>
                    <!-- Assignment Header (Click to Toggle) -->
                    <div class="assignment-header" onclick="toggleAssignmentDetails(this)" 
                        style="cursor: pointer; font-weight: bold; display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #ddd;">
                        <span>📄 <?php echo htmlspecialchars($file['file_name']); ?></span>
                        
                    </div>

                    <!-- Assignment Details (Initially Hidden) -->
                    <div class="assignment-details" style="display: none; padding: 10px; border: 1px solid #ddd; margin-top: 5px; border-radius: 5px;">
                        <a href="download.php?file=<?php echo urlencode($file['file_name']); ?>" 
                        class="btn" 
                        style="background-color: #4CAF50; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none;">
                        ⬇️ Download
                        </a>

                        <a href="delete.php?id=<?php echo $file['id']; ?>" 
                        class="btn-danger" 
                        style="background-color: #f44336; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none; margin-left: 10px;" 
                        onclick="return confirm('Are you sure you want to delete this file?')">
                        ❌ Delete
                        </a>

                        <a href="view_submissions.php?file_id=<?php echo $file['id']; ?>" 
                        class="btn" 
                        style="margin-left: 10px; background-color: #2196f3; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none;">
                        📥 Submissions
                        </a>
                    </div>
                </li>
            <?php endwhile; ?>

            <a href="upload.php?class_id=<?php echo $class_id; ?>" 
                class="btn" 
                style="margin-top: 10px; display: inline-block; background-color: #4d2aa7; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
                📤 Upload Assignments
            </a>
        </ul>

        

        <script>
            function toggleAssignments() {
                var assignmentList = document.getElementById("assignmentList");
                assignmentList.style.display = (assignmentList.style.display === "none" || assignmentList.style.display === "") ? "block" : "none";
            }

            function toggleAssignmentDetails(element) {
                var details = element.nextElementSibling;
                details.style.display = (details.style.display === "none" || details.style.display === "") ? "block" : "none";
            }
        </script>





        <!-- Enrolled Students -->

        <h2 onclick="toggleStudents()" 
           style="cursor: pointer; 
           border-radius: 25px; 
           border: 2px solid #4d2aa7; 
           background-color: rgb(240, 234, 255);; 
           padding: 10px 20px; 
           display: inline-block; 
           color: #4d2aa7;">
           📋 Enrolled Students
        </h2>

        <ul class="student-list" id="studentList" style="display: none;">
            <?php while ($student = $students->fetch_assoc()): ?>
                <li>
                    <!-- Student Header (Click to Toggle) -->
                    <div class="student-header" onclick="toggleStudentDetails(this)" 
                        style="cursor: pointer; font-weight: bold; display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #ddd;">
                        <span>👤 <?php echo htmlspecialchars($student['name']); ?></span>
                        
                    </div>

                    <!-- Student Actions (Initially Hidden) -->
                    <div class="student-actions" style="display: none; padding: 10px; border: 1px solid #ddd; margin-top: 5px; border-radius: 5px;">
                        <a href="message_student.php?class_id=<?php echo $class_id; ?>&receiver_id=<?php echo $student['id']; ?>" 
                        class="btn message-button" 
                        style="background-color: #ff9800; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; position: relative;">
                        ✉️ Messages
                        <span class="message-notification" style="display: none; position: absolute; top: 5px; right: 5px; width: 10px; height: 10px; background: red; border-radius: 50%;"></span>
                        </a>

                        <a href="remove_student.php?class_id=<?php echo $class_id; ?>&student_id=<?php echo $student['id']; ?>" 
                        class="btn-remove"
                        style="background-color: #f44336; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; margin-left: 10px;" 
                        onclick="return confirm('Are you sure you want to remove this student?');">
                        ❌ Remove
                        </a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <script>
            function toggleStudents() {
                var studentList = document.getElementById("studentList");
                studentList.style.display = (studentList.style.display === "none" || studentList.style.display === "") ? "block" : "none";
            }

            function toggleStudentDetails(element) {
                var details = element.nextElementSibling;
                details.style.display = (details.style.display === "none" || details.style.display === "") ? "block" : "none";
            }
        </script>

        <!-- Attendance Management -->
        <h2 onclick="redirectToAttendance()" 
        style="cursor: pointer; 
        border-radius: 25px; 
        border: 2px solid #4d2aa7; 
        background-color: rgb(240, 234, 255);; 
        padding: 10px 20px; 
        display: inline-block; 
        color: #4d2aa7;">
        📅 Attendance
        </h2>

        <script>
            function redirectToAttendance() {
                let params = new URLSearchParams(window.location.search);
                let classId = params.get("id"); // Use only "id" to match PHP expectation

                if (classId) {
                    window.location.href = "attendance.php?id=" + classId; // Pass "id" correctly
                } else {
                    alert("Class ID is missing!"); // Optional error handling
                }
            }

        </script>

        <!-- Chatbot Redirect -->
        <h2 onclick="redirectToChat()"
        style="cursor: pointer; 
        border-radius: 25px; 
        border: 2px solid #4d2aa7; 
        background-color: rgb(240, 234, 255);; 
        padding: 10px 20px; 
        display: inline-block; 
        color: #4d2aa7; 
        /* margin-top: 15px;"> 
        💬 AI Assistance
        </h2>

        <script>
            function redirectToChat() {
                window.location.href = "chat.html";
            }
        </script>

        <!-- Test Dashboard Redirect -->
        <h2 onclick="redirectToTestDashboard()"
            style="cursor: pointer; 
            border-radius: 25px; 
            border: 2px solid #4d2aa7; 
            background-color: rgb(240, 234, 255); 
            padding: 10px 20px; 
            display: inline-block; 
            color: #4d2aa7;">
            🧪 Test Dashboard
        </h2>

        <script>
            function redirectToTestDashboard() {
                const classId = <?php echo json_encode($class_id); ?>;
                window.location.href = `test_dashboard.php?class_id=${classId}`;
            }
        </script>






        <a href="dashboard.php" class="btn btn-danger">⬅️ Back to Dashboard</a><br>
    </div>
</div>

</body>
</html>

<?php 
$files_stmt->close(); 
$students_stmt->close(); 
?>
