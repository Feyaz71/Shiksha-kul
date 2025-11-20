<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <meta http-equiv="refresh" content="3;url=code_me.html">
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
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .logout-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        /* Loading Animation */
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4d2aaf;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 10px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Button */
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4d2aaf;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #3949ab;
        }
    </style>
</head>
<body>

<div class="logout-box">
    <h1>Logging Out...</h1>
    <p>You will be redirected to the front page shortly.</p>
    <div class="loader"></div>
    <p>If not redirected, <a href="index.html" class="btn">Click Here</a></p>
</div>

</body>
</html>
