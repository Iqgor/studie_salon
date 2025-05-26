<?php
$data = json_decode(file_get_contents('php://input'), true);

                $userEmail = $data['email'] ?? null;
                $password = $data['password'] ?? null;
                $subscriptionId = $data['plan_id'] ?? null;

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

                // kijk of er een proef periode was genomen

                $stmt = $conn->prepare("SELECT id FROM invoices WHERE user_email = ? AND subscription_id = ? AND is_trial = 1 LIMIT 1");
                $stmt->bind_param("si", $userEmail, $subscriptionId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    jsonResponse(['title' => 'Proefversie verlopen', 'message' => 'Je hebt deze proefversie al gebruikt.', 'type' => 'warning'], 403);

                }



                // controleer of de gebruiker al een actief abonnement heeft
                $today = date('Y-m-d');
                $stmt = $conn->prepare("SELECT id FROM users_subscriptions WHERE user_id = ? AND end_date > ?");
                $stmt->bind_param("is", $user['id'], $today);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    jsonResponse(['title' => 'Wijziging geblokkeerd', 'message' => 'Je hebt al een actief abonnement.', 'type' => 'warning'], 403);
                }


                // maak een factuur aan met is_trial = 1
                $createdAt = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("INSERT INTO invoices (user_id, user_email, subscription_id, amount, is_trial, created_at) VALUES (?, ?, ?, 0, 1, ?)");
                $stmt->bind_param("isis", $user['id'], $userEmail, $subscriptionId, $createdAt);
                $stmt->execute();



                if ($stmt->affected_rows === 0) {
                    jsonResponse(['title' => 'factuur probleem', 'message' => 'Kon factuur niet aanmaken.', 'type' => 'error'], 500);

                }



                // zet de nieuwe actieve abonnement in de users_subscriptions tabel
                $expiryDate = date('Y-m-d', strtotime('+3 days'));
                $stmt = $conn->prepare("INSERT INTO users_subscriptions (user_id, subscription_id, start_date, end_date) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $user['id'], $subscriptionId, $today, $expiryDate);
                $stmt->execute();



                if ($stmt->affected_rows === 0) {
                    jsonResponse(['title' => 'iets verkeerd gegaan', 'message' => 'Kon abonnement niet activeren.', 'type' => 'error'], 500);

                }


                //^ Token genereren (JWT)
                $issuedAt = time();
                $expirationTime = $issuedAt + 3600; // 1 uur waard

                unset(
                    $user['password'],
                    $user['temp_password'],
                    $user['temp_password_expires_at'],
                    $user['otp_code'],
                    $user['otp_expires_at']
                );

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
                    'title' => 'Proef versie genomen',
                    'message' => 'Proefabonnement succesvol geactiveerd.',
                    'type' => 'success',
                    'token' => $jwt,
                    'temp_used' => $temp_used,

                ], 200);