<?php
// Haal de JSON-data op uit het request body en decodeer het naar een PHP-array
$data = json_decode(file_get_contents('php://input'), true);

// Verkrijg het huidige gebruikers-ID, indien beschikbaar
$userId = $currentUser['id'] ?? null;

// Controleer of de gebruiker is ingelogd
if (!$userId) {
    jsonResponse([
        'title' => 'Niet geauthenticeerd',
        'message' => 'U moet ingelogd zijn om deze functie uit te voeren',
        'type' => 'error'
    ], 400);
    exit;
}

// Voorbereiden van velden die kunnen worden bijgewerkt
$fieldsToUpdate = [];
$params = [];
$types = "";

// Voeg 'name' toe aan het update-query als het is meegegeven
if (!empty($data['name'])) {
    $fieldsToUpdate[] = "name = ?";
    $params[] = $data['name'];
    $types .= "s"; // 's' staat voor string
}

// Voeg 'email' toe aan het update-query als het is meegegeven
if (!empty($data['email'])) {
    $fieldsToUpdate[] = "email = ?";
    $params[] = $data['email'];
    $types .= "s";
}

// Stop als er geen velden zijn om bij te werken
if (empty($fieldsToUpdate)) {
    jsonResponse([
        'title' => 'Geen wijzigingen',
        'message' => 'Er zijn geen gegevens gevonden om bij te werken.',
        'type' => 'message'
    ], 400);
    exit;
}

// Haal het huidige e-mailadres van de gebruiker op uit de database
$emailQuery = $conn->prepare("SELECT email FROM users WHERE id = ?");
$emailQuery->bind_param("i", $userId);
$emailQuery->execute();
$emailResult = $emailQuery->get_result();
$currentData = $emailResult->fetch_assoc();

// Bepaal het oude en nieuwe e-mailadres
$oldEmail = $currentData['email'] ?? null;
$newEmail = trim($data['email'] ?? '');

// Controleer of het nieuwe e-mailadres al bestaat bij een andere gebruiker
if ($newEmail && $newEmail !== $oldEmail) {
    $emailExistsQuery = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $emailExistsQuery->bind_param("si", $newEmail, $userId);
    $emailExistsQuery->execute();
    $emailExistsResult = $emailExistsQuery->get_result();

    if ($emailExistsResult->num_rows > 0) {
        jsonResponse([
            'title' => 'E-mailadres bestaat al',
            'message' => 'Dit e-mailadres is al in gebruik door een andere gebruiker.',
            'type' => 'error'
        ], 400);
        exit;
    }
}

// Stel het UPDATE-query samen
$sql = "UPDATE users SET " . implode(", ", $fieldsToUpdate) . " WHERE id = ?";
$params[] = $userId;
$types .= "i"; // 'i' staat voor integer

// Bereid het statement voor en voer het uit
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    // Als het e-mailadres is gewijzigd, werk ook bij in 'invoices'-tabel
if ($oldEmail && $newEmail && $oldEmail !== $newEmail) {
        
    $invoiceUpdate = $conn->prepare("UPDATE invoices SET user_email = ? WHERE user_email = ?");
    if (!$invoiceUpdate) {
        error_log("Fout bij voorbereiden van invoice-update: " . $conn->error);
    } else {
        $invoiceUpdate->bind_param("ss", $newEmail, $oldEmail);
        $invoiceUpdate->execute();

        if ($invoiceUpdate->affected_rows === 0) {
            error_log("Geen bijwerkingen in invoices voor email: $oldEmail -> $newEmail");
        }
    }
}

    // Haal de bijgewerkte gebruikersgegevens op
    $userQuery = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $userQuery->bind_param("i", $userId);
    $userQuery->execute();
    $result = $userQuery->get_result();
    $user = $result->fetch_assoc();

    // Als de gebruiker niet is gevonden (moet eigenlijk niet gebeuren), geef waarschuwing
    if (!$user) {
        jsonResponse([
            'title' => 'Succesvol bijgewerkt',
            'message' => 'Je profiel is bijgewerkt. Log opnieuw in om verder te gaan.',
            'type' => 'warning'
        ], 500);
    }

    // Verwijder gevoelige velden uit het gebruikersobject
    unset(
        $user['password'],
        $user['temp_password'],
        $user['temp_password_expires_at'],
        $user['otp_code'],
        $user['otp_expires_at']
    );

    // Genereer een nieuwe JWT-token met de bijgewerkte gebruikersgegevens
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user' => $user,
    ];

    $jwt = generate_jwt($payload);

    // Stuur een succesvolle JSON-response terug met token
    jsonResponse([
        'title' => 'Gelukt',
        'message' => 'Uw account is aangepast',
        'type' => 'success',
        'token' => $jwt
    ], 200);
} else {
    // Bij mislukte update, geef foutmelding
    jsonResponse([
        'title' => 'Mislukt',
        'message' => 'Kon u niet aanpassen',
        'status' => 'error'
    ], 500);
}
