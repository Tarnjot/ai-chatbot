<?php
session_start();
require_once 'includes/db.php';

$userId = 1;

$stmt = $pdo->prepare("SELECT * FROM conversations WHERE user_id = ? ORDER BY timestamp ASC");
$stmt->execute([$userId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>AI Chatbot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-box {
            height: 400px;
            overflow-y: scroll;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .message.user { text-align: right; color: #0d6efd; }
        .message.bot { text-align: left; color: #198754; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="mb-4 text-center">🤖 AI Chatbot</h3>

    <div class="chat-box mb-3" id="chatBox">
        <?php foreach ($messages as $msg): ?>
            <div class="message user"><strong>You:</strong> <?= htmlspecialchars($msg['message']) ?></div>
            <div class="message bot"><strong>Bot:</strong> <?= htmlspecialchars($msg['response']) ?></div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="process.php" class="d-flex">
        <input type="text" name="message" class="form-control me-2" placeholder="Type your message..." required autofocus>
        <button class="btn btn-primary">Send</button>
    </form>
</div>

</body>
</html>