<?php
include 'database.php'; // ensure this connects to your DB
session_start();

$current_user_id = $_SESSION['user_id']; // Assuming user is logged in

// Find all open posts by this user
$sql = "SELECT * FROM barter_post WHERE poster_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user_posts = $stmt->get_result();

while ($post = $user_posts->fetch_assoc()) {
    $offered = $post['offered_item'];
    $requested = $post['requested_item'];
    $post_id = $post['post_id'];
    $partner_id = $post['partner_id'];

    // Find a match from another user, reversed offer/request, and open status
    $match_sql = "
        SELECT * FROM barter_post
        WHERE 
            offered_item = ? AND
            requested_item = ? AND
            poster_id != ? AND
            status = 'open'
        LIMIT 1
    ";
    $match_stmt = $conn->prepare($match_sql);
    $match_stmt->bind_param("iii", $requested, $offered, $current_user_id);
    $match_stmt->execute();
    $match_result = $match_stmt->get_result();

    if ($match_result->num_rows > 0) {
        $match = $match_result->fetch_assoc();

        // Generate secure 16-digit hash key
        $full_hash = strtoupper(substr(hash('sha256', uniqid()), 0, 16));
        $part_a = substr($full_hash, 0, 8);
        $part_y = substr($full_hash, 8, 8);

        // Prepare data
        $post_a = $post;
        $post_x = $match;

        $user_a = $post_a['poster_id'];
        $user_x = $post_x['poster_id'];
        $user_b = $post_a['partner_id'];
        $user_y = $post_x['partner_id'];

        // Fallback for missing partners
        if (!$user_b || !$user_y) continue;

        // Insert into transactions
        $insert_sql = "
            INSERT INTO transactions (
                post1_id, post2_id,
                user_x_id, user_a_id, user_b_id, user_y_id,
                item1_id, item2_id,
                hash_code, part_a_code, part_y_code,
                created_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iii iii iis ss",
            $post_x['post_id'], $post_a['post_id'],
            $user_x, $user_a, $user_b, $user_y,
            $post_x['offered_item'], $post_a['offered_item'],
            $full_hash, $part_a, $part_y
        );

        if ($stmt->execute()) {
            // Update both posts to 'closed'
            $conn->query("UPDATE barter_post SET status = 'closed' WHERE post_id IN ($post_id, {$match['post_id']})");
            echo "Match found and transaction created for posts $post_id and {$match['post_id']}<br>";
        } else {
            echo "Transaction insert failed: " . $stmt->error . "<br>";
        }
    }
}

$conn->close();
?>
