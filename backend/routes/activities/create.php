<?php
$userId = $_POST['userId'] ?? null; // Get userId from POST data
$title = $_POST['title'] ?? null; // Get title from POST data
$vakName = $_POST['vakName'] ?? null; // Get vakName from POST data
$maakWerk = $_POST['maakwerk'] ?? null; // Get vakName from POST data
$startDate = $_POST['startDate'] ?? null; // Get startDate from POST data
$endDate = $_POST['endDate'] ?? null; // Get endDate from POST data

// Validate required fields
if (!$userId || !$startDate || !$title || !$vakName) {
    jsonResponse(['error' => 'userId, startDate, and activityType are required'], 400);
    exit;
}

// Insert activity into the database
$stmt = $conn->prepare("INSERT INTO activities (user_id, vak,maakwerk, title,start_datetime, end_datetime) VALUES (?, ?, ?, ?, ?, ?)");
$startDateTime = $startDate . ':00';
$endDateTime = $endDate ? $endDate . ':00' : null;

$stmt->bind_param("isssss", $userId, $vakName, $maakWerk, $title, $startDateTime, $endDateTime);


if ($stmt->execute()) {
    jsonResponse(['message' => 'Activity created successfully', 'activity_id' => $stmt->insert_id], 201);
} else {
    jsonResponse(['error' => 'Failed to create activity'], 500);
}
$stmt->close();