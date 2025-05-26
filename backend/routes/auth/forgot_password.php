<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? null;

// Validate input
if (!$email) {
    jsonResponse(['title' => 'Email in nodig', 'message' => 'Geef uw email op', 'type' => 'error'], 400);
    exit;
}

// Check of user bestaat
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    jsonResponse(['title' => 'Geen gebruiker gevonden', 'message' => 'Gebruiker bestaat niet', 'type' => 'error'], 404);
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
    $mail->Host = $mail_host;
    $mail->SMTPAuth = true;
    $mail->Username = $mail_username;
    $mail->Password = $mail_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom($mail_username, 'StudieSalon');
    $mail->addAddress($email);  // Ontvanger

    $mail->isHTML(true);
    $mail->Subject = 'Jouw tijdelijke wachtwoord';
    $mail->Body = "het wachtwoord is: <b>$tempPasswordPlain</b><br>Deze is 30 minuten geldig.";
    $mail->AltBody = "het wachtwoord is: $tempPasswordPlain\nDeze is 30 minuten geldig.";

    $mail->send();

    // Geef aan frontend aan dat tijdelijk wachtwoord is verzonden
    jsonResponse(['title' => 'Email verzonden', 'message' => 'U heeft een tijdelijk wachtwoord gekregen', 'type' => 'success'], 200);

} catch (Exception $e) {
    jsonResponse(['title' => 'Verzenden mislukt', 'message' => 'Probeer het later opnieuw', 'type' => 'error'], 500);
}