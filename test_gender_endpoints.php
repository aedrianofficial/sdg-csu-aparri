<?php
// Test script for the SDG AI Gender Analysis API

echo "===== TESTING GENDER ANALYSIS ENDPOINTS =====\n\n";

// Test text analysis
echo "1. Testing /gender/analyze-text endpoint\n";
$data = [
    'text' => 'This project focuses on providing equal opportunities for education to women and girls in rural areas. It also addresses the needs of men and boys to ensure balanced development. The goal is to promote gender equality in education and reduce gender disparities.',
    'target_beneficiaries' => 'Women, girls, men, and boys in rural areas'
];

$ch = curl_init('http://localhost:8003/gender/analyze-text');
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
    $result = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Benefits Women: " . ($result['benefits_women'] ? 'YES' : 'NO') . "\n";
        echo "Benefits Men: " . ($result['benefits_men'] ? 'YES' : 'NO') . "\n";
        echo "Benefits All: " . ($result['benefits_all'] ? 'YES' : 'NO') . "\n";
        echo "Addresses Gender Inequality: " . ($result['addresses_gender_inequality'] ? 'YES' : 'NO') . "\n";
        echo "Confidence: " . ($result['confidence_score'] * 100) . "%\n";
        if ($result['gender_notes']) {
            echo "Notes: " . $result['gender_notes'] . "\n";
        }
    } else {
        echo "Error parsing JSON response\n";
        echo $response . "\n";
    }
}

echo "\n\n";

// Test file upload (if you have a test file)
echo "2. Testing /gender/analyze endpoint with file upload\n";
echo "(To run this test, place a test PDF file named 'test_gender.pdf' in the same directory)\n";

$testFile = __DIR__ . '/test_gender.pdf';
if (file_exists($testFile)) {
    $ch = curl_init('http://localhost:8003/gender/analyze');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    
    $cfile = new CURLFile($testFile, 'application/pdf', 'test_gender.pdf');
    $data = [
        'file' => $cfile,
        'target_beneficiaries' => 'Women, girls, men, and boys in rural areas'
    ];
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "ERROR: " . $error . "\n";
    } else {
        echo "RESPONSE: \n";
        $result = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "Benefits Women: " . ($result['benefits_women'] ? 'YES' : 'NO') . "\n";
            echo "Benefits Men: " . ($result['benefits_men'] ? 'YES' : 'NO') . "\n";
            echo "Benefits All: " . ($result['benefits_all'] ? 'YES' : 'NO') . "\n";
            echo "Addresses Gender Inequality: " . ($result['addresses_gender_inequality'] ? 'YES' : 'NO') . "\n";
            echo "Confidence: " . ($result['confidence_score'] * 100) . "%\n";
            if ($result['gender_notes']) {
                echo "Notes: " . $result['gender_notes'] . "\n";
            }
        } else {
            echo "Error parsing JSON response\n";
            echo $response . "\n";
        }
    }
} else {
    echo "Test file not found. Skipping file upload test.\n";
}

echo "\n===== TESTING COMPLETE =====\n"; 