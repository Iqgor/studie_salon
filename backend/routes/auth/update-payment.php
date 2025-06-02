<?php
$apiKey = getenv('mollie_KEY');
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

$status = $payment['status'] ?? null;
$userId = $payment['metadata']['user_id'] ?? null;
$userEmail = $payment['metadata']['user_email'] ?? null;
$subscriptionId = $payment['metadata']['subscription_id'] ?? null;
$subscriptionPeriod = $payment['metadata']['subscription_period'] ?? null;
$price = isset($payment['metadata']['subscription_price']) ? (float) $payment['metadata']['subscription_price'] : null;

switch ($status) {
    case 'paid':
        if (!$userId || $price === null) {
            http_response_code(400);
            exit();
        }

        $createdAt = date('Y-m-d H:i:s');
        $paymentId = $payment['id'] ?? null;

        // Check for duplicate payment
        $check = $conn->prepare("SELECT id FROM invoices WHERE mollie_payment_id = ?");
        $check->bind_param("s", $paymentId);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            http_response_code(200);
            exit();
        }

        // Insert invoice
        $stmt = $conn->prepare("INSERT INTO invoices (user_id, user_email, subscription_id, amount, is_trial, created_at, status, mollie_payment_id) VALUES (?, ?, ?, ?, 0, ?, 'paid', ?)");
        $stmt->bind_param("issdss", $userId, $userEmail, $subscriptionId, $price, $createdAt, $paymentId);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            http_response_code(500);
            exit();
        }

        // Insert subscription
        if ($subscriptionPeriod === 'maandelijks') {
            $expiryDate = date('Y-m-d', strtotime('+1 month'));
        } elseif ($subscriptionPeriod === 'jaarlijks') {
            $expiryDate = date('Y-m-d', strtotime('+1 year'));
        } else {
            http_response_code(400);
            exit();
        }

        $today = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO users_subscriptions (user_id, subscription_id, start_date, end_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $userId, $subscriptionId, $today, $expiryDate);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            http_response_code(500);
            exit();
        }

        break;

    case 'open':
    case 'pending':
    case 'authorized':
    case 'canceled':
    case 'expired':
    case 'failed':
        // Log or handle these statuses as needed
        http_response_code(200);
        break;

    default:
        http_response_code(400);
        break;
}
