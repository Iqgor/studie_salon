<?php 
$carouselName = $_POST['carouselName'] ?? null;
$itemName = $_POST['itemName'] ?? null;

$stmt = $conn->prepare("SELECT MAX(id) as last_id FROM coursel_items WHERE coursel_name = ?");
$stmt->bind_param("s", $carouselName);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$lastId = $row['last_id'] ?? 0;
$newId = $lastId + 1;

if (!$carouselName || !$itemName) {
    jsonResponse(['error' => 'Carousel name and item name are required'], 400);
    exit;
}

$url = preg_replace('/[^\p{L}\p{N}\-]+/u', '', str_replace([' ', '/'], ['-', ''], $itemName));

$stmt = $conn->prepare("INSERT INTO coursel_items (id,coursel_name, title, url) VALUES (?,?, ?, ?)");
$stmt->bind_param("isss", $newId ,$carouselName, $itemName, $url);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Item added successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to add item'], 500);
}