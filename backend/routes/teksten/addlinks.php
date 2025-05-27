<?php
$tegel = $_POST['slug'] ?? null; // Get slug from POST data
$link = $_POST['link'] ?? null; // Get links from POST data
if (!$tegel || !$link) {
    jsonResponse(['error' => 'slug and links are required'], 400);
    exit;
}
$slug = strtolower(trim($link));
$slug = str_replace(' ', '-', $slug);
if ($link !== '') {
    $link = "➔ " . $link;
    $stmt = $conn->prepare("INSERT INTO teksten (tegel,name,slug ) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $tegel, $link, $slug);
    $stmt->execute();
}
jsonResponse(['success' => 'Links added successfully'], 200);