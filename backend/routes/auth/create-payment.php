<?php
if (!$mollie_key) {
    jsonResponse(['title' => 'API key ontbreekt', 'message' => 'De API key is niet ingesteld op de server.', 'type' => 'error'], 500);
}
$data = json_decode(file_get_contents('php://input'), true);
$price = $data['price'] ?? null;
$currency = 'EUR'; // Naar euro want wij zijn in Nederland
$description = 'Betaling voor StudieSalon'; // Standaard beschrijving
$redirectUrl = 'https://33993.hosts1.ma-cloud.nl/login'; // Standaard redirect URL
$webhookUrl = 'https://33993.hosts1.ma-cloud.nl/backend/update-payment'; // optioneel
$method = 'ideal'; // Standaard betaalmethode

$data = json_decode(file_get_contents('php://input'), true);

$userEmail = $data['email'] ?? null;
$password = $data['password'] ?? null;
$subscriptionId = $data['plan_id'] ?? null;
$subscriptionPeriod = $data['periode'] ?? null;

if (!$password || !$userEmail || !$subscriptionId) {
    jsonResponse(["title" => 'Gegevens missen', 'message' => 'we hebben uw inlog gegevens nodig', 'type' => 'error'], 400);
}

// Gebruiker zoeken op email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 0) {
    jsonResponse(['title' => 'niet gevonden', 'message' => 'Geen gebruiker gevonden met email: ' . $userEmail, 'type' => 'error'], 401);
    exit;
}
$temp_used = false; // kijken of temp wachtwoord is gebruikt
$user = $result->fetch_assoc();


// Wachtwoord controleren
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

$data = [
    'amount' => [
        'currency' => $currency,
        'value' => number_format($price, 2, '.', '') // formateren naar 2 decimalen voor zekerheid
    ],
    'description' => $description,
    'redirectUrl' => $redirectUrl,
    'webhookUrl' => $webhookUrl,
    'method' => $method,
    'metadata' => [
        'user_id' => $user['id'],
        'user_email' => $userEmail,
        'subscription_id' => $subscriptionId,
        'subscription_period' => $subscriptionPeriod,
        'subscription_price' => $price,
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $mollie_key,
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response, true); // Omzetten naar array (optioneel)

jsonResponse($json); // Of eventueel: jsonResponse($json, 200);