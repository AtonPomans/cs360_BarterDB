<?php
session_start();
include '../config/database.php';

$result = $conn->query("SELECT * FROM transactions");

echo "<table><tr><th>Transaction ID</th><th>Items Exchanged</th><th>Status</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['transaction_id']}</td><td>{$row['item_sent']} ↔ {$row['item_received']}</td><td>{$row['status']}</td></tr>";
}
echo "</table>";
?>
