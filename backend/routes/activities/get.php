<?php
$userId = $currentUser['id'] ?? null; // Get userId from POST data
$startDate = $_POST['startDate'] ?? null; // Get startDate from POST data
$endDate = $_POST['endDate'] ?? null; // Get endDate from POST data
// Validate required fields
if (!$userId) {
    jsonResponse(['error' => 'userId header is required'], 400);
    exit;
}

// Prepare base query
$query = "SELECT * FROM activities WHERE user_id = ? and weergeven = 1";
$params = [$userId];
$types = "i";

// Add date range filtering if dates are provided
if ($startDate && $endDate) {
    // Validate date format
    if (!DateTime::createFromFormat('Y-n-j', $startDate) || !DateTime::createFromFormat('Y-n-j', $endDate)) {
        jsonResponse(['error' => 'Invalid date format. Use YYYY-MM-DD'], 400);
        exit;
    }

    $query .= " AND start_datetime >= ? AND (end_datetime <= ? OR end_datetime IS NULL)";
    $params[] = $startDate . ' 00:00:00';
    $params[] = $endDate . ' 23:59:59';
    $types .= "ss";
}

// Prepare and execute statement
$stmt = $conn->prepare($query);

// Dynamic binding based on parameters
$bindParams = [$types];
foreach ($params as &$param) {
    $bindParams[] = &$param;
}
call_user_func_array([$stmt, 'bind_param'], $bindParams);

$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

jsonResponse(['activities' => $activities], 200);