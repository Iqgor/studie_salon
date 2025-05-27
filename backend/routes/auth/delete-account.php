<?php
$data = json_decode(file_get_contents('php://input'), true);
$password = $data['password'] ?? null;

//wachtwoord controleren => kijken of het echt de user is
if (!$password) {
    jsonResponse([
        'title' => 'Wachtwoord vereist',
        'message' => 'Voer je wachtwoord in om je account te verwijderen.',
        'type' => 'warning'
    ], 400);
}

// Fetch current user info, especially the hashed password
$stmt = $conn->prepare("SELECT password, role FROM users WHERE id = ?");
$stmt->bind_param("i", $currentUser['id']);
$stmt->execute();
$stmt->bind_result($hashedPassword, $role);
$stmt->fetch();
$stmt->close();

// Check password
if (!password_verify($password, $hashedPassword)) {
    jsonResponse([
        'title' => 'Ongeldig wachtwoord',
        'message' => 'Het opgegeven wachtwoord is onjuist.',
        'type' => 'error'
    ], 401);
}


// als de user admin is => moet er even worden gekeken of hij de enige is
if ($currentUser['role'] == 'admin') {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND id != ?");
    $stmt->bind_param("i", $currentUser['id']);
    $stmt->execute();
    $stmt->bind_result($otherAdminsCount);
    $stmt->fetch();
    $stmt->close();

    if ($otherAdminsCount == 0) {
        jsonResponse([
            'title' => 'Enige admin',
            'message' => 'Je bent de enige admin. Stel eerst een andere admin aan.',
            'type' => 'warning'
        ], 403);
    }
}
// anders => abonnement stopppen
else {
    $stmt = $conn->prepare("DELETE FROM users_subscriptions WHERE user_id = ?");
    $stmt->bind_param("i", $currentUser['id']);
    if ($stmt->execute()) {
        $stmt->close();
    } else {
        $stmt->close();
        jsonResponse([
            'title' => 'abonnement niet verwijderd',
            'message' => 'Er is iets misgegaan bij het verwijderen van je account.',
            'type' => 'error'
        ], 500);
    }
}

// als alles is gelukt verwijder de user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $currentUser['id']);
if ($stmt->execute()) {
    $stmt->close();

    jsonResponse([
        'title' => 'Gebruiker verwijderd',
        'message' => 'Je account is succesvol verwijderd.',
        'type' => 'success'
    ]);
} else {
    $stmt->close();
    jsonResponse([
        'title' => 'Fout',
        'message' => 'Er is iets misgegaan bij het verwijderen van je account.',
        'type' => 'error'
    ], 500);
}