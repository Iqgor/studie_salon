<?php
$slug = $_POST['slug'] ?? null; // Get slug from POST data
$text = $_POST['tekst'] ?? null; // Get text from POST data
// Remove inline styling from the text
$text = preg_replace('/style="[^"]*"/i', '', $text);
if (!$slug || !$text) {
    jsonResponse(['error' => 'slug and text are required'], 400);
    exit;
}
$stmt = $conn->prepare("UPDATE teksten SET tekst = ? WHERE slug = ?");
$stmt->bind_param("ss", $text, $slug);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Text added successfully'], 201);
} else {
    jsonResponse(['error' => 'Failed to add text'], 500);
}