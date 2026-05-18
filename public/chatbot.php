<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html><head><title>AI Research Assistant</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
    <h2>🤖 AI Research Assistant</h2>
    <div id="chatbox" class="border rounded p-3 mb-3" style="height: 400px; overflow-y: auto; background: #f8f9fa;">
        <div class="alert alert-secondary">Hello! I'm your AI assistant. Ask me anything about research, papers, or academic writing.</div>
    </div>
    <div class="input-group"><input type="text" id="userInput" class="form-control" placeholder="Type your question...">
        <button class="btn btn-primary" onclick="sendMessage()">Send</button>
    </div>
</div>
<script>
    async function sendMessage() {
        let input = document.getElementById('userInput');
        let msg = input.value.trim();
        if (!msg) return;
        let chatbox = document.getElementById('chatbox');
        chatbox.innerHTML += `<div class="alert alert-primary">You: ${escapeHtml(msg)}</div>`;
        input.value = '';
        let response = await fetch('chat-api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({message: msg})
        });
        let data = await response.json();
        chatbox.innerHTML += `<div class="alert alert-secondary">🤖 AI: ${escapeHtml(data.reply)}</div>`;
        chatbox.scrollTop = chatbox.scrollHeight;
    }
    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    document.getElementById('userInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>