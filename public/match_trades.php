<?php
session_start();
include '../config/database.php';

function generate_hash() {
    return bin2hex(random_bytes(8));
}

$pending_trades = $conn->query("SELECT * FROM trade_requests WHERE status='pending'");

while ($trade = $pending_trades->fetch_assoc()) {
    $match = $conn->query("SELECT * FROM trade_requests WHERE requested_item = '{$trade['item_id']}' AND status='pending' LIMIT 1");

    if ($match->num_rows > 0) {
        $match_row = $match->fetch_assoc();
        $hash_key = generate_hash();

        $conn->query("UPDATE trade_requests SET status='matched' WHERE request_id IN ({$trade['request_id']}, {$match_row['request_id']})");
        $conn->query("INSERT INTO transactions (request_id, hash_key, sender_id, receiver_id, item_sent, item_received, status) VALUES ({$trade['request_id']}, '$hash_key', {$trade['user_id']}, {$match_row['user_id']}, {$trade['item_id']}, {$match_row['item_id']}, 'pending')");
    }
}
?>
