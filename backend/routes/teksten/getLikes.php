<?php
$userId = $currentUser['id'] ?? null;
$slug = $_POST['slug'] ?? null;
$keys = $_POST['keys'] ?? null;
if($keys){
    $likes = [];
    foreach (json_decode($keys, true) as $itemSlug) {    
        $stmt = $conn->prepare('SELECT link FROM user_likes WHERE user_id = ? AND tegel = ? AND weergeven = 1');
        $stmt->bind_param('is', $userId, $itemSlug);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $likes[$itemSlug][] = ['slug' => $row['link']];
        }
    }
    
    jsonResponse(['likes' => $likes], 200);
    exit;
}
$stmt = $conn->prepare('SELECT link FROM user_likes where user_id = ? AND tegel = ? and weergeven = 1');
$stmt->bind_param('is', $userId, $slug);
$stmt->execute();
$result = $stmt->get_result();
$likes = [];
while ($row = $result->fetch_assoc()) {
    $likes[] = $row['link'];
}
jsonResponse(['likes' => $likes], 200);
