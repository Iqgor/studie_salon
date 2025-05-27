<?php
$link = $_POST['name'] ?? null; // Get link from POST data
$slug = $_POST['slug'] ?? null; // Get slug from POST data
if (!$link || !$slug) {
    jsonResponse(['error' => 'link and slug are required'], 400);
    exit;
}
$stmt = $conn->prepare("UPDATE teksten SET name = ? WHERE slug = ?");
$stmt->bind_param("ss", $link, $slug);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Link updated successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to update link'], 500);
}