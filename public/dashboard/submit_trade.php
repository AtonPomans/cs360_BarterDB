<?php
// submit_trade.php

session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/../config/database.php";

// 1. Auth check
if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit();
}
$user_id = (int)$_SESSION['user_id'];

// 2. Validate POST
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || empty($_POST['listing_id'])
    || !filter_var($_POST['listing_id'], FILTER_VALIDATE_INT)
) {
    header("Location: /dashboard/dashboard.php?error=invalid_request");
    exit();
}
$post1_id = (int)$_POST['listing_id'];

// 3. Load original listing
$stmt = $conn->prepare("
    SELECT poster_id,
           offered_item, requested_item,
           offered_quantity, requested_quantity
    FROM barter_post
    WHERE post_id = ? AND status = 'open'
");
$stmt->bind_param("i", $post1_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: /dashboard/dashboard.php?error=not_found");
    exit();
}
$orig = $res->fetch_assoc();

// 4. Prevent trading with yourself
if ((int)$orig['poster_id'] === $user_id) {
    header("Location: /dashboard/dashboard.php?error=cannot_trade_self");
    exit();
}

// 5. Start transaction
$conn->begin_transaction();
try {
    // 6a. Create the request‐post (post2)
    $stmt2 = $conn->prepare("
        INSERT INTO barter_post
          (poster_id, partner_id,
           offered_item, requested_item,
           offered_quantity, requested_quantity,
           status)
        VALUES (?, ?, ?, ?, ?, ?, 'open')
    ");
    $poster2         = $user_id;                 // you
    $partner2        = (int)$orig['poster_id'];  // listing owner
    $offer2_item     = (int)$orig['requested_item'];
    $request2_item   = (int)$orig['offered_item'];
    $offer2_qty      = (int)$orig['requested_quantity'];
    $request2_qty    = (int)$orig['offered_quantity'];

    $stmt2->bind_param(
        "iiiiii",
        $poster2,
        $partner2,
        $offer2_item,
        $request2_item,
        $offer2_qty,
        $request2_qty
    );
    $stmt2->execute();
    $post2_id = $conn->insert_id;

    // 6b. Generate transaction codes
    $bytes     = random_bytes(8);
    $hash_code = bin2hex($bytes);          // 16 hex chars
    $part_a    = substr($hash_code, 0, 8);
    $part_y    = substr($hash_code, 8, 8);

    // 6c. Insert into transactions
    $stmt3 = $conn->prepare("
        INSERT INTO transactions
          (post1_id, post2_id,
           user_a_id, user_b_id,
           user_x_id, user_y_id,
           item1_id, item2_id,
           hash_code, part_a_code, part_y_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    // Schema roles:
    //  • X = owner of item1 → original poster
    //  • Y = X’s partner    → you
    //  • A = requester of item1 → you
    //  • B = A’s partner         → original poster
    $user_a = $user_id;
    $user_b = (int)$orig['poster_id'];
    $user_x = (int)$orig['poster_id'];
    $user_y = $user_id;
    $item1  = (int)$orig['offered_item'];
    $item2  = (int)$orig['requested_item'];

    $stmt3->bind_param(
        "iiiiiiiisss",
        $post1_id,
        $post2_id,
        $user_a,
        $user_b,
        $user_x,
        $user_y,
        $item1,
        $item2,
        $hash_code,
        $part_a,
        $part_y
    );
    $stmt3->execute();

    $conn->commit();
    header("Location: /dashboard/dashboard.php?trade_started=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    // Log $e->getMessage() in real app
    echo "Could not start trade: " . htmlspecialchars($e->getMessage());
    exit();
}

