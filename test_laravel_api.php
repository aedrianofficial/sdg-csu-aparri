<?php
// Script to test the Laravel API endpoint

$data = [
    'text' => 'The Sustainable Development Goal (SDG) Portal of the Cagayan State University-Aparri Research and Development Extension Unit (SDG Monitor) is an innovative and mobile-responsive platform aligning the university\'s research and development initiatives and contributions with the United Nations\' SDGs.'
];

$ch = curl_init('http://localhost:8000/api/sdg-ai/analyze');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Write to a file instead of stdout
$output = '';
if ($error) {
    $output = "ERROR: " . $error . "\n";
} else {
    $output = "RESPONSE: \n";
    $json = json_decode($response, true);
    $output .= json_encode($json, JSON_PRETTY_PRINT);
}

file_put_contents('api_test_result.txt', $output); 