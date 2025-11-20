<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Convert email to lowercase
    $email = strtolower(trim($_POST["email"]));

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(email) = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));

        // Store the token in the database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Create a password reset link (Fixed extra slash)
        $resetLink = "http://localhost/BBJ/reset_password.php?token=$token";

        // Send an email to the user (Replace with a real mail function)
        mail($email, "Password Reset Request", "Click here to reset your password: $resetLink", "From: no-reply@yourwebsite.com");

        // Redirect to a confirmation page
        header("Location: forgot_password.html?success=Check your email for the reset link.");
        exit();
    } else {
        header("Location: forgot_password.html?error=Email not found.");
        exit();
    }
}
?>
