<?php
$link = $_POST['name'] ?? null; // Get link from POST data
$slug = $_POST['slug'] ?? null; // Get slug from POST data
if (!$link || !$slug) {
    jsonResponse(['error' => 'link and slug are required'], 400);
    exit;
}
$newSlug = strtolower(trim($link));
$newSlug = str_replace(' ', '-', $newSlug);
$newSlug = str_replace('/', '', $newSlug);
$newSlug = str_replace('?', '', $newSlug);
// Ensure the slug is unique
$stmt = $conn->prepare("UPDATE teksten SET name = ?, slug = ? WHERE slug = ?");
$stmt->bind_param("sss", $link, $newSlug, $slug);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Link updated successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to update link'], 500);
}