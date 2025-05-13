<?php
header("Access-Control-Allow-Origin: *"); // Of specifieker: http://localhost:3000
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// PHPMailer importeren
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
$config = require_once __DIR__ . '/config.php';

$conn = getDBConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $url = $_SERVER['REQUEST_URI'];
        $urlParts = explode('?', $url, 2);
        $urlParts = explode('/', trim($urlParts[0], '/'));
        $resource = $urlParts[2] ?? null;
        switch ($resource) {
            case 'plans':
                $stmt = $conn->prepare("
        SELECT 
            plans.id AS plan_id,
            plans.name AS plan_name,
            plans.price,
            plans.description AS plan_description,
            plans.rank AS plan_rank,
            plans.icon AS plan_icon,
            plans.sale AS plan_sale,
            plans.sale_type AS plan_sale_type,
            features.id AS feature_id,
            features.name AS feature_name,
            features.description AS feature_description,
            features.icon AS feature_icon
        FROM plans
        LEFT JOIN features ON plans.id = features.plan_id
        ORDER BY plans.rank ASC
    ");
                $stmt->execute();
                $result = $stmt->get_result();

                $plans = [];
                while ($row = $result->fetch_assoc()) {
                    $planId = $row['plan_id'];

                    if (!isset($plans[$planId])) {
                        $plans[$planId] = [
                            'id' => $row['plan_id'],
                            'name' => $row['plan_name'],
                            'price' => $row['price'],
                            'description' => $row['plan_description'],
                            'rank' => $row['plan_rank'],
                            'icon' => $row['plan_icon'],
                            'sale' => $row['plan_sale'],
                            'sale_type' => $row['plan_sale_type'],
                            'features' => []
                        ];
                    }

                    if ($row['feature_id']) {
                        $plans[$planId]['features'][] = [
                            'id' => $row['feature_id'],
                            'name' => $row['feature_name'],
                            'description' => $row['feature_description'],
                            'icon' => $row['feature_icon']
                        ];
                    }
                }

                $formattedPlans = array_values($plans);

                jsonResponse(['plans' => $formattedPlans], 200);
                break;

            default:
                jsonResponse(['error' => 'Method not allowed'], 405);
                break;
        }


    case 'POST':

        $url = $_SERVER['REQUEST_URI'];
        $urlParts = explode('?', $url, 2);
        $urlParts = explode('/', trim($urlParts[0], '/'));
        $resource = $urlParts[2] ?? null;

        switch ($resource) {
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
                $startDateTime = $startDate .':00';
                $endDateTime = $endDate ? $endDate . ':00' : null;

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



            case 'login':
                // Assumes $conn is your mysqli connection
                // jsonResponse is a helper function to send JSON responses

                //^ JSON input uitlezen
                $data = json_decode(file_get_contents('php://input'), true);
                $email = $data['email'] ?? null;
                $password = $data['password'] ?? null;

                //^ Validatie van vereiste velden
                if (!$email || !$password) {
                    jsonResponse(['title' => 'Gegevens onderbreken','message' => 'Email en wachtwoord zijn nodig', 'type' => 'error'], 400);
                    exit;
                }

                //^ Gebruiker zoeken op email
                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();




                if ($result->num_rows === 0) {
                    jsonResponse(['title' => 'niet gevonden','message' => 'Geen gebruiker gevonden met: ' . $email, 'type' => 'error'], 401);

                    exit;
                }
                $temp_used = false; // kijken of temp wachtwoord is gebruikt
                $user = $result->fetch_assoc();
                //^ als de gebruiker zijn email niet geverifieerd is


                //^ Wachtwoord controleren
                // Controleer of er een temp_password is
                if (isset($user['temp_password']) && isset($user['temp_password_expires_at'])) {
                    // Controleer of temp_password geldig is en niet verlopen
                    if (
                        password_verify($password, $user['temp_password']) &&
                        strtotime($user['temp_password_expires_at']) > time()
                    ) {
                        $temp_used = true; // Temp wachtwoord is gebruikt
                        // Temp wachtwoord is geldig
                        $stmt = $conn->prepare("UPDATE users SET temp_password = NULL, temp_password_expires_at = NULL WHERE id = ?");
                        $stmt->bind_param("i", $user['id']);
                        $stmt->execute();

                    } elseif (password_verify($password, $user['password'])) {
                        // Normaal wachtwoord is geldig
                        $stmt = $conn->prepare("UPDATE users SET temp_password = NULL, temp_password_expires_at = NULL WHERE id = ?");
                        $stmt->bind_param("i", $user['id']);
                        $stmt->execute();
                    } else {
                        // Temp wachtwoord is ongeldig of verlopen
                        jsonResponse(['title' => 'tijdelijk wachtwoord','message' => 'tijdelijk wachtwoord klopt niet of is vergaan', 'type' => 'error'], 401);
                        exit;
                    }
                } else {
                    // Geen temp_password, dus controleer het normale wachtwoord
                    if (!password_verify($password, $user['password'])) {
                        jsonResponse(['title' => 'verkeerd wachtwoord','message' => 'wachtwoord is verkeerd', 'type' => 'error'], 401);
                        exit;
                    }
                }


                //^ Controleer of gebruiker een abonnement heeft
                $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND end_date > NOW()");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                $subscription = $result->fetch_assoc();
                // if ($subscription["status"] !== "active") {
                // jsonResponse(['title' => 'Geen actiev abonnement','message' => 'Neem een abonnement om in te loggen', 'type' => 'error'], 401);
                // exit;
                // }

                //^ Token genereren (JWT)
                $secret_key = "your_secret_key"; // use a secure one in real apps
                $issuedAt = time();
                $expirationTime = $issuedAt + 3600; // valid for 1 hour
                unset($user['password']); // Verwijder wachtwoord uit response
                unset($user['temp_password']); // Verwijder temp wachtwoord uit response
                unset($user['temp_password_expires_at']); // Verwijder temp wachtwoord uit response
                unset($user['otp_code']); // Verwijder OTP uit response
                unset($user['otp_expires_at']); // Verwijder OTP uit response


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


                //^ kijken of de user laatst 2 weken heeft ingelogd
                $lastLogin = $user['last_login']; // Haal deze waarde op uit je database
                $lastLoginTime = new DateTime($lastLogin);
                $now = new DateTime();
                $interval = $now->diff($lastLoginTime);


                if ($interval->days <= 29 && $now > $lastLoginTime) {
                    $user['active'] = 1; // Zet active op 1
                } else {
                    $user['active'] = 0; // Zet active op 0
                }



                //^ OTP als active 0 is
                if ($user['active'] === 0) {
                    $otp = random_int(100000, 999999);
                    $expiry = date('Y-m-d H:i:s', time() + 300); // 5 minuten geldig

                    $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $otp, $expiry, $user['id']);
                    $stmt->execute();


                    // Verstuur OTP naar e-mail
                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();

                        $mail->Host = $config['MAIL_HOST'];
                        $mail->SMTPAuth = true;
                        $mail->Username = $config['MAIL_USERNAME'];  
                        $mail->Password = $config['MAIL_PASSWORD'];  
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom($config['MAIL_USERNAME'], 'StudieSalon');
                        $mail->addAddress($user['email']);  // Ontvanger

                        $mail->isHTML(true);
                        $mail->Subject = 'Jouw logincode';
                        $mail->Body = "Je login code is: <b>$otp</b><br>Deze is 5 minuten geldig.";
                        $mail->AltBody = "Je login code is: $otp\nDeze is 5 minuten geldig.";

                        $mail->send();

                        // Geef aan frontend aan dat OTP vereist is
                        jsonResponse(['title' => 'OTP nodig','message' => 'Email verstuurd met een tijdelijke code', 'type' => 'message', 'otp_required' => true], 200);

                    } catch (Exception $e) {

                        jsonResponse(['title' => 'verzenden mislukt','message' => 'Probeer het later opnieuw', 'type' => 'error'], 500);
                    }
                }

                //^ Succesvolle login, user info teruggeven (zonder wachtwoord!)
                unset($user['password']); // Verwijder wachtwoord uit response
                unset($user['last_login']); // Verwijder last_login uit response
                unset($user['created_at']); // Verwijder created_at uit response

                jsonResponse([
                    'message' => 'Login successful',
                    'token' => $jwt,
                    'active' => $user['active'],
                    'temp_used' => $temp_used, // Add this line
                ], 200);

                break;
            case 'verify_otp':
                // Input ophalen
                $data = json_decode(file_get_contents('php://input'), true);
                $code = $data['otp'] ?? null;
                $email = $data['email'] ?? null;

                if (!$code || !$email) {
                    jsonResponse(['error' => 'Code en email zijn verplicht'], 400);
                    jsonResponse(['title' => 'Gegevens missen','message' => 'Geef de code in uw mail en uw email', 'type' => 'error'], 400);
                    exit;
                }

                // Haal gebruiker op met OTP-verificatie in dezelfde query
                $now = date('Y-m-d H:i:s');

                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND otp_code = ? AND otp_expires_at > ?");
                $stmt->bind_param("sss", $email, $code, $now);
                $stmt->execute();

                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                if (!$user) {
                    jsonResponse(['title' => 'Code verlopen','message' => 'vraag een nieuwe code aan', 'type' => 'error'], 401);
                    exit;
                }

                // OTP is geldig – wis de OTP uit de user record
                $stmt = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expires_at = NULL WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();

                // Update last_login naar huidige tijd
                $current_time = time();
                $stmt = $conn->prepare("UPDATE users SET last_login = FROM_UNIXTIME(?) WHERE id = ?");
                $stmt->bind_param("ii", $current_time, $user['id']);
                $stmt->execute();

                // Verwijder wachtwoord en andere gevoelige data uit de response
                unset($user['password']);
                unset($user['otp_code']);
                unset($user['otp_expires_at']);

                // JWT genereren
                $secret_key = "your_secret_key"; // Gebruik veilige env variabelen in productie
                $issuedAt = time();
                $expirationTime = $issuedAt + 3600;

                $payload = [
                    'iat' => $issuedAt,
                    'exp' => $expirationTime,
                    'user' => $user,
                ];

                $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
                $payload_json = json_encode($payload);

                $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
                $base64UrlPayload = rtrim(strtr(base64_encode($payload_json), '+/', '-_'), '=');

                $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret_key, true);
                $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

                $jwt = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";

                jsonResponse([
                    'message' => 'Login met OTP geslaagd',
                    'token' => $jwt,
                    'active' => 1,
                ], 200);
                break;

            case 'register':

                $data = json_decode(file_get_contents('php://input'), true);
                $name = $data['name'] ?? null;
                $email = $data['email'] ?? null;

                // Validate required fields
                if (!$name || !$email ) {
                    jsonResponse(['title' => 'niet gevonden','message' => 'Email en/of wachtwoord zijn verkeerd', 'type' => 'error'], 400);
                    exit;
                }

                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    jsonResponse(['title' => 'ongeldig e-mailformaat','message' => 'Wij supporten deze email niet', 'type' => 'error'], 400);
                    exit;
                }

                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    jsonResponse(['title' => 'Email bestaat al','message' => 'Gebruik een ander email', 'type' => 'error'], 409);
                    exit;
                }
                
                
                // Genereer tijdelijk wachtwoord
                $tempPasswordPlain = bin2hex(random_bytes(4)); // 8 tekens, veilig en random
                $hashedTempPassword = password_hash($tempPasswordPlain, PASSWORD_BCRYPT);
                $expiry = date('Y-m-d H:i:s', time() + 1800); // 30 minuten geldig
                // Sla op in de database
                $stmt = $conn->prepare("INSERT INTO users (name, email, temp_password, temp_password_expires_at) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $hashedTempPassword, $expiry);
                $stmt->execute();





                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = $config['MAIL_HOST'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $config['MAIL_USERNAME'];  
                    $mail->Password = $config['MAIL_PASSWORD']; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($config['MAIL_USERNAME'], 'StudieSalon');
                    $mail->addAddress($email);  // Ontvanger

                    $mail->isHTML(true);
                    $mail->Subject = 'Jouw tijdelijke wachtwoord';
                    $mail->Body = "De wachtwoord is: <b>$tempPasswordPlain</b><br>Deze is 30 minuten geldig.";
                    $mail->AltBody = "De wachtwoord is: $tempPasswordPlain\nDeze is 30 minuten geldig.";

                    $mail->send();

                    // Geef aan frontend aan dat tijdelijk wachtwoord is verzonden
                    jsonResponse(['title' => 'Email verzonden','message' => 'U heeft een tijdelijk wachtwoord gekregen', 'type' => 'message'], 200);

                } catch (Exception $e) {
                    jsonResponse(['title' => 'Verzenden mislukt','message' => 'Probeer het later opnieuw', 'type' => 'error'], 500);
                }
                break;
            case 'forgot_password':
                $data = json_decode(file_get_contents('php://input'), true);
                $email = $data['email'] ?? null;

                // Validate input
                if (!$email) {
                    jsonResponse(['title' => 'Email in nodig','message' => 'Geef uw email op', 'type' => 'error'], 400);
                    exit;
                }

                // Check of user bestaat
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                if (!$user) {
                    jsonResponse(['title' => 'Geen gebruiker gevonden','message' => 'Gebruiker bestaat niet', 'type' => 'error'], 404);
                    exit;
                }

                // Genereer tijdelijk wachtwoord
                $tempPasswordPlain = bin2hex(random_bytes(4)); // 8 tekens, veilig en random
                $hashedTempPassword = password_hash($tempPasswordPlain, PASSWORD_BCRYPT);
                $expiry = date('Y-m-d H:i:s', time() + 1800); // 30 minuten geldig

                // Sla op in de database
                $stmt = $conn->prepare("UPDATE users SET temp_password = ?, temp_password_expires_at = ? WHERE id = ?");
                $stmt->bind_param("ssi", $hashedTempPassword, $expiry, $user['id']);
                $stmt->execute();

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = $config['MAIL_HOST'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $config['MAIL_USERNAME'];  
                    $mail->Password = $config['MAIL_PASSWORD']; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($config['MAIL_USERNAME'], 'StudieSalon');
                    $mail->addAddress($email);  // Ontvanger

                    $mail->isHTML(true);
                    $mail->Subject = 'Jouw tijdelijke wachtwoord';
                    $mail->Body = "De wachtwoord is: <b>$tempPasswordPlain</b><br>Deze is 30 minuten geldig.";
                    $mail->AltBody = "De wachtwoord is: $tempPasswordPlain\nDeze is 30 minuten geldig.";

                    $mail->send();

                    // Geef aan frontend aan dat tijdelijk wachtwoord is verzonden
                    jsonResponse(['title' => 'Email verzonden','message' => 'U heeft een tijdelijk wachtwoord gekregen', 'type' => 'success'], 200);

                } catch (Exception $e) {
                    jsonResponse(['title' => 'Verzenden mislukt','message' => 'Probeer het later opnieuw', 'type' => 'error'], 500);
                }
                break;
            case 'change_password':
                $data = json_decode(file_get_contents('php://input'), true);
                $userId = $data['userId'] ?? null;
                $newPassword = $data['newPassword'] ?? null;

                // Validate input
                if (!$userId || !$newPassword) {
                    jsonResponse(['title' => 'Gegevens missen','message' => 'Geef uw wachtwoord op', 'type' => 'error'], 400);
                    exit;
                }

                // Hash new password
                $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                // Update password in the database
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedNewPassword, $userId);

                if ($stmt->execute()) {
                    $stmt = $conn->prepare("UPDATE users SET temp_password = NULL, temp_password_expires_at = NULL WHERE id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    jsonResponse(['title' => 'Wachtwoord veranderd','message' => 'Uw wachtwoord is veranderd', 'type' => 'success'], 200);
                } else {
                    jsonResponse(['title' => 'Veranderen gevaald','message' => 'wachtwoord veranderen gevaald', 'type' => 'error'], 500);
                }
                break;




            case 'create-payment':
                jsonResponse(['error' => 'Method not allowed'], 405);
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
            case 'getTekstLinks':
                $tegel = $_POST['slug'] ?? null; 
                $stmt = $conn->prepare("SELECT slug, name FROM teksten WHERE tegel = ?");
                $stmt->bind_param("s", $tegel);
                $stmt->execute();
                $result = $stmt->get_result();
                $links = [];
                while ($row = $result->fetch_assoc()) {
                    $links[] = [
                        'slug' => $row['slug'],
                        'name' => $row['name']
                    ];
                }
                jsonResponse($links, 200);
                break;
            case 'getTekst':
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
                break;
            case null:
                jsonResponse(['error' => 'Resource not found'], 404);

                break;
            default:
                jsonResponse(['error' => 'Resource not found'], 404);
                break;
        }
        break;
    case 'PUT':
        $url = $_SERVER['REQUEST_URI'];
        $urlParts = explode('?', $url, 2);
        $urlParts = explode('/', trim($urlParts[0], '/'));
        $resource = $urlParts[2] ?? null;
        switch ($resource) {
            
        }


    // Add cases for PUT, DELETE, etc.

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
        break;
}
?>