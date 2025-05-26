<?php
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'] ?? null;
$newPassword = $data['newPassword'] ?? null;
$oldPassword = $data['oldPassword'] ?? null;

if (!$userId || !$newPassword || !$oldPassword) {
    jsonResponse([
        'title' => 'Gegevens missen',
        'message' => 'Gebruikers-ID, oud wachtwoord en nieuw wachtwoord zijn vereist.',
        'type' => 'error'
    ], 400);
    exit;
}

// 1. Haal de gebruiker op uit de database
$stmt = $conn->prepare("SELECT password, temp_password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(['title' => 'Gebruiker niet gevonden', 'message' => 'Ongeldig gebruikers-ID.', 'type' => 'error'], 404);
    exit;
}

$user = $result->fetch_assoc();

// 2. Controleer of het ingevoerde oude wachtwoord overeenkomt met het huidige wachtwoord of tijdelijke wachtwoord
$passwordMatches = password_verify($oldPassword, $user['password']);
$tempPasswordMatches = $user['temp_password'] ? password_verify($oldPassword, $user['temp_password']) : false;

if (!$passwordMatches && !$tempPasswordMatches) {
    jsonResponse([
        'title' => 'Oud wachtwoord ongeldig',
        'message' => 'Het ingevoerde oude wachtwoord is onjuist.',
        'type' => 'error'
    ], 401);
    exit;
}

// 3. Update wachtwoord
$hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

$updateStmt = $conn->prepare("
                    UPDATE users 
                    SET 
                        password = ?, 
                        temp_password = NULL, 
                        temp_password_expires_at = NULL, 
                        updated_at = NOW() 
                    WHERE id = ?
                ");

$updateStmt->bind_param("si", $hashedNewPassword, $userId);

if ($updateStmt->execute()) {
    jsonResponse([
        'title' => 'Wachtwoord veranderd',
        'message' => 'Uw wachtwoord is succesvol aangepast.',
        'type' => 'success'
    ], 200);
} else {
    jsonResponse([
        'title' => 'Fout bij wijzigen',
        'message' => 'Er is een fout opgetreden bij het wijzigen van het wachtwoord.',
        'type' => 'error'
    ], 500);
}