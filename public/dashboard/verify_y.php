<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $entered_code = $_POST['part_y_code'];

    $stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND e_sent = 1 AND is_complete = 0");
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $reconstructed = $row['part_a_code'] . $entered_code;
        if ($reconstructed === $row['hash_code']) {
            $update = $conn->prepare("UPDATE transactions SET p_sent = 1, is_complete = 1 WHERE transaction_id = ?");
            $update->bind_param("i", $transaction_id);
            $update->execute();
            echo "✅ Transaction complete! Items distributed.";
        } else {
            echo "❌ Invalid code.";
        }
    } else {
        echo "❌ Transaction not found or already completed.";
    }

    header("Location: /user/cart.php");
    exit();
}
?>
