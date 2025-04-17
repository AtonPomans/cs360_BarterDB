<?php
session_start();
$activePage = 'Admin Dashboard';

include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

// Admin access check
if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit();
}

// Verify admin status
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

if (!$is_admin) {
    header("Location: /dashboard/dashboard.php");
    exit();
}

// Handle suspend/unsuspend toggle
if (isset($_GET['toggle_suspend'])) {
    $uid = intval($_GET['toggle_suspend']);
    $conn->query("UPDATE users SET is_suspended = IFNULL(1 - is_suspended, 1) WHERE user_id = $uid");
    header("Location: admin_dashboard.php");
    exit();
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $uid = intval($_POST['delete_user_id']);
    $conn->query("DELETE FROM users WHERE user_id = $uid");
    header("Location: admin_dashboard.php");
    exit();
}

$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_suspended BOOLEAN DEFAULT 0");

include $_SERVER['DOCUMENT_ROOT'] . "/../includes/header.php";


// // item pages logic
$items_per_page = 10;

// get current page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// get total # of items
$total_result = $conn->query("SELECT COUNT(*) as count FROM items");
$total_items = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_items / $items_per_page);

// get items for current page
$stmt = $conn->prepare("SELECT * FROM items LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $items_per_page, $offset);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { cursor: pointer; background: #eee; }
    </style>
    <script>
        function sortTable(n, tableID) {
            var table = document.getElementById(tableID), switching = true, dir = "asc", switchcount = 0;

            while (switching) {
                switching = false;
                var rows = table.rows;

                for (var i = 1; i < (rows.length - 1); i++) {
                    var x = rows[i].getElementsByTagName("TD")[n];
                    var y = rows[i + 1].getElementsByTagName("TD")[n];
                    var shouldSwitch = false;

                    if (dir === "asc" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    } else if (dir === "desc" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }

                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else if (switchcount === 0 && dir === "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }

        function confirmDelete(uid) {
            if (confirm("Are you sure you want to delete user ID " + uid + "?")) {
                document.getElementById('delete_user_id').value = uid;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</head>
<body>

<h1>Admin Dashboard</h1>

<h2>👥 User Management</h2>
<table id="userTable">
    <thead>
        <tr>
            <th onclick="sortTable(0, 'userTable')">User ID</th>
            <th onclick="sortTable(1, 'userTable')">Name</th>
            <th onclick="sortTable(2, 'userTable')">Email</th>
            <th onclick="sortTable(3, 'userTable')">Suspended</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $users = $conn->query("SELECT user_id, name, email, is_suspended FROM users ORDER BY user_id");
        while ($u = $users->fetch_assoc()):
        ?>
        <tr>
            <td><?= $u['user_id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['is_suspended'] ? 'Yes' : 'No' ?></td>
            <td>
                <a href="?toggle_suspend=<?= $u['user_id'] ?>">
                    <?= $u['is_suspended'] ? 'Unsuspend' : 'Suspend' ?>
                </a> |
                <a href="javascript:void(0);" onclick="confirmDelete(<?= $u['user_id'] ?>)">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" id="delete_user_id" name="delete_user_id">
</form>

<hr>

        <h2>Edit Items</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Current Value</th>
                    <th>Update Value</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td><?= htmlspecialchars($item['value']) ?></td>
                    <form method="POST" action="update_item_value.php">
                        <td>
                            <input type="number" name="new_value" value="<?= htmlspecialchars($item['value']) ?>" min="0" step="0.01" class="form-control">
                        </td>
                        <td>
                            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- switch item page links -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>



<hr>

<h2>📦 All Transactions</h2>
<table id="txTable">
    <thead>
        <tr>
            <th onclick="sortTable(0, 'txTable')">ID</th>
            <th onclick="sortTable(1, 'txTable')">Users (A-B-X-Y)</th>
            <th onclick="sortTable(2, 'txTable')">Items (P ⇄ E)</th>
            <th onclick="sortTable(3, 'txTable')">Hash</th>
            <th onclick="sortTable(4, 'txTable')">Status</th>
            <th onclick="sortTable(5, 'txTable')">Created</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $txs = $conn->query("SELECT * FROM transactions ORDER BY created_at DESC");
        while ($t = $txs->fetch_assoc()):
        ?>
        <tr>
            <td><?= $t['transaction_id'] ?></td>
            <td><?= "{$t['user_a_id']}-{$t['user_b_id']}-{$t['user_x_id']}-{$t['user_y_id']}" ?></td>
            <td><?= "{$t['item1_id']} ⇄ {$t['item2_id']}" ?></td>
            <td><?= $t['hash_code'] ?></td>
            <td>
                <?= $t['is_complete'] ? "✅ Complete" : ($t['e_sent'] ? "🔐 Awaiting Y" : "⏳ Awaiting B") ?>
            </td>
            <td><?= $t['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
