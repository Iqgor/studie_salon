<?php
$slug = $_POST['slug'] ?? null; // Get slug from POST data
$stmt = $conn->prepare("SELECT tekst FROM teksten WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$tekst = $result->fetch_assoc();
if ($tekst) {
    jsonResponse(['tekst' => $tekst['tekst']], 200);
} else {
    jsonResponse(['error' => 'No text found'], 404);
}