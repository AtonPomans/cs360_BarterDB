<?php
// connect database to page
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

$activePage = 'Dashboard';
include $_SERVER['DOCUMENT_ROOT'] . "/../includes/header.php";

if (!$loggedIn) {
    header("Location: /auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
?>

<body>

    <div class="container mt-3">

        <!-- Add Post Form -->
        <h2>Create a New Barter Post</h2>

        <form method="POST" action="create_post.php">
            <?php
            $item_result = $conn->query("SELECT * FROM items");
            ?>

            <h4>Offered Item</h4>
            <label>Name:</label>
            <!-- <input type="text" name="offered_name" required> -->
            <select name="offered_item_id" id="item" required>
                <option value="">--- Select an item ---</option>
                <?php
                if ($item_result->num_rows > 0) {
                while($row = $item_result->fetch_assoc()) {
                echo "<option value='" . $row['item_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                }
                ?>
            </select>
            <label>Quantity:</label>
            <input type="number" name="offered_quantity" value="1" min="1">


            <?php
            $item_result = $conn->query("SELECT * FROM items");
            ?>

            <h4>Requested Item</h4>
            <label>Name:</label>
            <!-- <input type="text" name="requested_name" required> -->
            <select name="requested_item_id" id="item" required>
                <option value="">--- Select an item ---</option>
                <?php
                if ($item_result->num_rows > 0) {
                while($row = $item_result->fetch_assoc()) {
                echo "<option value='" . $row['item_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                }
                ?>
            </select>
            <label>Quantity:</label>
            <input type="number" name="requested_quantity" value="1" min="1">


            <label for="partner_id">Your Trusted Partner (who will act on your behalf):</label>
            <select name="partner_id" required>
                <option value="">Select a partner</option>
                <?php
                $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE user_id != ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $partners = $stmt->get_result();
                while ($partner = $partners->fetch_assoc()):
                ?>
                <option value="<?= $partner['user_id'] ?>"><?= htmlspecialchars($partner['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <br><br>
            <input type="submit" value="Create Barter Post">
        </form>

        <hr>
    </div>


        <!-- Show All Listings -->

    <div class = "container mt-3">
        <div class="box">

            <?php
            $sql = "
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
            ORDER BY bp.status DESC, bp.post_id DESC
            ";
            $result = $conn->query($sql);
            ?>

            <h1 class="mb-4">Barter Post Listings</h1>

            <?php
            while ($row = $result->fetch_assoc()):
            ?>
            <div>
                <strong>Post #<?= $row['post_id'] ?></strong><br>
                Offering: <?= $row['offered_name'] ?> (x<?= $row['offered_quantity'] ?>)<br>
                Requesting: <?= $row['requested_name'] ?> (x<?= $row['requested_quantity'] ?>)<br>
            </div>
            <hr>
            <?php endwhile; ?>
        </div>
    </div>

</body>

</html>

