<?php
$tegel = $_POST['slug'] ?? null; // Get slug from POST data

// Find carousel_name where url = $tegel
$stmt = $conn->prepare("SELECT coursel_name FROM coursel_items WHERE url = ?");
$stmt->bind_param("s", $tegel);
$stmt->execute();
$stmt->bind_result($carousel_name);
$stmt->fetch();
$stmt->close();

if ($carousel_name) {
    // Format new URL: carousel_name to lowercase, spaces to -, append /$tegel
    $new_url = strtolower(str_replace(' ', '-', $carousel_name)) . '/' . $tegel;

    // Update the item's url
    $update = $conn->prepare("UPDATE coursel_items SET url = ? WHERE url = ?");
    $update->bind_param("ss", $new_url, $tegel);
    $update->execute();
    $update->close();
    jsonResponse(['success' => 'URL updated successfully', 'new_url' => $new_url], 200);
}

