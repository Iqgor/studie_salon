<?php
$slug = $_POST['slug'] ?? null; // Get slug from POST data
$carouselName = $_POST['carouselName'] ?? null; // Get carousel name from POST data
$text = $_POST['tekst'] ?? null; // Get text from POST data
// jsonResponse($text, 200);    
// Remove inline styling from the text
$text = preg_replace('/style="[^"]*"/i', '', $text);
if (!$slug || !$text) {
    jsonResponse(['error' => 'slug and text are required'], 400);
    exit;
}
// Check if the tekst exists for the given slug
$checkStmt = $conn->prepare("SELECT COUNT(*) FROM teksten WHERE slug = ?");
$checkStmt->bind_param("s", $slug);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count > 0) {
    // Update existing tekst
    $stmt = $conn->prepare("UPDATE teksten SET tekst = ? WHERE slug = ?");
    $stmt->bind_param("ss", $text, $slug);
} else {
    // Insert new tekst
    $name = str_replace('-', '', $slug);
    $stmt = $conn->prepare("INSERT INTO teksten (tegel, tekst, name, slug) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $carouselName, $text, $name, $slug);
}
if ($stmt->execute()) {
    jsonResponse(['success' => 'Text added successfully'], 201);
} else {
    jsonResponse(['error' => 'Failed to add text'], 500);
}