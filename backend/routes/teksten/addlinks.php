<?php
$tegel = $_POST['slug'] ?? null; // Get slug from POST data
$link = $_POST['link'] ?? null; // Get links from POST data
if (!$tegel || !$link) {
    jsonResponse(['error' => 'slug and links are required'], 400);
    exit;
}
$slug = strtolower(trim($link));
$slug = str_replace(' ', '-', $slug);
$slug = str_replace('/', '', $slug);
$slug = str_replace('?', '', $slug);


$tegelNaam = str_replace('-', ' ', $tegel);
if ($link !== '') {
    $link = "➔ " . $link;
    $stmt = $conn->prepare("INSERT INTO teksten (tegel,tegel_naam,name,slug ) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $tegel, $tegelNaam, $link, $slug);
    $stmt->execute();
}
jsonResponse(['success' => 'Links added successfully'], 200);
