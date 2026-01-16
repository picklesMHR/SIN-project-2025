<?php
// Set timezone to New Zealand
date_default_timezone_set('Pacific/Auckland');

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get data from request
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

// Handle both JSON (fetch) and form-encoded data (sendBeacon/FormData)
if (strpos($contentType, 'application/json') !== false) {
    // JSON request from fetch
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
} else if (strpos($contentType, 'multipart/form-data') !== false || empty($contentType)) {
    // Form-encoded or multipart data from sendBeacon/FormData
    $data = $_POST;
} else {
    // Try to parse as form-encoded or JSON fallback
    $input = file_get_contents('php://input');
    if (strpos($input, '{') === 0) {
        $data = json_decode($input, true);
    } else {
        parse_str($input, $data);
    }
}

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit();
}

// Validate required fields
if (empty($data['participant_id']) || empty($data['csv_data'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing participant_id or csv_data']);
    exit();
}

$participant_id = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['participant_id']);
$csv_data = $data['csv_data'];

// Create result_files directory if it doesn't exist
if (!is_dir('result_files')) {
    if (!mkdir('result_files', 0755, true)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create result_files directory']);
        exit();
    }
}

// Generate filename with timestamp
$timestamp = date('Y-m-d_H-i-s');
$filename = "result_files/sin_data_{$participant_id}_{$timestamp}.csv";

// Write the CSV data to file
if (file_put_contents($filename, $csv_data) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save data']);
    exit();
}

// Success response
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Data saved successfully',
    'filename' => $filename
]);
?>
