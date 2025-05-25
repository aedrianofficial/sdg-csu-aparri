<?php
// Script to test the Laravel API endpoint

// Test SDG analysis
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

// Write SDG analysis results to a file
$output = '';
if ($error) {
    $output = "ERROR (SDG Analysis): " . $error . "\n";
} else {
    $output = "RESPONSE (SDG Analysis): \n";
    $json = json_decode($response, true);
    $output .= json_encode($json, JSON_PRETTY_PRINT);
}

file_put_contents('sdg_api_test_result.txt', $output);

// Test Gender Analysis
$data = [
    'text' => 'This research examines the impact of gender-based policies on education outcomes. 
    The study focuses on how women and girls can benefit from inclusive educational practices, 
    while also addressing the needs of boys and men in rural communities. 
    The goal is to promote gender equality and reduce educational disparities for approximately 
    200 women and 150 men in the target communities.',
    'target_beneficiaries' => 'Women, girls, men, and boys in rural communities'
];

$ch = curl_init('http://localhost:8000/api/gender-ai/analyze');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Write Gender analysis results to a file
$output = '';
if ($error) {
    $output = "ERROR (Gender Analysis): " . $error . "\n";
} else {
    $output = "RESPONSE (Gender Analysis): \n";
    $json = json_decode($response, true);
    $output .= json_encode($json, JSON_PRETTY_PRINT);
}

file_put_contents('gender_api_test_result.txt', $output); 