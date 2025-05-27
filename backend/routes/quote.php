<?php
$language = $_POST['language'] ?? null; // Get language from query parameters
$stmt = $conn->prepare("SELECT * FROM quotes WHERE language = ? ORDER BY RAND() LIMIT 1");
$stmt->bind_param("s", $language);
$stmt->execute();
$result = $stmt->get_result();
$quote = $result->fetch_assoc();
if ($quote) {
    jsonResponse(['quote' => $quote], 200);
} else {
    jsonResponse(['error' => 'No quotes found'], 404);
}