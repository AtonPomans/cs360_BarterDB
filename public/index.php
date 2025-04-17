<?php
session_start();
$activePage = 'Home';
if (isset($_SESSION["user_id"])) {
    header("Location: /dashboard/dashboard.php"); // Redirect logged-in users
    exit();
}
include $_SERVER['DOCUMENT_ROOT'] . "/../includes/header.php";

include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php"; // Ensure this file exists and is correctly set up

// Check if the user is logged in

?>

<body>
    <div class="container text-center mt-5">
        <h1>Welcome to the Anonymous Barter Exchange</h1>
        <p>Trade goods and services securely and anonymously.</p>

        <a href="/auth/register.php" class="btn btn-primary">Register</a>
        <a href="/auth/login.php" class="btn btn-success">Login</a>
    </div>
</body>

</html>
