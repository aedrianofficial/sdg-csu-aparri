<?php
// Simple script to test the SDG AI API

$data = [
    'text' => 'The Sustainable Development Goal (SDG) Portal of the Cagayan State University-Aparri Research and Development Extension Unit (SDG Monitor) is an innovative and mobile-responsive platform aligning the university\'s research and development initiatives and contributions with the United Nations\' SDGs.'
];

$ch = curl_init('http://localhost:8003/sdg/analyze-text');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "ERROR: " . $error . "\n";
} else {
    echo "RESPONSE: \n";
    $json = json_decode($response, true);
    echo json_encode($json, JSON_PRETTY_PRINT);
} 