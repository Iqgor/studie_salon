<?php 
$carouselName = $_POST['carouselName'] ?? null;
$itemName = $_POST['itemName'] ?? null;


if (!$carouselName || !$itemName) {
    jsonResponse(['error' => 'Carousel name and item name are required'], 400);
    exit;
}

$url = preg_replace('/[^\p{L}\p{N}\-]+/u', '', str_replace([' ', '/'], ['-', ''], $itemName));


$stmt = $conn->prepare("INSERT INTO coursel_items (coursel_name,title, url) VALUES (?, ?, ?)");


$stmt->bind_param("sss",$carouselName, $itemName, $url);
if ($stmt->execute()) {
    jsonResponse(['success' => 'Item added successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to add item'], 500);
}
?>