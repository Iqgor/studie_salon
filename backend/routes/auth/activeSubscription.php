<?php
$userId = $_GET['user_id'];

// STEP 1: Retrieve the user's active or any subscription (if admin)
if ($currentUser['role'] === 'admin') {
    jsonResponse(['title' => 'Admin access', 'message' => 'Admin Gebruikers mogen alles doen', 'type' => 'success'], 200);
} else {
    $stmt = $conn->prepare("
        SELECT us.*, s.name AS subscription_name
        FROM users_subscriptions us
        JOIN subscriptions s ON us.subscription_id = s.id
        WHERE us.user_id = ? AND us.end_date > NOW()
        ORDER BY us.end_date DESC
        LIMIT 1
    ");
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$subscriptionResult = $stmt->get_result();
$subscription = $subscriptionResult->fetch_assoc();

if ($subscription) {
    // Fetch features as before
$stmtFeatures = $conn->prepare("
    SELECT f.*, sf.access_level
    FROM subscription_features sf
    JOIN features f ON sf.feature_id = f.id
    WHERE sf.subscription_id = ?
");
$stmtFeatures->bind_param("i", $subscription['subscription_id']);
$stmtFeatures->execute();
$featuresResult = $stmtFeatures->get_result();

$features = [];
while ($row = $featuresResult->fetch_assoc()) {
    $features[] = $row; // bevat nu ook 'access_level'
}

$subscription['features'] = $features;



    jsonResponse($subscription, 200);
} else {
    jsonResponse(['title' => 'Geen abonnement', 'message' => 'U heeft geen geldig abonnement', 'type' => 'error'], 404);

}

