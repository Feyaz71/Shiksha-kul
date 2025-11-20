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
$user_id = $_SESSION["user_id"]; // Logged-in user

// Fetch class details
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$class) {
    die("Error: Class not found.");
}

// ✅ Fetch uploaded assignments (files) with submission status
$files_stmt = $conn->prepare("
    SELECT f.id, f.file_name, f.due_date,
           CASE 
               WHEN s.id IS NOT NULL THEN 'Submitted' 
               ELSE 'Not Submitted' 
           END AS submission_status,
           s.submitted_at
    FROM files f
    LEFT JOIN submissions s 
        ON f.id = s.file_id AND s.student_id = ?
    WHERE f.class_id = ?
    ORDER BY f.uploaded_at DESC
");
$files_stmt->bind_param("ii", $user_id, $class_id);
$files_stmt->execute();
$files = $files_stmt->get_result();

// ✅ Fetch enrolled students
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

// ✅ Handle student messaging
$receiver_id = isset($_GET["receiver_id"]) ? intval($_GET["receiver_id"]) : 0;

if ($receiver_id) {
    $update_stmt = $conn->prepare("UPDATE messages SET seen = 1 WHERE class_id = ? AND receiver_id = ? AND sender_id = ?");
    $update_stmt->bind_param("iii", $class_id, $user_id, $receiver_id);
    $update_stmt->execute();
    $update_stmt->close();

    $messages_stmt = $conn->prepare("SELECT * FROM messages WHERE class_id = ? AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) ORDER BY created_at ASC");
    $messages_stmt->bind_param("iiiii", $class_id, $user_id, $receiver_id, $receiver_id, $user_id);
    $messages_stmt->execute();
    $messages = $messages_stmt->get_result();
}
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

        /* .sidebar a:hover {
            background-color:rgb(165, 129, 206);
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
            background-color: white;
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
            color: #4d2aaf;
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
            padding: 12px;
            background-color: #fff;
            margin-bottom: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 0px;
            background-color:rgb(199, 234, 29);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color:rgb(87, 171, 57);
        }

        .btn-danger {
            background-color: #ff6f61;
        }

        .btn-danger:hover {
            background-color: #e6574d;
        }

        .assignment-details {
            transition: all 0.3s ease-in-out;
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2 style="color: white;">Student Pannel</h2>
    <a href="student_dashboard.php" class="<?= ($current_page == 'view_class_st.php') ?>">🏠 Home</a>
    <a href="join_class.php">📚 Join Class</a>
    <a href="profile.html">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="class-details">
    <h1 style="display: flex; justify-content: space-between; align-items: center;">
        <span><?php echo htmlspecialchars($class['class_name']); ?></span>
    </h1>
    <!-- Add this button at the top inside .class-details -->
    
    <?php $student_id = $_SESSION["user_id"]; // Get logged-in student ID ?>

    <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
        <?php if (isset($class_id) && isset($student_id)): ?>
            <a href="message_teacher.php?class_id=<?php echo urlencode($class_id); ?>&receiver_id=<?php echo urlencode($student_id); ?>" 
                class="btn message-button" 
                data-class-id="<?php echo $class_id; ?>" 
                data-receiver-id="<?php echo $student_id; ?>" 
                style="background-color: #ff9800; padding: 10px 15px; text-decoration: none; color: white; border-radius: 5px; position: relative;">
                ✉️ TALK TO TEACHER 
                <span class="message-notification" style="display: none; position: absolute; top: 5px; right: 5px; width: 10px; height: 10px; background: red; border-radius: 50%;"></span>
            </a>

        <?php else: ?>
            <p>Error: Missing class ID or student ID.</p>
        <?php endif; ?>
    </div>


        <!-- Uploaded Assignments -->
        <h2 onclick="toggleAssignments()" 
           style="cursor: pointer; 
           border-radius: 25px; 
           border: 2px solid #4d2aaf; 
           background-color:rgb(240, 234, 255); 
           padding: 10px 20px; 
           display: inline-block; 
           color: #4d2aaf;">
           ✒️Assignments
        </h2>
        <ul class="file-list" id="assignmentList" style="display: none;">

            <?php while ($file = $files->fetch_assoc()): ?>
                <li>
                    <div class="assignment-header" onclick="toggleAssignmentDetails(this)">
                        <span style="cursor: pointer; font-weight: bold;">
                            📄 <?php echo htmlspecialchars($file['file_name']); ?>
                        </span>
                        <span style="color: <?php echo ($file['submission_status'] == 'Submitted') ? 'green' : 'red'; ?>;">
                            <?php echo $file['submission_status']; ?>
                        </span>
                    </div>

                    <div class="assignment-details" style="display: none; margin-top: 10px;">
                        <p>📅 Due Date: <?php echo htmlspecialchars($file['due_date']); ?></p><br>
                        <a href="download.php?file=<?php echo urlencode($file['file_name']); ?>">⬇️ Download</a>
                        <?php if ($file['submission_status'] === 'Not Submitted'): ?>
                            <a href="submit_assignment.php?assignment_id=<?php echo $file['id']; ?>&class_id=<?php echo $class_id; ?>" class="btn" style="margin-left: 10px;">
                                📤 Submit Assignment
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
         <!-- Enrolled Students -->


         <h2 onclick="toggleStudents()" 
            style="cursor: pointer; 
                border-radius: 25px; 
                border: 2px solid #4d2aaf; 
                background-color:rgb(240, 234, 255); 
                padding: 10px 20px; 
                display: inline-block; 
                color: #4d2aaf;">
            📋 Enrolled Students
         </h2>

         <ul class="student-list" id="studentList" style="display: none;">
            
        <?php while ($student = $students->fetch_assoc()): ?>
            <li style="display: flex; justify-content: space-between; align-items: center; padding: 12px;">
                <span>👤 <?php echo htmlspecialchars($student['name']); ?></span>
                
                <div class="student-actions">
                    <?php if ($student['id'] != $_SESSION["user_id"]): ?>
                        
                        <a href="message_student.php?class_id=<?php echo $class_id; ?>&receiver_id=<?php echo $student['id']; ?>" 
                            class="btn message-button" 
                            data-class-id="<?php echo $class_id; ?>" 
                            data-receiver-id="<?php echo $student['id']; ?>" 
                            style="background-color: #ff9800; text-decoration: none; color: white; border-radius: 5px; position: relative;">
                            ✉️ Messages 
                            <span class="message-notification" style="display: none; position: absolute; top: 5px; right: 5px; width: 10px; height: 10px; background: red; border-radius: 50%;"></span>
                        </a>


                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>

        </ul>

        <!-- Attendance Management -->
        <h2 onclick="redirectToAttendance()" 
        style="cursor: pointer; 
        border-radius: 25px; 
        border: 2px solid #4d2aaf; 
        background-color:rgb(240, 234, 255); 
        padding: 10px 20px; 
        display: inline-block; 
        color: #4d2aaf;">
        📅 Attendance
        </h2>

        <script>
            function redirectToAttendance() {
                let params = new URLSearchParams(window.location.search);
                let classId = params.get("id");  // Getting class_id from URL
                let studentId = "<?php echo $_SESSION['user_id']; ?>";
                 // Fetching student_id from PHP session

                if (classId && studentId) {
                    window.location.href = "attendance_student.php?id=" + classId + "&student_id=" + studentId; // Pass both IDs
                } else {
                    alert("Class ID or Student ID is missing!"); // Handle missing values
                }
            }
        </script>

        <!-- Chatbot Redirect -->
        <h2 onclick="redirectToChat()"
        style="cursor: pointer; 
        border-radius: 25px; 
        border: 2px solid #4d2aaf; 
        background-color:rgb(240, 234, 255); 
        padding: 10px 20px; 
        display: inline-block; 
        color: #4d2aaf;">
        💬 Ask Your Doubts
        </h2>

        <script>
            function redirectToChat() {
                window.location.href = "chat.html";
            }
        </script>

        <!-- Student Test Dashboard Redirect -->
        <h2 onclick="redirectToStudentDashboard()"
        style="cursor: pointer; 
        border-radius: 25px; 
        border: 2px solid #4d2aaf; 
        background-color:rgb(240, 234, 255); 
        padding: 10px 20px; 
        display: inline-block; 
        color: #4d2aaf;">
        📚 My Test Dashboard
        </h2>

        <script>
            function redirectToStudentDashboard() {
                window.location.href = "student_test_dashboard.php";
            }
        </script>



        
        <a href="student_dashboard.php" class="btn btn-danger">⬅️ Back to Dashboard</a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    checkNewMessages(); // Check unread messages on page load
    setInterval(checkNewMessages, 5000); // Auto-refresh unread messages every 5 seconds
});

// Function to check unread messages for each recipient separately
function checkNewMessages() {
    document.querySelectorAll(".message-button").forEach(button => {
        let classId = button.getAttribute("data-class-id");
        let receiverId = button.getAttribute("data-receiver-id"); // This must be the logged-in user's ID!

        fetch(`check_unread_messages.php?class_id=${classId}&receiver_id=${receiverId}`)
            .then(response => response.json())
            .then(data => {
                let notification = button.querySelector(".message-notification");

                if (data.unread_count > 0) {
                    notification.style.display = "inline"; 
                } else {
                    notification.style.display = "none"; 
                }
            })
            .catch(error => console.error("Error fetching message count:", error));
    });
}

// Run every few seconds to update message status
setInterval(checkNewMessages, 5000);






// Check if the message form exists before attaching event listener
let messageForm = document.getElementById("message-form");
if (messageForm) {
    messageForm.addEventListener("submit", function(event) {
        event.preventDefault();
        
        let messageInput = document.getElementById("message-input");
        let message = messageInput.value.trim();
        
        if (message === "") return;

        let formData = new FormData();
        formData.append("message", message);

        fetch(window.location.href, {
            method: "POST",
            body: formData
        }).then(() => {
            messageInput.value = "";
            loadMessages();
            checkNewMessages(); // Update unread message count after sending
        });
    });
}

// Function to check unread messages and show/hide red dot
function checkNewMessages() {
    document.querySelectorAll(".message-button").forEach(button => {
        let classId = button.getAttribute("data-class-id");
        let receiverId = button.getAttribute("data-receiver-id");

        fetch(`check_unread_messages.php?class_id=${classId}&receiver_id=${receiverId}`)
            .then(response => response.json()) // Expect JSON response
            .then(data => {
                let notification = button.querySelector(".message-notification");
                if (data.unread_count > 0) {
                    notification.style.display = "inline"; // Show red dot
                } else {
                    notification.style.display = "none"; // Hide red dot
                }
            })
            .catch(error => console.error("Error fetching message count:", error));
    });
}

function toggleAssignmentDetails(headerElement) {
    const details = headerElement.nextElementSibling;
    details.style.display = details.style.display === "none" ? "block" : "none";
}
function toggleAssignments() {
    const list = document.getElementById("assignmentList");
    list.style.display = list.style.display === "none" ? "block" : "none";
}
function toggleStudents() {
    const list = document.getElementById("studentList");
    list.style.display = list.style.display === "none" ? "block" : "none";
}


</script>

</body>
</html>

<?php 
$files_stmt->close(); 
$students_stmt->close(); 
?>
