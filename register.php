<?php
session_start();
include "config.php"; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = strtolower(trim($_POST["role"])); // Convert role to lowercase for consistency

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        echo "<script>alert('All fields are required!'); window.location.href='register.html';</script>";
        exit();
    }

    // Validate password match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='register.html';</script>";
        exit();
    }

    // Validate role (only "student" or "teacher" allowed)
    if (!in_array($role, ["student", "teacher"])) {
        echo "<script>alert('Invalid role selected!'); window.location.href='register.html';</script>";
        exit();
    }

    // Hash password before storing
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if the email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Email already registered! Please use a different email.'); window.location.href='register.html';</script>";
    } else {
        // Insert user into users table
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            // If the role is student, insert into students table
            if ($role == "student") {
                $student_stmt = $conn->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
                $student_stmt->bind_param("ss", $name, $email);
                $student_stmt->execute();
                $student_stmt->close();
            }

            echo "<script>alert('Registration successful!'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>
