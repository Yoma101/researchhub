<?php
// public/chat-api.php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Please ask a question.']);
    exit;
}

// --- GitHub Models API configuration ---
$githubToken = GITHUB_TOKEN;

if (empty($githubToken) || $githubToken === 'YOUR_GITHUB_PERSONAL_ACCESS_TOKEN_HERE') {
    echo json_encode(['reply' => 'GitHub token not configured. Please set GITHUB_TOKEN in config/database.php']);
    exit;
}

$modelId = 'openai/gpt-4o-mini';  // Free via GitHub Models
$apiUrl = 'https://models.github.ai/inference/chat/completions';

$requestPayload = [
    'model' => $modelId,
    'messages' => [
        ['role' => 'user', 'content' => $userMessage]
    ],
    'temperature' => 0.7,
    'max_tokens' => 1000,
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/vnd.github+json',
    'Authorization: Bearer ' . $githubToken,
    'X-GitHub-Api-Version: 2022-11-28',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    error_log("GitHub Models API error: HTTP $httpCode - $response");
    echo json_encode(['reply' => 'AI service temporarily unavailable. Please try again later.']);
    exit;
}

$result = json_decode($response, true);
$reply = $result['choices'][0]['message']['content'] ?? 'Sorry, I could not process that.';
echo json_encode(['reply' => $reply]);
?>