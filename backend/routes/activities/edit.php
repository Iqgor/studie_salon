<?php
$activityData = $_POST['shownActivity'] ?? null; // Get activityId from POST dataMore actions
if (!$activityData) {
    jsonResponse(['error' => 'Activity data is required'], 400);
    exit;
}
// Decode JSON string to array
if (is_string($activityData)) {
    $activityData = json_decode($activityData, true);
}

$activityId = $activityData['id'] ?? null; // Get activityId from POST data
$title = $activityData['title'] ?? null; // Get title from POST data
$vakName = $activityData['vak'] ?? null; // Get vakName from POST data
$maakWerk = $activityData['maakwerk'] ?? null; // Get vakName from POST data
$startDate = $activityData['start_datetime'] ?? null; // Get startDate from POST data
$endDate = $activityData['end_datetime'] ?? null; // Get endDate from POST data
$done = $activityData['done'] ?? null; // Get done from POST data
$stmt = $conn->prepare("UPDATE activities SET title = ?, vak = ?, maakwerk = ?, done = ?, start_datetime = ?, end_datetime = ? WHERE id  = ?");
$startDateTime = $startDate . ':00';
$endDateTime = $endDate ? $endDate . ':00' : null;
$stmt->bind_param("sssissi", $title, $vakName, $maakWerk, $done, $startDateTime, $endDateTime, $activityId);
if ($stmt->execute()) {
    jsonResponse(['message' => 'Activity updated successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to update activity'], 500);
}
