<?php
header("Access-Control-Allow-Origin: *"); // Of specifieker: http://localhost:3000
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
require_once __DIR__ . '/config.php';

$conn = getDBConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Handle GET request (read)

        
    case 'POST':
    
        $url = $_SERVER['REQUEST_URI'];
        $urlParts = explode('/', trim($url, '/'));
        // dit veranderen naar 1 voor server en 2 voor local
        $resource = $urlParts[2] ?? null;
         switch($resource){
            case 'create_activity':
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
                $startDateTime = $startDate . ' 00:00:00';
                $endDateTime = $endDate ? $endDate . ' 23:59:59' : null;
                $stmt->bind_param("isssss", $userId, $vakName,$maakWerk, $title, $startDateTime, $endDateTime);

                if ($stmt->execute()) {
                    jsonResponse(['message' => 'Activity created successfully', 'activity_id' => $stmt->insert_id], 201);
                } else {
                    jsonResponse(['error' => 'Failed to create activity'], 500);
                }
                $stmt->close();
                break;
            case 'users':
                $queryParams = $_GET; // Get all query parameters from the URL
                $stmt = $conn->prepare("SELECT * FROM users");
                $stmt->execute();
                $result = $stmt->get_result();
        
                $users = [];
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row;
                }
        
                jsonResponse(['users' => $users], 200);
                break;
            case 'activities':
                $userId = $_POST['userId'] ?? null; // Get userId from POST data
                $startDate = $_POST['startDate'] ?? null; // Get startDate from POST data
                $endDate = $_POST['endDate'] ?? null; // Get endDate from POST data
                // Validate required fields
                if (!$userId) {
                    jsonResponse(['error' => 'userId header is required'], 400);
                    exit;
                }

                // Prepare base query
                $query = "SELECT * FROM activities WHERE user_id = ?";
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
                break;
            case 'quote':
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
                break;
            case null:
                jsonResponse(['error' => 'Resource not found'], 404);
                break;
        }

        break;
        
    // Add cases for PUT, DELETE, etc.
        
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
        break;
}
?>