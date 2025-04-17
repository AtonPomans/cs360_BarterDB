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

            <div class="row">
                <div class="col-md-6">
                    <h4 class="mt-3">Offered Item</h4>
                    <label>Name:</label>
                    <select name="offered_item_id" id="offeredItemSelect" required class="form-control">
                        <option value="">--- Select an item ---</option>
                        <?php
                        while ($row = $item_result->fetch_assoc()) {
                        echo "<option value='" . $row['item_id'] . "'
                        data-value='" . htmlspecialchars($row['value']) . "'
                        data-description='" . htmlspecialchars($row['description']) . "'>"
                        . htmlspecialchars($row['name']) .
                        "</option>";
                        }
                        ?>
                    </select>
                    <label>Quantity:</label>
                    <input type="number" name="offered_quantity" id="offeredQuantity" value="1" min="1" class="form-control mb-2">
                    <div id="offeredItemDetails" class="mt-2 mb-3">
                        <p><strong>Value:</strong> <span id="offeredItemValue">--</span></p>
                        <p><strong>Description:</strong> <span id="offeredItemDescription">--</span></p>
                    </div>
                </div>


                <?php
                $item_result = $conn->query("SELECT * FROM items");
                ?>

                <div class="col-md-6">
                    <h4 class="mt-3">Requested Item</h4>
                    <label>Name:</label>
                    <select name="requested_item_id" id="requestedItemSelect" required class="form-control">
                        <option value="">--- Select an item ---</option>
                        <?php
                        while ($row = $item_result->fetch_assoc()) {
                        echo "<option value='" . $row['item_id'] . "'
                        data-value='" . htmlspecialchars($row['value']) . "'
                        data-description='" . htmlspecialchars($row['description']) . "'>"
                        . htmlspecialchars($row['name']) .
                        "</option>";
                        }
                        ?>
                    </select>
                    <label>Quantity:</label>
                    <input type="number" name="requested_quantity" id="requestedQuantity" value="1" min="1" class="form-control mb-2">
                    <div id="requestedItemDetails" class="mt-2 mb-3">
                        <p><strong>Value:</strong> <span id="requestedItemValue">--</span></p>
                        <p><strong>Description:</strong> <span id="requestedItemDescription">--</span></p>
                    </div>
                </div>
            </div>


            <h4 class="mt-3">Your Trusted Partner</h4>
            <label for="partner_id">(who will act on your behalf):</label>
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

    <script src="/assets/js/updateItemAttributes.js"></script>

</body>

</html>

