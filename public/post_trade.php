<?php
session_start();
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $item_name = $_POST["item_name"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("INSERT INTO items (user_id, name, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $item_name, $description);
    $stmt->execute();
    header("Location: dashboard.php");
}
?>
