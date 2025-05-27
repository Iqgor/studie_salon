<?php
$slug = $_POST['slug'] ?? null; // Get slug from POST data
$tegel_naam = $_POST['tegel_naam'] ?? null; // Get tegelNaam from POST data
if(!$slug || !$tegel_naam) {
    jsonResponse(['error' => 'slug and tegel_naam are required'], 400);
    exit;
}
$stmt = $conn->prepare("UPDATE teksten SET tegel_naam = ? WHERE tegel = ?");
$stmt->bind_param("ss", $tegel_naam, $slug);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Link updated successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to update link'], 500);
}
