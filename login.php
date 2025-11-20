<?php
ob_start();  // Start output buffering
session_start();  // Start session at the beginning

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        // Set session variables
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];

        // Debugging step (check if it reaches this point)
        error_log("✅ Login successful for: " . $user["email"]);

        // Redirect based on role
        if ($user["role"] === "teacher") {
            header("Location: dashboard.php");
            exit();
        } elseif ($user["role"] === "student") {
            header("Location: student_dashboard.php");
            exit();
        } else {
            $_SESSION["error_message"] = "Invalid user role.";
            header("Location: login.html");
            exit();
        }
    } else {
        // Pass error message via query string
        header("Location: login.html?error=Invalid email or password.");
        exit();
    }
}
ob_end_flush();  // Flush output buffer at the end
?>
