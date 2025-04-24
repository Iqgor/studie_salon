<?php
require_once __DIR__ . '/config.php';

$conn = getDBConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
    // Handle GET request (read)


    case 'POST':

        $url = $_SERVER['REQUEST_URI'];
        $urlParts = explode('/', trim($url, '/'));
        $resource = $urlParts[2] ?? null;
        switch ($resource) {
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




            case 'login':
            // Assumes $conn is your mysqli connection
            // jsonResponse is a helper function to send JSON responses
            
            //^ JSON input uitlezen
            $data = json_decode(file_get_contents('php://input'), true);
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
            
            //^ Validatie van vereiste velden
            if (!$email || !$password) {
                jsonResponse(['error' => 'Email and password are required'], 400);
                exit;
            }
            
            //^ Gebruiker zoeken op email
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                jsonResponse(['error' => 'Invalid email or password'], 401);
                exit;
            }
            
            $user = $result->fetch_assoc();
            //^ Wachtwoord controleren
            if (!password_verify($password, $user['password'])) {
                jsonResponse(['error' => 'Invalid email or password'], 401);
                exit;
            }

            //^ controleren of gebruiker actief is
            if ($user['active'] !== 1) {
                jsonResponse(['error' => 'User is not active'], 403);
                exit;
            }

            //^ Controleer of gebruiker een abonnement heeft
            $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND end_date > NOW()");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            //jsonResponse(['subscriptions' => $result->fetch_all(MYSQLI_ASSOC)], 200);

            $subscription = $result->fetch_assoc();
            if ($subscription["status"] !== "active") {
                jsonResponse(['error' => 'Subscription is not active'], 403);
                exit;
            }
            
            //^ Token genereren (JWT)
            $secret_key = "your_secret_key"; // use a secure one in real apps
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // valid for 1 hour
            unset($user['password']); // Verwijder wachtwoord uit response


            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'user' => $user,
            ];
            
            //^ Create JWT manually
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
            $payload_json = json_encode($payload);
            
            $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
            $base64UrlPayload = rtrim(strtr(base64_encode($payload_json), '+/', '-_'), '=');
            
            $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret_key, true);
            $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
            
            $jwt = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
            
            //^ Succesvolle login, user info teruggeven (zonder wachtwoord!)
            unset($user['password']); // Verwijder wachtwoord uit response
            
            jsonResponse([
                'message' => 'Login successful',
                'token' => $jwt,
            ], 200);
        }

        break;

    // Add cases for PUT, DELETE, etc.

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
        break;
}
?>