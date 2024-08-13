<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'];
$use_api = $input['useApi'];
$api_key = $input['apiKey'];
$endpoint = $input['endpoint'];
$model_name = $input['modelName'];

$responses = json_decode(file_get_contents('responses.json'), true);

if (isset($responses[$user_message])) {
    echo json_encode($responses[$user_message]);
    exit;
}

if (!$use_api) {
    echo json_encode("I'm sorry, I don't have a specific answer for that question.");
    exit;
}

$data = [
    'messages' => [
        [
            'role' => 'user',
            'content' => $user_message,
        ],
    ],
    'max_tokens' => 800,
    'temperature' => 0.7,
];

$ch = curl_init("$endpoint/openai/deployments/$model_name/chat/completions?api-version=2023-05-15");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'api-key: ' . $api_key,
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    exit;
}

curl_close($ch);

$response_data = json_decode($response, true);

if (isset($response_data['choices'][0]['message']['content'])) {
    $bot_message = $response_data['choices'][0]['message']['content'];
    echo json_encode($bot_message);
} else {
    echo json_encode(['error' => 'Unexpected API response', 'response' => $response_data]);
}
?>