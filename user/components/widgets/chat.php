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

        .options-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 15px;
            background: #f8f9fa;
        }

        .option-button {
            background: #365486;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1em; /* Increased font size */
        }

        .option-button:hover {
            background: #7FC7D9;
            transform: translateY(-2px);
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
            <div class="options-container" id="optionsContainer">
                <!-- Options will appear here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const chatData = {
            start: {
                message: "Hello! How can I help you today?",
                options: [
                    { text: "What is Kulturifiko?", next: "about" },
                    { text: "How do I use this platform?", next: "usage" },
                    { text: "I need help with my account", next: "account" }
                ]
            },
            about: {
                message: "Kulturifiko is a platform for cultural exchange and learning. Would you like to know more about:",
                options: [
                    { text: "Our Mission", next: "mission" },
                    { text: "Features", next: "features" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            usage: {
                message: "Here's how you can use Kulturifiko. What would you like to learn about?",
                options: [
                    { text: "Creating Posts", next: "posting" },
                    { text: "Interacting with Others", next: "interaction" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            account: {
                message: "What kind of account help do you need?",
                options: [
                    { text: "Login Issues", next: "login" },
                    { text: "Profile Settings", next: "profile" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            mission: {
                message: "Our mission is to connect people through cultural exchange and foster global understanding.",
                options: [
                    { text: "Learn More About Us", next: "about" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            features: {
                message: "Kulturifiko offers cultural posts, interactive learning, and community engagement features.",
                options: [
                    { text: "How to Use Features", next: "usage" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            posting: {
                message: "To create a post, click the '+ Create' button in the navigation bar and follow the prompts.",
                options: [
                    { text: "More Usage Tips", next: "usage" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            interaction: {
                message: "You can interact by liking posts, commenting, and following other users.",
                options: [
                    { text: "More Usage Tips", next: "usage" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            login: {
                message: "For login issues, try resetting your password or contact support at support@kulturifiko.com",
                options: [
                    { text: "Other Account Help", next: "account" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            },
            profile: {
                message: "You can edit your profile settings by clicking on 'Profile' in the menu dropdown.",
                options: [
                    { text: "Other Account Help", next: "account" },
                    { text: "Back to Main Menu", next: "start" }
                ]
            }
        };

        // Toggle chat window
        $('#chatBubble').click(function() {
            const container = $('#chatContainer');
            const bubble = $('#chatBubble');
            
            container.toggleClass('active');
            bubble.toggleClass('hidden'); // Hide/show chat bubble
            
            // Initialize chat if it hasn't been already
            if (!container.data('initialized')) {
                addMessage(chatData.start.message);
                showOptions(chatData.start.options);
                container.data('initialized', true);
            }
        });

        function addMessage(message, isBot = true) {
            const messageDiv = $('<div class="message"></div>')
                .addClass(isBot ? 'bot-message' : 'user-message')
                .text(message);
            $('#chatMessages').append(messageDiv);
            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
        }

        function showOptions(options) {
            const container = $('#optionsContainer');
            container.empty();
            options.forEach(option => {
                $('<button></button>')
                    .addClass('option-button')
                    .text(option.text)
                    .click(() => handleOption(option))
                    .appendTo(container);
            });
        }

        function handleOption(option) {
            addMessage(option.text, false);
            const nextState = chatData[option.next];
            setTimeout(() => {
                addMessage(nextState.message);
                showOptions(nextState.options);
            }, 500);
        }

        // Close chat when clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.chat-widget').length) {
                $('#chatContainer').removeClass('active');
                $('#chatBubble').removeClass('hidden'); // Show chat bubble when closing
            }
        });
    </script>
</body>
</html>
