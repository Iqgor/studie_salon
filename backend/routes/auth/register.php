<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? null;
$email = $data['email'] ?? null;

// Validate required fields
if (!$name || !$email) {
    jsonResponse(['title' => 'niet gevonden', 'message' => 'Email en/of wachtwoord zijn verkeerd', 'type' => 'error'], 400);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['title' => 'ongeldig e-mailformaat', 'message' => 'Wij supporten deze email niet', 'type' => 'error'], 400);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    jsonResponse(['title' => 'Email bestaat al', 'message' => 'Gebruik een ander email', 'type' => 'error'], 409);
    exit;
}


// Genereer tijdelijk wachtwoord
$tempPasswordPlain = bin2hex(random_bytes(4)); // 8 tekens, veilig en random
$hashedTempPassword = password_hash($tempPasswordPlain, PASSWORD_BCRYPT);
$expiry = date('Y-m-d H:i:s', time() + 1800); // 30 minuten geldig

// Sla op in de database
$currentTime = date('Y-m-d H:i:s'); // huidige timestamp

// Sla op in de database
$stmt = $conn->prepare("INSERT INTO users (name, email, temp_password, temp_password_expires_at, created_at, updated_at, last_login) VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssss", $name, $email, $hashedTempPassword, $expiry, $currentTime, $currentTime, $currentTime);

$stmt->execute();


$subject = 'Jouw logincode';
$htmlBody = "het wachtwoord is: <b>$tempPasswordPlain</b><br>Deze is 30 minuten geldig.";
$altBody = "het wachtwoord is: $tempPasswordPlain\nDeze is 30 minuten geldig.";

if (sendMail($email, $subject, $htmlBody, $altBody)) {
    jsonResponse(['title' => 
    'Email verzonden',
    'message' => 'U heeft een tijdelijk wachtwoord gekregen', 
    'type' => 'success'],
    200);

} else {
    jsonResponse(['title' => 
    'Verzenden mislukt',
    'message' => 'Probeer het later opnieuw', 
    'type' => 'error'],
    500);

}