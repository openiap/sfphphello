<?php
#!/usr/bin/env php

// Only output debug info if running from CLI and not handling web requests
$isCliMode = php_sapi_name() === 'cli';
$isWebRequest = isset($_SERVER['REQUEST_METHOD']);

if ($isCliMode && !$isWebRequest) {
    echo "Script start\n";
}

// Function to send CORS headers
function sendCorsHeaders() {
    if (!headers_sent()) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
    }
}

// Function to handle requests
function handleRequest() {
    global $isCliMode, $isWebRequest;
    
    // Check if we have the required server variables
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    if ($isCliMode && !$isWebRequest) {
        echo "Request received: $method $path from $clientIp\n";
    }
    
    if ($method === 'OPTIONS') {
        if (!headers_sent()) {
            http_response_code(200);
            sendCorsHeaders();
        }
        return;
    }
    
    $dt = new DateTime();
    $version = $_ENV['SF_TAG'] ?? 'latest';
    
    $responseData = [
        'message' => 'Hello from PHP',
        'dt' => $dt->format('c'), // ISO 8601 format
        'version' => $version
    ];
    
    if (!headers_sent()) {
        http_response_code(200);
        header('Content-Type: application/json');
        sendCorsHeaders();
    }
    
    echo json_encode($responseData);
}

$host = '0.0.0.0';
$port = 3000;

if ($isCliMode && !$isWebRequest) {
    echo "Server created\n";
    echo "Server listening callback\n";
    echo "Server running on $host:$port\n";
}

// Create a simple router for the built-in server
if (php_sapi_name() === 'cli-server') {
    handleRequest();
} else {
    // If not using built-in server, we need to use a different approach
    // This would typically be handled by a web server like Apache or Nginx
    handleRequest();
}
