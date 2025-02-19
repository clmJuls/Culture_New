<!DOCTYPE html>
<html>
<head>
    <style>
        /* Chat Container */
        .chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .chat-bubble {
            width: 65px;
            height: 65px;
            background: #365486;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            opacity: 1;
        }

        .chat-bubble.hidden {
            opacity: 0;
            transform: scale(0);
            pointer-events: none;
        }

        .chat-bubble:hover {
            transform: scale(1.1);
            background: #7FC7D9;
        }

        .chat-bubble i {
            color: white;
            font-size: 28px;
        }

        .chat-container {
            display: none;
            position: fixed;
            bottom: 0;
            right: 20px;
            width: 380px;
            height: 600px;
            border-radius: 15px 15px 0 0;
            overflow: hidden;
            flex-direction: column;
            background: white;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            z-index: 1000;
        }

        .chat-container.active {
            display: flex;
            opacity: 1;
            transform: translateY(0);
        }

        .chat-header {
            background: #365486;
            color: white;
            padding: 20px; /* Increased padding */
            text-align: center;
            font-weight: bold;
            font-size: 1.2em; /* Increased font size */
        }

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            padding-bottom: 80px; /* Increase bottom padding */
        }

        .message {
            margin: 8px;
            padding: 12px 16px;
            border-radius: 15px;
            max-width: 85%;
            word-wrap: break-word;
            font-size: 1.1em; /* Increased font size */
            line-height: 1.4;
        }

        .bot-message {
            background: #e9ecef;
            align-self: flex-start;
        }

        .user-message {
            background: #365486;
            color: white;
            align-self: flex-end;
        }

        /* Animation for chat bubble */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Add these new styles */
        .chat-input-container {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            background: white;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 10px;
            z-index: 1001;
        }

        .chat-input {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            outline: none;
            font-size: 1em;
        }

        .chat-input:focus {
            border-color: #365486;
        }

        .send-button {
            background: #365486;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .send-button:hover {
            background: #7FC7D9;
            transform: scale(1.1);
        }

        /* Add new typing animation styles */
        .typing-indicator {
            display: inline-block;
        }

        .typing-indicator span {
            display: inline-block;
            opacity: 0.4;
            animation: typingAnimation 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingAnimation {
            0% { opacity: 0.4; transform: translateY(0); }
            50% { opacity: 1; transform: translateY(-4px); }
            100% { opacity: 0.4; transform: translateY(0); }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="chat-widget">
        <div class="chat-bubble pulse" id="chatBubble">
            <i class="fas fa-comments"></i>
        </div>
        <div class="chat-container" id="chatContainer">
            <div class="chat-header">
                Kulturifiko Assistant
            </div>
            <div class="chat-messages" id="chatMessages">
                <!-- Messages will appear here -->
            </div>
            <!-- Add this new input container -->
            <div class="chat-input-container">
                <input type="text" class="chat-input" id="chatInput" placeholder="Type your message...">
                <button class="send-button" id="sendButton">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Replace the static chatData with a function to call Ollama
        async function getMistralResponse(userMessage) {
            try {
                const response = await fetch('components/widgets/api/chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: userMessage })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.message || 'Unknown error occurred');
                }
                
                return data.response || "I apologize, but I received an invalid response from the AI service.";
            } catch (error) {
                console.error('Error:', error);
                return "I apologize, but I'm having trouble connecting to the AI service right now.";
            }
        }

        // Modify the click handler to remove options
        $('#chatBubble').click(async function() {
            const container = $('#chatContainer');
            const bubble = $('#chatBubble');
            
            container.toggleClass('active');
            bubble.toggleClass('hidden');
            
            if (!container.data('initialized')) {
                addMessage("Hello! How can I help you learn about different cultures today?");
                container.data('initialized', true);
            }
        });

        async function handleUserInput(userMessage) {
            addMessage(userMessage, false);
            
            // Show typing indicator with animated dots
            const typingDiv = $('<div class="message bot-message"><div class="typing-indicator">Thinking<span>.</span><span>.</span><span>.</span></div></div>');
            $('#chatMessages').append(typingDiv);
            
            // Get response from Mistral
            const response = await getMistralResponse(userMessage);
            
            // Remove typing indicator and add response
            typingDiv.remove();
            addMessage(response);
        }

        // Close chat when clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.chat-widget').length) {
                $('#chatContainer').removeClass('active');
                $('#chatBubble').removeClass('hidden'); // Show chat bubble when closing
            }
        });

        // Add these new event listeners
        $('#chatInput').keypress(function(e) {
            if (e.which == 13) { // Enter key
                sendMessage();
            }
        });

        $('#sendButton').click(sendMessage);

        function sendMessage() {
            const input = $('#chatInput');
            const message = input.val().trim();
            
            if (message) {
                handleUserInput(message);
                input.val(''); // Clear input after sending
            }
        }

        function addMessage(message, isBot = true) {
            const messageDiv = $('<div></div>')
                .addClass('message')
                .addClass(isBot ? 'bot-message' : 'user-message')
                .text(message);
            
            $('#chatMessages').append(messageDiv);
            
            // Scroll to the bottom of the messages
            const messagesDiv = $('#chatMessages');
            messagesDiv.scrollTop(messagesDiv[0].scrollHeight);
        }
    </script>
</body>
</html>
