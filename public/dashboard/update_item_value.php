<?php
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = $_POST["item_id"];
    $new_value = $_POST["new_value"];

    if (is_numeric($new_value)) {
        $stmt = $conn->prepare("UPDATE items SET value = ? WHERE item_id = ?");
        $stmt->bind_param("di", $new_value, $item_id);
        $stmt->execute();
    }
}

header("Location: admin_dashboard.php");
exit();
