<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Require PHPMailer and other configs as you already have
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once 'load_env.php';
loadEnv(__DIR__ . '/.env');
require_once __DIR__ . '/jwt_helper.php';
require_once __DIR__ . '/mail_helper.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require_once __DIR__ . '/config.php';
$conn = getDBConnection();


//global env variables
$secret_key = getenv('jwt_KEY');
$mail_host = getenv('MAIL_HOST');
$mail_username = getenv('MAIL_USERNAME');
$mail_password = getenv('MAIL_PASSWORD');


// Parse URL and method
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');
$segments = explode('/', $path);
$resource = $segments[2] ?? null;

// List of public routes that don't need auth
$publicRoutes = [
    'login',
    'register',
    'forgot_password',
    'subscriptions',
    'verify_otp',
    'subscribeTrial',
    'create-payment',
    'update-payment'
];


// JWT verificatie function
function base64UrlDecode($data)
{
    return base64_decode(strtr($data, '-_', '+/'));
}

function isValidJWT($jwt, $secret_key)
{
    $parts = explode('.', $jwt);
    if (count($parts) !== 3)
        return false;

    list($headerB64, $payloadB64, $signatureB64) = $parts;
    if (empty($headerB64) || empty($payloadB64) || empty($signatureB64))
        return false;

    $header = json_decode(base64UrlDecode($headerB64), true);
    if (!isset($header['alg']) || $header['alg'] !== 'HS256')
        return false;

    $signature = hash_hmac('sha256', "$headerB64.$payloadB64", $secret_key, true);
    $expectedSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    if (!hash_equals($signatureB64, $expectedSignature))
        return false;

    $payload = json_decode(base64UrlDecode($payloadB64), true);
    if (!$payload || !isset($payload['exp']) || time() >= $payload['exp'])
        return false;

    return $payload;
}

if (!in_array($resource, $publicRoutes)) {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Ontbrekende autorisatieheader', 'action' => 'logout']);

        exit;
    }
    if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        $jwt = $matches[1];
        $payload = isValidJWT($jwt, $secret_key);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Ongeldige of verlopen token', 'action' => 'logout']);
            exit;
        }

        // Token is valid, user is geathenticated
        // kan nu de informatie van de user pakken uit de token
        $currentUser = $payload['user'];

    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Misvormde autorisatieheader', 'action' => 'logout']);
        exit;
    }
}


// After auth check, prepare routing table
$routes = [
    // Public routes
    'GET subscriptions' => __DIR__ . '/routes/subscriptions/get.php',
    'POST quote' => __DIR__ . '/routes/quote.php',

    // Auth routes
    'GET activeSubscription' => __DIR__ . '/routes/auth/activeSubscription.php',
    'POST login' => __DIR__ . '/routes/auth/login.php',
    'POST register' => __DIR__ . '/routes/auth/register.php',
    'POST forgot_password' => __DIR__ . '/routes/auth/forgot_password.php',
    'POST verify_otp' => __DIR__ . '/routes/auth/verify_otp.php',
    'POST subscribeTrial' => __DIR__ . '/routes/auth/subscribeTrial.php',
    'POST create-payment' => __DIR__ . '/routes/auth/create-payment.php',
    'POST update-payment' => __DIR__ . '/routes/auth/update-payment.php',
    'PUT change_password' => __DIR__ . '/routes/auth/change-password.php',	
    'PUT update_user' => __dir__ . '/routes/auth/update-user.php',
    'DELETE Delete_account' => __DIR__ . '/routes/delete-user/delete.php',

    // Carousel routes
    'POST editCarouselLink' => __DIR__ . '/routes/carousel/link.php',
    'POST getCarouselItems' => __DIR__ . '/routes/carousel/get.php',
    'POST addCarouselItem' => __DIR__ . '/routes/carousel/add.php',

    // activities routes
    'POST create_activity' => __DIR__ . '/routes/activities/create.php',
    'POST edit_activity' => __DIR__ . '/routes/activities/edit.php',
    'POST delete_activity' => __DIR__ . '/routes/activities/delete.php',
    'POST activities' => __DIR__ . '/routes/activities/get.php',

    // teksten routes
    'POST addText' => __DIR__ . '/routes/teksten/addText.php',
    'POST addLinks' => __DIR__ . '/routes/teksten/addLinks.php',
    'POST editLink' => __DIR__ . '/routes/teksten/editLink.php',
    'POST getTekst' => __DIR__ . '/routes/teksten/gettekst.php',
    'POST editTekst'=> __DIR__ . '/routes/teksten/editTekst.php',
    'POST getTekstLinks' => __DIR__ . '/routes/teksten/getTekstLinks.php',
    'POST editTitleLink' => __DIR__ . '/routes/teksten/editTitleLinks.php',
    'POST getLikes' => __DIR__ . '/routes/teksten/getLikes.php',
    'POST sendLikes' => __DIR__ . '/routes/teksten/sendLikes.php',
    'POST makeSinglefile' => __DIR__ . '/routes/teksten/makeSinglefile.php',

    // Admin routes
    'POST adminInfo' => __DIR__ . '/routes/admin/getInfo.php',
    'POST editItem' => __DIR__ . '/routes/admin/editItem.php',
];


// Build route key
$routeKey = "$method $resource";

if (isset($routes[$routeKey])) {
    require $routes[$routeKey];
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
    exit;
}


