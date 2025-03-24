<?php
session_start();
include '../config/database.php'; // Ensure this file exists and is correctly set up

// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php"); // Redirect logged-in users
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barter Exchange</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container text-center mt-5">
        <h1>Welcome to the Anonymous Barter Exchange</h1>
        <p>Trade goods and services securely and anonymously.</p>
        
        <a href="register.php" class="btn btn-primary">Register</a>
        <a href="login.php" class="btn btn-success">Login</a>
    </div>
</body>
</html>
