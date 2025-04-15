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
    $stmt = $conn->prepare("SELECT item_id FROM items WHERE LOWER(name) = ? LIMIT 1");
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
        offered_quantity, requested_quantity
    ) VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iiiiii",
    $poster_id,
    $partner_id,
    $offered_item_id,
    $requested_item_id,
    $offered_quantity,
    $requested_quantity
);
$stmt->execute();

// Post matching logic
$new_post_id = $conn->insert_id;

$match_query = "
    SELECT * FROM barter_post
    WHERE offered_item = ?
      AND requested_item = ?
      AND poster_id != ?
      AND status = 'open'
    LIMIT 1
";

$stmt = $conn->prepare($match_query);
$stmt->bind_param("iii", $requested_item_id, $offered_item_id, $poster_id);
$stmt->execute();
$match_result = $stmt->get_result();

// If match found
if ($match = $match_result->fetch_assoc()) {
    $match_post_id = $match['post_id'];
    $match_user_id = $match['poster_id'];
    
    $match_partner_id = $match['partner_id'];
    if (!$partner_id || !$match_partner_id) {
        // leave both posts open, don’t match
        header("Location: dashboard.php?post=success");
        exit();
    }

    // Close both posts
    $conn->query("UPDATE barter_post SET status = 'closed' WHERE post_id IN ($new_post_id, $match_post_id)");

    // Generate hash
    $hash = strtoupper(bin2hex(random_bytes(8)));
    $part_a = substr($hash, 0, 8);
    $part_y = substr($hash, 8, 8);

    // Insert into transactions table
    $stmt = $conn->prepare("
        INSERT INTO transactions (
            post1_id, post2_id,
            user_a_id, user_x_id,
            user_b_id, user_y_id,
            item1_id, item2_id,
            hash_code, part_a_code, part_y_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iiiiiiissss",
        $new_post_id,        //this post 
        $match_post_id,      // other post
        $poster_id,          // A
        $match_user_id,      // X
        $partner_id,         // B
        $match_partner_id,   // Y
        $offered_item_id,    // this post's item
        $requested_item_id,  // item receiving from other post
        $hash,
        $part_a,
        $part_y
    );
    $stmt->execute();

    header("Location: dashboard.php?match=1");
    exit();
}


// If no match found
header("Location: dashboard.php?post=success");
exit();

?>

