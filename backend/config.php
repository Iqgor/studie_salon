<?php
// Database configuration

//local
    // define('DB_HOST', 'localhost');
    // define('DB_USER', 'root');
    // define('DB_PASS', '');
    // define('DB_NAME', 'studie_saloon');

// staging
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'c7637igor');
    define('DB_PASS', 'Ballo2711!');
    define('DB_NAME', 'c7637studie');

// live
    // naar db: https://teams.microsoft.com/l/message/19:4227a46d-c0ee-4734-ad95-f8d543207b89_f0ecb4fb-a2c9-45d0-8a7e-9a95ea6e2462@unq.gbl.spaces/1749728624215?context=%7B%22contextType%22%3A%22chat%22%7D
    // define('DB_HOST', 'localhost');
    // define('DB_USER', 'u511577715_studysaloon');
    // define('DB_PASS', 'v8$Ve8x!4VT^');
    // define('DB_NAME', 'u511577715_studysaloon');

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



//! betaalings service
// require_once 'vendor/autoload.php';

// $mollie = new \Mollie\Api\MollieApiClient();
// $mollie->setApiKey('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // gebruik je test/live key
// $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
