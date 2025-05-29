<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//^ JSON input uitlezen
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

//^ Validatie van vereiste velden
if (!$email || !$password) {
    jsonResponse(['title' => 'Gegevens onderbreken', 'message' => 'Email en wachtwoord zijn nodig', 'type' => 'error'], 400);
    exit;
}

//^ Gebruiker zoeken op email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 0) {
    jsonResponse(['title' => 'niet gevonden', 'message' => 'Geen gebruiker gevonden met email: ' . $email, 'type' => 'error'], 401);
    exit;
}
$temp_used = false; // kijken of temp wachtwoord is gebruikt
$user = $result->fetch_assoc();


//^ Wachtwoord controleren
// Controleer of er een temp_password is
if (isset($user['temp_password']) && isset($user['temp_password_expires_at'])) {
    // Controleer of temp_password geldig is en niet verlopen
    if (
        password_verify($password, $user['temp_password']) &&
        strtotime($user['temp_password_expires_at']) > time()
    ) {
        $temp_used = true; // Temp wachtwoord is gebruikt

    } elseif (password_verify($password, $user['password'])) {
        // Normaal wachtwoord is geldig
        $stmt = $conn->prepare("UPDATE users SET temp_password = NULL, temp_password_expires_at = NULL WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
    } else {
        // Temp wachtwoord is ongeldig of verlopen
        jsonResponse(['title' => 'tijdelijk wachtwoord', 'message' => 'tijdelijk wachtwoord klopt niet of is vergaan', 'type' => 'error'], 401);
        exit;
    }
} else {
    // Geen temp_password, dus controleer het normale wachtwoord
    if (!password_verify($password, $user['password'])) {
        jsonResponse(['title' => 'verkeerd wachtwoord', 'message' => 'wachtwoord is verkeerd', 'type' => 'error'], 401);
        exit;
    }
}

$activeSubscriptionId = null;
if ($user['role'] !== 'admin') {
    //^ Controleer of gebruiker een abonnement heeft

    $stmt = $conn->prepare("
                    SELECT s.id
                    FROM users_subscriptions us
                    JOIN subscriptions s ON us.subscription_id = s.id
                    WHERE us.user_id = ? AND us.end_date > NOW()
                    ORDER BY us.end_date DESC
                    LIMIT 1
                ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $subscriptionResult = $stmt->get_result();
    $subscription = $subscriptionResult->fetch_assoc();

    if (!$subscription) {
        jsonResponse([
            'title' => 'Geen actief abonnement',
            'message' => 'Neem een abonnement om in te loggen.',
            'type' => 'error'
        ], 401);
    }

    $activeSubscriptionId = $subscription['id'];

}


//^ Token genereren (JWT)
$issuedAt = time();
$expirationTime = $issuedAt + 10800; // 3 uur waard

unset(
    $user['password'],
    $user['temp_password'],
    $user['temp_password_expires_at'],
    $user['otp_code'],
    $user['otp_expires_at']
);


if ($activeSubscriptionId) {
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user' => $user,
        'subscription' => $activeSubscriptionId
    ];
} else {
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user' => $user,
    ];
}

$jwt = generate_jwt($payload);


//^ kijken of de user laatst 2 weken heeft ingelogd
$lastLogin = $user['last_login'];
$lastLoginTime = new DateTime($lastLogin);
$now = new DateTime();
$interval = $now->diff($lastLoginTime);


if ($interval->days <= 29 && $now > $lastLoginTime) {
    $user['active'] = 1;
} else {
    $user['active'] = 0;
}



//^ OTP als active 0 is
if ($user['active'] === 0) {
    $otp = random_int(100000, 999999);
    $expiry = date('Y-m-d H:i:s', time() + 300); // 5 minuten geldig

    $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
    $stmt->bind_param("ssi", $otp, $expiry, $user['id']);
    $stmt->execute();

    $email = $user['email'];
    $subject = 'Jouw logincode';
    $htmlBody = "Je login code is: <b>$otp</b><br>Deze is 5 minuten geldig.";
    $altBody = "Je login code is: $otp\nDeze is 5 minuten geldig.";

    if (sendMail($email, $subject, $htmlBody, $altBody)) {
        jsonResponse([
            'title' => 'OTP nodig',
            'message' => 'Email verstuurd met een tijdelijke code',
            'type' => 'message',
            'otp_required' => true
        ], 200);
    } else {
        jsonResponse([
            'title' => 'Verzenden mislukt',
            'message' => 'Probeer het later opnieuw',
            'type' => 'error'
        ], 500);
    }

}

//^ Succesvolle login, user info teruggeven (zonder wachtwoord!)
unset($user['password']);
unset($user['last_login']);
unset($user['created_at']);

jsonResponse([
    'message' => 'Login successful',
    'token' => $jwt,
    'active' => $user['active'],
    'temp_used' => $temp_used,
], 200);