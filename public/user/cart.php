
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

$activePage = 'Cart';
include $_SERVER['DOCUMENT_ROOT'] . "/../includes/header.php";

// Check if the user is logged in

if (!$loggedIn) {
    header("Location: /auth/login.php"); // redirect unknown users
    exit();
}

$user_id = $_SESSION["user_id"];

?>

<body>

    <div class="container mt-3">
        <h1 class="text-center">Your Listings</h1>
    </div>

    <div class = "container mt-3">

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
                <strong>Post #<?= $row['post_id'] ?></strong><br>
                Offering: <?= $row['offered_name'] ?> (x<?= $row['offered_quantity'] ?>)<br>
                Requesting: <?= $row['requested_name'] ?> (x<?= $row['requested_quantity'] ?>)<br>
                Status: <?= ucfirst($row['status']) ?><br>
                <?php if ($row['transaction_id']): ?>
                <span class="success">✅ Matched (Transaction #<?= $row['transaction_id'] ?>)</span><br>
                Trade Status: <?= $row['is_complete'] ? "✅ Completed" : "⏳ In Progress" ?>
                <?php else: ?>
                <span class="warning">⏳ Waiting for match...</span>
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
            WHERE user_a_id = ? OR user_b_id = ? OR user_x_id = ? OR user_y_id = ?
            ORDER BY created_at DESC
            ";
            $stmt = $conn->prepare($tx_query);
            $stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
            $stmt->execute();
            $tx_result = $stmt->get_result();

            while ($tx = $tx_result->fetch_assoc()):
            $role = '';
            if ($user_id == $tx['user_a_id']) $role = 'A (Requester of P)';
            if ($user_id == $tx['user_b_id']) $role = 'B (Sender of E)';
            if ($user_id == $tx['user_x_id']) $role = 'X (Owner of P)';
            if ($user_id == $tx['user_y_id']) $role = 'Y (Receiver of E)';
            ?>
            <div>
                <strong>Transaction #<?= $tx['transaction_id'] ?></strong><br>
                Your Role: <strong><?= $role ?></strong><br>
                Status: <?= $tx['is_complete'] ? "✅ Completed" : "⏳ In Progress" ?><br>
                Your Code:
                <?php
                if ($user_id == $tx['user_a_id']) {
                echo $tx['part_a_code'] . " (first half)";
                } elseif ($user_id == $tx['user_x_id']) {
                echo $tx['part_y_code'] . " (second half)";
                } else {
                echo "Hidden – only for A and X";
                }
                ?><br>

                <!-- Code Input Form for B -->
                <?php if ($user_id == $tx['user_b_id'] && !$tx['e_sent']): ?>
                <form method="POST" action="verify_b.php">
                    <input type="hidden" name="transaction_id" value="<?= $tx['transaction_id'] ?>">
                    <label>Enter 8-digit Code from A:</label>
                    <input type="text" name="part_a_code" maxlength="8" required>
                    <button type="submit">Submit</button>
                </form>
                <?php endif; ?>

                <!-- Code Input Form for Y -->
                <?php if ($user_id == $tx['user_y_id'] && $tx['e_sent'] && !$tx['p_sent']): ?>
                <form method="POST" action="verify_y.php">
                    <input type="hidden" name="transaction_id" value="<?= $tx['transaction_id'] ?>">
                    <label>Enter 8-digit Code from X:</label>
                    <input type="text" name="part_y_code" maxlength="8" required>
                    <button type="submit">Complete Exchange</button>
                </form>
                <?php endif; ?>

            </div>
            <hr>
            <?php endwhile; ?>
        </div>
</body>

</html>

