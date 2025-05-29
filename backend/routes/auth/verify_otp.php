<?php


$data = json_decode(file_get_contents('php://input'), true);
$code = $data['otp'] ?? null;
$email = $data['email'] ?? null;

if (!$code || !$email) {
    jsonResponse(['error' => 'Code en email zijn verplicht'], 400);
    jsonResponse(['title' => 'Gegevens missen', 'message' => 'Geef de code in uw mail en uw email', 'type' => 'error'], 400);
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
    jsonResponse(['title' => 'Code verlopen', 'message' => 'vraag een nieuwe code aan', 'type' => 'error'], 401);
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
$issuedAt = time();
$expirationTime = $issuedAt + 10800; // 3 uur geldig

$payload = [
    'iat' => $issuedAt,
    'exp' => $expirationTime,
    'user' => $user,
];

$jwt = generate_jwt($payload);

jsonResponse([
    'message' => 'Login met OTP geslaagd',
    'token' => $jwt,
    'active' => 1,
], 200);