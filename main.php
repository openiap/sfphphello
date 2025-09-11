<?php
#!/usr/bin/env php

// Only show startup messages when not running as built-in server
if (php_sapi_name() !== 'cli-server') {
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
    // Check if we have the required server variables
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Log to error_log so it doesn't interfere with HTTP response
    error_log("Processing: $method $path from $clientIp");
    
    // Send CORS headers first
    if (!headers_sent()) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
    }
    
    if ($method === 'OPTIONS') {
        // For OPTIONS, send 200 status and exit
        if (!headers_sent()) {
            http_response_code(200);
        }
        exit();
    }
    
    $dt = new DateTime();
    $version = $_ENV['SF_TAG'] ?? 'latest';
    
    $responseData = [
        'message' => 'Hello from PHP',
        'dt' => $dt->format('c'), // ISO 8601 format
        'version' => $version
    ];
    
    // Set content type and send JSON response
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(200);
    }
    
    echo json_encode($responseData);
    
    // Ensure output is flushed
    if (ob_get_level()) {
        ob_end_flush();
    }
    flush();
}

$host = '0.0.0.0';
$port = 3000;

// Only show server messages when not running as built-in server
if (php_sapi_name() !== 'cli-server') {
    echo "Server created\n";
    echo "Server listening callback\n";
    echo "Server running on $host:$port\n";
}

// Check if running as built-in server
if (php_sapi_name() === 'cli-server') {
    // Running with php -S, handle single request
    // Suppress any previous output for clean JSON response
    if (ob_get_level()) {
        ob_clean();
    }
    handleRequest();
    // Add flush to ensure output is sent immediately
    if (ob_get_level()) {
        ob_end_flush();
    }
    flush();
} else {
    // Running as CLI script - start built-in server
    echo "Starting PHP built-in server...\n";
    echo "To start the server, run: php -S $host:$port " . __FILE__ . "\n";
    echo "Or run this script with the built-in server command automatically:\n";
    
    // Auto-start the built-in server
    $command = "php -S $host:$port " . escapeshellarg(__FILE__);
    echo "Executing: $command\n";
    
    // Execute the server command
    passthru($command);
}
