<?php
$id = $_POST['id'] ?? null; // Get id from POST data
$title = $_POST['title'] ?? null; // Get name from POST data

if (!$id || !$title) {
    $carouselName = $_POST['carouselName'] ?? null; // Get name from POST data
    $newCarouselName = $_POST['newCarouselName'] ?? null; // Get name from POST data
    if (!$carouselName || !$newCarouselName) {
        jsonResponse(['error' => 'id and title or carouselName and newCarouselName are required'], 400);
        exit;
    } else {
        $stmt = $conn->prepare("UPDATE coursel_items SET coursel_name = ? WHERE coursel_name = ?");
        $stmt->bind_param("ss", $newCarouselName, $carouselName);
        if ($stmt->execute()) {
            jsonResponse(['success' => 'Link updated successfully'], 200);
        } else {
            jsonResponse(['error' => 'Failed to update link'], 500);
        }
    }
}
$stmt = $conn->prepare("UPDATE coursel_items SET title = ? WHERE id = ?");
$stmt->bind_param("si", $title, $id);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Link updated successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to update link'], 500);
}
