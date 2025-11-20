<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize token
    $token = trim($_POST["token"]);
    $newPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Verify token (Ensure token is not expired)
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Update password and remove token
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $newPassword, $user["id"]);
        $stmt->execute();

        header("Location: login.html?success=Password reset successfully.");
        exit();
    } else {
        header("Location: reset_password.php?error=Invalid or expired token.");
        exit();
    }
}
?>
