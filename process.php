<?php

require_once 'includes/db.php';

header('Content-Type: application/json');

session_start();

$user_id = 1;

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['error' => 'empty message']);
    exit;
}

$lower = strtolower($message);

$stmt = $pdo->prepare("
    SELECT message FROM conversations
    WHERE user_id = ?
    ORDER BY timestamp DESC
    LIMIT 3
");
$stmt->execute([$user_id]);
$recentMessages = $stmt->fetchAll(PDO::FETCH_COLUMN);
$context = implode(" | ", array_reverse($recentMessages));

if (strpos($lower, 'hello') !== false || strpos($lower, 'hi') !== false) {
    $response = "Hey there! 👋 How can I help you today?";
} elseif (strpos($lower, 'how are you') !== false) {
    $response = "I'm just code, but I'm running smoothly! 😄";
} elseif (strpos($lower, 'help') !== false) {
    $response = "I'm here to chat! Ask me anything.";
} elseif (strpos($lower, 'bye') !== false) {
    $response = "Goodbye! Come back anytime. 👋";
} elseif (strpos($lower, 'what did i say') !== false) {
    $response = "Your last few messages were: " . $context;
} else {
    $response = "Hmm... I'm still learning. Try asking something else!";
}

$stmt = $pdo->prepare("
    INSERT INTO conversations (user_id, message, response)
    VALUES (?, ?, ?)
");
$stmt->execute([$user_id, $message, $response]);

echo json_encode(['reply' => $response]);