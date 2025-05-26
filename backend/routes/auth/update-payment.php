<?php
$apiKey = 'test_kVPG44gJnW585v7EcMUNPp9ksfxEAR';
$paymentId = $_POST['id'];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.mollie.com/v2/payments/$paymentId");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
curl_close($ch);

$payment = json_decode($response, true);
$userId = $payment['metadata']['user_id'] ?? null;
$userEmail = $payment['metadata']['user_email'] ?? null;
$subscriptionId = $payment['metadata']['subscription_id'] ?? null;
$subscriptionPeriod = $payment['metadata']['subscription_period'] ?? null;
$price = isset($payment['metadata']['subscription_price']) ? (float) $payment['metadata']['subscription_price'] : null;

if ($payment['status'] === 'paid' && $userId && $price !== null) {
    $createdAt = date('Y-m-d H:i:s');
    $paymentId = $payment['id'] ?? null; // The Mollie payment ID

    //check of het al betaald is Geen dubbele betaling

    $check = $conn->prepare("SELECT id FROM invoices WHERE mollie_payment_id = ?");
    $check->bind_param("s", $paymentId);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        http_response_code(200); // Already handled
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO invoices (user_id, user_email, subscription_id, amount, is_trial, created_at, status, mollie_payment_id) VALUES (?, ?, ?, ?, 0, ?, 'paid', ?)");
    $stmt->bind_param("issdss", $userId, $userEmail, $subscriptionId, $price, $createdAt, $paymentId);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(500);
        exit();
    }



    // zet de nieuwe actieve abonnement in de users_subscriptions tabel
    if ($subscriptionPeriod === 'maandelijks') {
        $expiryDate = date('Y-m-d', strtotime('+1 month'));
    } else if ($subscriptionPeriod === 'jaarlijks') {
        $expiryDate = date('Y-m-d', strtotime('+1 year'));
    }

    $today = date('Y-m-d');
    $stmt = $conn->prepare("INSERT INTO users_subscriptions (user_id, subscription_id, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $userId, $subscriptionId, $today, $expiryDate);
    $stmt->execute();



    if ($stmt->affected_rows === 0) {
        http_response_code(500);
        exit();
    }


}