<?php
// connect database to page
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

$activePage = 'Dashboard'; // Page title
include $_SERVER['DOCUMENT_ROOT'] . "/../includes/header.php"; // header with navbar

if (!$loggedIn) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT * FROM items WHERE user_id = $user_id");
?>


<body>

    <div class="container mt-3">
        <h1 class="text-center">Welcome to Your Dashboard</h1>
        <!--
        <a href="/dashboard/post_trade.php">Post a Trade</a> | <a href="/auth/logout.php">Logout</a>
        <h2>Your Items</h2>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <p><?php echo $row["name"]; ?> - <?php echo $row["description"]; ?></p>
        <?php } ?>
        -->
    </div>

    <!-- Add Post Form -->
    <div class="container mt-3">

        <h2>Create a New Barter Post</h2>

        <form method="POST" action="create_post.php">
            <h4>Offered Item</h4>
            <label>Name:</label>
            <input type="text" name="offered_name" required>
            <label>Quantity:</label>
            <input type="number" name="offered_quantity" value="1" min="1">

            <h4>Requested Item</h4>
            <label>Name:</label>
            <input type="text" name="requested_name" required>
            <label>Quantity:</label>
            <input type="number" name="requested_quantity" value="1" min="1">

            <br><br>
            <input type="submit" value="Create Barter Post">
        </form>

    </div>


    <hr>


    <!-- List of Posts -->


</body>

</html>

