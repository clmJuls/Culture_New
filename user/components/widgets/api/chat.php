<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userMessage = $data['message'];

    // Call Ollama API
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://127.0.0.1:11434/api/generate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'mistral',
            'prompt' => "You are Kulturifiko, a cultural expert assistant. Please provide accurate and respectful information about cultures. User question: " . $userMessage,
            'stream' => false
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if ($httpCode !== 200) {
        throw new Exception("HTTP Error: " . $httpCode);
    }

    $result = json_decode($response, true);
    
    if (!$result || !isset($result['response'])) {
        // Log the raw response for debugging
        error_log("Raw Ollama response: " . $response);
        throw new Exception("Invalid response format from Ollama");
    }

    echo json_encode([
        'response' => $result['response']
    ]);

} catch (Exception $e) {
    error_log("Error in chat.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

curl_close($curl);
?>