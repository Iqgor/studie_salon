<?php
// Database configuration
//local
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'studie_saloon');
// server
// define('DB_HOST', '127.0.0.1');
// define('DB_USER', 'c7637igor');
// define('DB_PASS', 'Ballo2711!');
// define('DB_NAME', 'c7637studie');


// Connect to database
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4"); 
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Helper function to return JSON responses
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

return [
    'MAIL_PASSWORD' => 'ksjd ymnj rwom pkjx',
    'MAIL_USERNAME' => 'bilalelkoudadi526@gmail.com',
    'MAIL_HOST' => 'smtp.gmail.com',
];

//! betaalings service
// require_once 'vendor/autoload.php';

// $mollie = new \Mollie\Api\MollieApiClient();
// $mollie->setApiKey('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // gebruik je test/live key
// $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
