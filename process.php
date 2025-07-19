<?php

require_once 'includes/db.php';
header('Content-Type: application/json');
session_start();

$user_id = 1;

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['error' => 'empty messsage']);
    exit;
}

$payload = json_encode([
    'model' => 'llama3',
    'messages' => [
        ['role' => 'user', 'content' => $message]
    ]
]);

$ch = curl_init('http://localhost:11434/api/chat');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['reply' => "⚠️ Error talking to the AI. Is Ollama running?"]);
    exit;
}

$lines = explode("\n", $response);
$reply = '';

foreach ($lines as $line) {
    if (trim($line)) {
        $data = json_decode($line, true);
        $reply .= $data['message']['content'] ?? '';
    }
}

$reply = trim($reply) ?: "🤖 AI didn't respond.";

curl_close($ch);

$stmt = $pdo->prepare("
    INSERT INTO conversations (user_id, message, response)
    VALUES (?, ?, ?)
");
$stmt->execute([$user_id, $message, $reply]);

echo json_encode(['reply' => $reply]);