<?php
$userId = $_POST['userId']; // Get userId from POST data
$likes = $_POST['likes'] ?? null; // Get link from POST data
$tegel = $_POST['slug'] ?? null; // Get slug from POST data
$likes = json_decode($likes, true);
if ($userId && $tegel) {
    // Delete all existing likes for this user and tegel
    $stmt = $conn->prepare("DELETE FROM user_likes WHERE user_id = ? AND tegel = ?");
    $stmt->bind_param("is", $userId, $tegel);
    $stmt->execute();
}

$success = true;
if (is_array($likes)) {
    foreach ($likes as $like) {
        $link = $like['slug'] ?? null;
        if ($userId && $tegel && $link) {
            $stmt = $conn->prepare("INSERT INTO user_likes (user_id, tegel, link) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $tegel, $link);
            if (!$stmt->execute()) {
                $success = false;
            }
        }
    }
}

if ($success) {
    jsonResponse(['success' => 'Likes updated successfully'], 201);
} else {
    jsonResponse(['error' => 'Failed to update likes'], 500);
}

