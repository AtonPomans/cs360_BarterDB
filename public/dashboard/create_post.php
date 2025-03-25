<?php

session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$poster_id = $_SESSION['user_id'];

// Get form data
$offered_name = $_POST['offered_name'];
$offered_quantity = $_POST['offered_quantity'];

$requested_name = $_POST['requested_name'];
$requested_quantity = $_POST['requested_quantity'];

$partner_id = !empty($_POST['partner_id']) ? $_POST['partner_id'] : null;
$is_split_allowed = isset($_POST['is_split_allowed']) ? 1 : 0;

// --- Helper function: Get existing item or insert a new one ---
function getOrCreateItem($conn, $name) {
    $stmt = $conn->prepare("SELECT item_id FROM items WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($existing_id);
    if ($stmt->fetch()) {
        $stmt->close();
        return $existing_id;
    }
    $stmt->close();

    // Insert new item
    $stmt = $conn->prepare("INSERT INTO items (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();

    return $new_id;
}

// Get or create items
$offered_item_id = getOrCreateItem($conn, $offered_name);
$requested_item_id = getOrCreateItem($conn, $requested_name);

// Insert barter post
$stmt = $conn->prepare("
    INSERT INTO barter_post (
        poster_id, partner_id, offered_item, requested_item,
        offered_quantity, requested_quantity, is_split_allowed
    ) VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iiiiiii",
    $poster_id,
    $partner_id,
    $offered_item_id,
    $requested_item_id,
    $offered_quantity,
    $requested_quantity,
    $is_split_allowed
);
$stmt->execute();

header("Location: dashboard.php?post=success");
exit();
?>

