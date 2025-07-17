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



<div class="card shadow-sm">
  <div class="card-body">
        <form id="chatForm" class="d-flex mb-3">
            <input type="text" id="messageInput" class="form-control me-2" placeholder="Type your message..." required autofocus>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>

        <script>
            document.getElementById('chatForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const input = document.getElementById('messageInput');
                const message = input.value.trim();
                if (!message) return;

                const chatBox = document.getElementById('chatBox');
                chatBox.innerHTML += `<div class="message user"><strong>You:</strong> ${message}</div>`;


                const res = await fetch('process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message }) 
                });
                const data = await res.json();

                chatBox.innerHTML += `<div class="message bot"><strong>Bot:</strong> ${data.reply}</div>`;


                input.value = '';
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        </script>
  </div>
</div>

</body>
</html>