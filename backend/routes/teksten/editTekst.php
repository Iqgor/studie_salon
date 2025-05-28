<?php
$slug = $_POST['slug'] ?? null; // Get slug from POST data
$text = $_POST['editedContent'] ?? null; // Get text from POST data
$text = preg_replace('/style="[^"]*"/i', '', $text);
if (!$slug || !$text) {
    jsonResponse(['error' => 'slug and text are required'], 400);
    exit;
}
$stmt = $conn->prepare("UPDATE teksten SET tekst = ? WHERE slug = ?");
$stmt->bind_param("ss", $text, $slug);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Text updated successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to update text'], 500);
}
