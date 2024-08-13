<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

$use_api = $_SESSION['use_api'] ?? false;
$api_key = $_SESSION['api_key'] ?? '';
$endpoint = $_SESSION['endpoint'] ?? '';
$model_name = $_SESSION['model_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style-welcome.css">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
</head>
<body class="dark-theme">
    <header class="header">
        <div class="logo">My Chatbot</div>
        <form action="logout.php" method="post">
            <input type="submit" value="Logout" class="logout-btn">
        </form>
    </header>

    <div class="container">
        <h1>Welcome to an AI Chatbot about ME!</h1>

        <div class="card-container">
            <div class="card" onclick="insertText('What are Seharish\'s favorite hobbies?')">What are Seharish's favorite hobbies?</div>
            <div class="card" onclick="insertText('What projects is Seharish currently working on?')">What projects is Seharish currently working on?</div>
            <div class="card" onclick="insertText('Tell me something interesting about Seharish.')">Tell me something interesting about Seharish.</div>
            <div class="card" onclick="insertText('What skills does Seharish have?')">What skills does Seharish have?</div>
        </div>

        <div id="chatOutput" class="chat-output"></div>

        <div class="chatbox">
            <textarea id="messageBox" placeholder="Send a message..."></textarea>
            <button class="send-btn" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        const useApi = <?php echo json_encode($use_api); ?>;
        const apiKey = <?php echo json_encode($api_key); ?>;
        const endpoint = <?php echo json_encode($endpoint); ?>;
        const modelName = <?php echo json_encode($model_name); ?>;

        function insertText(text) {
            document.getElementById('messageBox').value = text;
        }

        async function sendMessage() {
            const message = document.getElementById('messageBox').value;
            if (message.trim() === "") {
                alert("Please enter a message!");
                return;
            }

            const chatOutput = document.getElementById('chatOutput');
            chatOutput.innerHTML += `<div class='user-message'>${message}</div>`;

            const response = await fetch('get_response.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    message, 
                    useApi, 
                    apiKey, 
                    endpoint, 
                    modelName 
                })
            });

            const data = await response.text();
            chatOutput.innerHTML += `<div class='bot-message'>${data}</div>`;

            document.getElementById('messageBox').value = "";
        }
    </script>
</body>
</html>
