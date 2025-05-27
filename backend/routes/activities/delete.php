<?php
$activityId = $_POST['activity_id'] ?? null; // Get activityId from POST data
if (!$activityId) {
    jsonResponse(['error' => 'activity_id is required'], 400);
    exit;
}
$stmt = $conn->prepare("DELETE FROM activities WHERE activity_id = ?");
$stmt->bind_param("i", $activityId);
if ($stmt->execute()) {
    jsonResponse(['message' => 'Activity deleted successfully'], 200);
} else {
    jsonResponse(['error' => 'Failed to delete activity'], 500);
}