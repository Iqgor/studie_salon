<?php
function generate_jwt(array $payload): string {
    // key ophalen uit de env
    $secret_key = getenv('jwt_KEY');

    // standaard header
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload_json = json_encode($payload);

    // Base64Url encode header en payload
    $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
    $base64UrlPayload = rtrim(strtr(base64_encode($payload_json), '+/', '-_'), '=');

    // maak de signature
    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret_key, true);
    $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    // geef de JWT
    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
}
