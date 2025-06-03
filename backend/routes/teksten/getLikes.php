<?php
$userId = $currentUser['id'] ?? null;
$slug = $_POST['slug'];
$stmt = $conn->prepare('SELECT link FROM user_likes where user_id = ? AND tegel = ? and weergeven = 1');
$stmt->bind_param('is', $userId, $slug);
$stmt->execute();
$result = $stmt->get_result();
$likes = [];
while ($row = $result->fetch_assoc()) {
    $likes[] = $row['link'];
}
jsonResponse(['likes' => $likes], 200);
