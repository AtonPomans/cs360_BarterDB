<?php
$activePage = 'Dashboard'; // Title
include $_SERVER['DOCUMENT_ROOT'] . "/../includes/header.php"; // header with navbar

// connect database to page
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

if (!$loggedIn) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT * FROM items WHERE user_id = $user_id");
?>


<body>
    <div class="container mt-3">
        <h1>Welcome to Your Dashboard</h1>
        <a href="post_trade.php">Post a Trade</a> | <a href="logout.php">Logout</a>
        <h2>Your Items</h2>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <p><?php echo $row["name"]; ?> - <?php echo $row["description"]; ?></p>
        <?php } ?>
    </div>
</body>

</html>

