<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $entered_code = $_POST['part_a_code'];

    $stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND e_sent = 0");
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($entered_code === $row['part_a_code']) {
            $update = $conn->prepare("UPDATE transactions SET e_sent = 1 WHERE transaction_id = ?");
            $update->bind_param("i", $transaction_id);
            $update->execute();
            echo "✅ E received. Waiting on Y to complete.";
        } else {
            echo "❌ Invalid code.";
        }
    } else {
        echo "❌ Transaction not found or already processed.";
    }
}
?>
