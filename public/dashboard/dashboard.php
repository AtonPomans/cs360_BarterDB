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



        <hr>



        <!-- Your Posts -->
        <div class="box">
            <h2>Your Barter Posts</h2>

            <?php


            $query = "

            SELECT bp.*,
            i1.name AS offered_name,
            i2.name AS requested_name,
            t.transaction_id,
            t.is_complete
            FROM barter_post bp
            JOIN items i1 ON bp.offered_item = i1.item_id
            JOIN items i2 ON bp.requested_item = i2.item_id
            LEFT JOIN transactions t
            ON t.post1_id = bp.post_id OR t.post2_id = bp.post_id
            WHERE bp.poster_id = ?
            ORDER BY bp.status DESC, bp.post_id DESC

            ";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()):
            ?>

            <div>
                <strong>Post #<?= $row['post_id'] ?></strong><br>                                           <!-- post number -->
                Offering: <?= $row['offered_name'] ?> (x<?= $row['offered_quantity'] ?>)<br>                <!-- offered item and quantity -->
                Requesting: <?= $row['requested_name'] ?> (x<?= $row['requested_quantity'] ?>)<br>          <!-- requested item and quantity -->
                Status: <?= ucfirst($row['status']) ?><br>                                                  <!-- post status (open/closed) -->
                <?php if ($row['transaction_id']): ?>
                <span class="success">✅ Matched (Transaction #<?= $row['transaction_id'] ?>)</span><br>    <!-- match success -->
                Trade Status: <?= $row['is_complete'] ? "✅ Completed" : "⏳ In Progress" ?>                <!-- transaction status (completed/in progress) -->
                <?php else: ?>
                <span class="warning">⏳ Waiting for match...</span>                                        <!-- match pending -->
                <?php endif; ?>
            </div>

            <hr>
            <?php endwhile; ?>

        </div>




        <!-- Your Transactions -->
        <div class="box">
            <h2>Your Transactions</h2>

            <?php
            $tx_query = "
            SELECT * FROM transactions
            WHERE user1_id = ? OR user2_id = ?
            ORDER BY created_at DESC
            ";
            $stmt = $conn->prepare($tx_query);
            $stmt->bind_param("ii", $user_id, $user_id);
            $stmt->execute();
            $tx_result = $stmt->get_result();

            while ($tx = $tx_result->fetch_assoc()):
            ?>

            <div>
                <strong>Transaction #<?= $tx['transaction_id'] ?></strong><br>          <!-- transaction number -->
                Your Code:                                                              <!-- logic for correct hash half -->
                <?php
                if ($user_id == $tx['user1_id']) {
                echo $tx['part_a_code'] . " (first half)";
                } elseif ($user_id == $tx['user2_id']) {
                echo $tx['part_y_code'] . " (second half)";
                } else {
                echo "N/A";
                }
                ?><br>
                Status: <?= $tx['is_complete'] ? "✅ Completed" : "⏳ In Progress" ?>   <!-- transaction status -->
            </div>

            <hr>
            <?php endwhile; ?>



    </div>

</body>

</html>

