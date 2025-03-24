<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT * FROM items WHERE user_id = $user_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Welcome to Your Dashboard</h1>
    <a href="post_trade.php">Post a Trade</a> | <a href="logout.php">Logout</a>
    <h2>Your Items</h2>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <p><?php echo $row["name"]; ?> - <?php echo $row["description"]; ?></p>
    <?php } ?>
</body>
</html>
