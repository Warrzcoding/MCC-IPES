<style>
    :root {
        --chatbot-gradient: linear-gradient(135deg, #8e2de2 0%, #ff0099 100%);
        --chatbot-violet: #8e2de2;
        --chatbot-pink: #ff0099;
        --chatbot-purple: #4a00e0;
    }

    #chatbot-container {
        position: fixed;
        bottom: 30px;
        right: 20px;
        z-index: 1050;
        font-family: 'Inter', sans-serif;
    }

    @media (max-width: 768px) {
        #chatbot-container {
            bottom: 80px; /* Move higher on mobile to avoid footer */
        }
    }

    #chatbot-button {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--chatbot-gradient);
        box-shadow: 0 4px 15px rgba(255, 0, 153, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    #chatbot-button:hover, #chatbot-button:focus {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(255, 0, 153, 0.4);
        animation: shake 0.5s ease-in-out infinite;
    }

    @keyframes shake {
        0%, 100% { transform: scale(1.1) rotate(0deg); }
        25% { transform: scale(1.1) rotate(-5deg); }
        75% { transform: scale(1.1) rotate(5deg); }
    }

    #chatbot-window {
        position: absolute;
        bottom: 65px;
        right: 0;
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(255, 0, 153, 0.1);
    }

    #chatbot-window.active {
        display: flex;
        animation: slideUp 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .chatbot-header {
        background: var(--chatbot-gradient);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* FAQ / Home Screen */
    #chatbot-home {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
        background: #fdfbff;
        overflow-y: auto;
    }

    .faq-title {
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .faq-item {
        background: white;
        padding: 12px 15px;
        border-radius: 12px;
        margin-bottom: 10px;
        border: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #555;
    }

    .faq-item:hover {
        border-color: var(--chatbot-pink);
        transform: translateX(5px);
        color: var(--chatbot-violet);
    }

    .open-chat-btn {
        margin-top: auto;
        background: var(--chatbot-gradient);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: transform 0.2s;
        box-shadow: 0 4px 15px rgba(255, 0, 153, 0.2);
    }

    .open-chat-btn:hover {
        transform: translateY(-2px);
    }

    /* Chat / Inbox Screen */
    #chatbot-chat {
        flex: 1;
        display: none;
        flex-direction: column;
        overflow: hidden;
    }

    .chatbot-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #fdfbff;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chatbot-input-area {
        padding: 12px 15px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        gap: 10px;
        align-items: center;
        background: white;
    }

    #chatbot-input {
        flex: 1;
        border-radius: 20px;
        padding: 8px 15px;
        border: 1px solid #e0e0e0;
        outline: none;
        font-size: 13px;
    }

    #send-chatbot {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: var(--chatbot-gradient);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .message {
        padding: 8px 12px;
        border-radius: 15px;
        max-width: 85%;
        font-size: 13px;
        line-height: 1.4;
    }

    .message.bot {
        background: #f0f2f5;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }

    .message.user {
        background: var(--chatbot-gradient);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 10px 15px;
        background: #f0f2f5;
        border-radius: 15px;
        align-self: flex-start;
        margin-bottom: 5px;
        animation: botShake 0.5s ease-in-out infinite;
    }

    @keyframes botShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-2px); }
        75% { transform: translateX(2px); }
    }

    .typing-dot {
        width: 5px;
        height: 5px;
        background: #90949c;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-4px); }
    }
</style>

<div id="chatbot-container">
    <div id="chatbot-window">
        <div class="chatbot-header">
            <div class="d-flex align-items-center">
                <button id="back-to-home" class="btn btn-link text-white p-0 me-2" style="display: none;">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <i class="fas fa-robot me-2"></i>
                <span class="fw-bold">MCC-IPES Assistant</span>
            </div>
            <button type="button" class="btn-close btn-close-white" id="close-chatbot"></button>
        </div>

        <!-- Home Screen with FAQs -->
        <div id="chatbot-home">
            <div class="faq-title">Frequently Asked Questions</div>
            <div class="faq-item" data-question="How to evaluate an instructor?">
                How to evaluate an instructor? <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            <div class="faq-item" data-question="I forgot my password">
                I forgot my password <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            <div class="faq-item" data-question="When is the evaluation deadline?">
                When is the evaluation deadline? <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            <div class="faq-item" data-question="How to see my ratings?">
                How to see my ratings? <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            
            <button class="open-chat-btn" id="open-inbox">
                <i class="fas fa-paper-plane"></i> Ask to open inbox
            </button>
        </div>

        <!-- Direct Chat Screen -->
        <div id="chatbot-chat">
            <div class="chatbot-messages" id="chatbot-messages">
                <div class="message bot">
                    Hello! How can I help you today?
                </div>
            </div>
            <div class="chatbot-input-area">
                <input type="text" id="chatbot-input" placeholder="Type a message...">
                <button id="send-chatbot">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="chatbot-button">
        <i class="fas fa-comments"></i>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botBtn = document.getElementById('chatbot-button');
        const botWindow = document.getElementById('chatbot-window');
        const closeBtn = document.getElementById('close-chatbot');
        const homeScreen = document.getElementById('chatbot-home');
        const chatScreen = document.getElementById('chatbot-chat');
        const openInboxBtn = document.getElementById('open-inbox');
        const backBtn = document.getElementById('back-to-home');
        const sendBtn = document.getElementById('send-chatbot');
        const input = document.getElementById('chatbot-input');
        const messagesContainer = document.getElementById('chatbot-messages');
        const faqItems = document.querySelectorAll('.faq-item');

        botBtn.addEventListener('click', () => {
            botWindow.classList.toggle('active');
        });

        closeBtn.addEventListener('click', () => {
            botWindow.classList.remove('active');
        });

        openInboxBtn.addEventListener('click', () => {
            homeScreen.style.display = 'none';
            chatScreen.style.display = 'flex';
            backBtn.style.display = 'block';
            input.focus();
        });

        backBtn.addEventListener('click', () => {
            chatScreen.style.display = 'none';
            homeScreen.style.display = 'flex';
            backBtn.style.display = 'none';
        });

        faqItems.forEach(item => {
            item.addEventListener('click', () => {
                const question = item.getAttribute('data-question');
                homeScreen.style.display = 'none';
                chatScreen.style.display = 'flex';
                backBtn.style.display = 'block';
                addMessage(question, 'user');
                
                const typing = showTyping();
                setTimeout(() => {
                    typing.remove();
                    addMessage("This is an automated response to: " + question + ". Please contact support for more details.", 'bot');
                }, 1500);
            });
        });

        function addMessage(text, type) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `message ${type}`;
            msgDiv.textContent = text;
            messagesContainer.appendChild(msgDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function showTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
            messagesContainer.appendChild(typingDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            return typingDiv;
        }

        function handleSend() {
            const text = input.value.trim();
            if (text) {
                addMessage(text, 'user');
                input.value = '';
                const typing = showTyping();
                setTimeout(() => {
                    typing.remove();
                    addMessage("I've received your message. Our team will get back to you soon.", 'bot');
                }, 2000);
            }
        }

        sendBtn.addEventListener('click', handleSend);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') handleSend();
        });
    });
</script>
