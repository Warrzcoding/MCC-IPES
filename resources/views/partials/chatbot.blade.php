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
        padding: 10px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: transform 0.2s;
        box-shadow: 0 4px 15px rgba(255, 0, 153, 0.2);
        font-size: 13px;
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .social-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: transform 0.2s;
        font-size: 16px;
    }

    .social-icon:hover {
        transform: scale(1.1);
        color: white;
    }

    .social-icon.facebook { background: #1877F2; }
    .social-icon.github { background: #333; }
    .social-icon.custom { background: #667eea; overflow: hidden; }
    .social-icon.custom img { width: 100%; height: 100%; object-fit: cover; }

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
            <div class="faq-item" data-question="evaluate instructors">
                How to evaluate an instructor? <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            <div class="faq-item" data-question="create account">
                How to create account? <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            <div class="faq-item" data-question="forgot password">
                How to reset password? <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            <div class="faq-item" data-question="enable location">
                How to enable or open location permission? <i class="fas fa-chevron-right fa-xs"></i>
            </div>
            
            <button class="open-chat-btn" id="open-inbox">
                <i class="fas fa-paper-plane"></i> Chat Now
            </button>

            <!-- Social Links / Additional Websites -->
            <div class="social-links">
                <a href="https://www.facebook.com/profile.php?id=61584272574390" target="_blank" class="social-icon facebook" title="MCCIPES Concern">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://github.com/your-repo" target="_blank" class="social-icon github" title="WarrCODING">
                    <i class="fab fa-github"></i>
                </a>
                <!-- Custom Website Links (Replace images and hrefs) -->
                <a href="https://madridejoscommunitycollege.com" target="_blank" class="social-icon custom" title="MCC-Enrollment System">
                    <img src="{{ asset('images/logo.png') }}" alt="S1">
                </a>
                <a href="https://mccgradeinfo.com" target="_blank" class="social-icon custom" title="MCC-Grading System">
                    <img src="{{ asset('images/logo.png') }}" alt="S2">
                </a>
                <a href="https://mcc-clearance.com" target="_blank" class="social-icon custom" title="Mcc-Clearance System">
                    <img src="{{ asset('images/logo.png') }}" alt="S3">
                </a>
                <a href="https://mcc-lrc.com" target="_blank" class="social-icon custom" title="MCC-Library">
                    <img src="{{ asset('images/mcc-lrc.png') }}" alt="S4">
                </a>
            </div>
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
                const questionText = item.textContent.trim();
                homeScreen.style.display = 'none';
                chatScreen.style.display = 'flex';
                backBtn.style.display = 'block';
                addMessage(questionText, 'user');
                
                const typing = showTyping();
                
                // Custom Local Answers for FAQs
                let customAnswer = null;
                if (question === 'evaluate instructors') {
                    customAnswer = `To evaluate your instructors, please follow these steps:<br>1. Log in to your account.<br>2. Go to the "Evaluation" section.<br>3. Select the instructor you want to evaluate.<br>4. Fill out the evaluation form and submit.<br><br><img src="{{ asset('images/evaluate-guide.png') }}" alt="Evaluation Guide" style="width: 100%; border-radius: 8px; margin-top: 10px;">`;
                } else if (question === 'create account') {
                    customAnswer = `To create an account, follow these steps:
                    <br><br><b>Step 1:</b> Tap "Start Student Login", Click "Signup here" link.
                    <br><img src="{{ asset('images/signup-step1.png') }}" style="width: 100%; border-radius: 8px; margin-top: 5px;">
                    <br><br><b>Step 2:</b> Enter your ID number to check eligibility. (Enrolled students are provided with an ID). Tap "Accept Terms and Conditions", then click "CHECK ID". 
                    <br><i>Note: If your ID exists, click "THIS IS ME" to proceed to Step 3. If not, contact the IPES Team via our Facebook page (see links in FAQ).</i>
                    <br><img src="{{ asset('images/signup-step2.png') }}" style="width: 100%; border-radius: 8px; margin-top: 5px;">
                    <br><br><b>Step 3:</b> Enter your <b>sample@mcclawis.edu.ph</b> email address (MS 365 account) and click "SEND VERIFICATION".
                    <br><img src="{{ asset('images/signup-step3.png') }}" style="width: 100%; border-radius: 8px; margin-top: 5px;">
                    <br><br><b>Step 4:</b> Open your Outlook app where your MS account is logged in. Copy the verification code sent to you, then go back to the OTP verification page and "VERIFY OTP".
                    <br><img src="{{ asset('images/signup-step4.png') }}" style="width: 100%; border-radius: 8px; margin-top: 5px;">
                    <br><img src="{{ asset('images/signup-step5.png') }}" style="width: 100%; border-radius: 8px; margin-top: 5px;">
                    <br><br><b>Step 5:</b> You will be redirected to the Signup form. Fill out the details and create a strong password, then click "Submit".
                     <br><img src="{{ asset('images/signup-step6.png') }}" style="width: 100%; border-radius: 8px; margin-top: 5px;">
                    <br><br><b>Step 6:</b> Wait for administrator approval before you can log in.`;
                } else if (question === 'forgot password') {
                    customAnswer = "If you've forgotten your password, click on the 'Forgot Password?' link on the login page. Enter your registered email address, and we'll send you instructions on how to reset it.";
                } else if (question === 'enable location') {
                    customAnswer = "To enable location services, look for the lock icon in your browser's address bar. Click it and ensure 'Location' is set to 'Allow'. This is required for attendance and other location-based features.";
                }

                if (customAnswer) {
                    setTimeout(() => {
                        typing.remove();
                        addMessage(customAnswer, 'bot');
                    }, 800);
                    return;
                }

                // Call Backend for FAQ
                fetch('{{ route("chatbot.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: question })
                })
                .then(response => response.json())
                .then(data => {
                    typing.remove();
                    addMessage(data.reply, 'bot');
                })
                .catch(error => {
                    typing.remove();
                    addMessage("Sorry, I'm having trouble connecting to the server.", 'bot');
                });
            });
        });

        function addMessage(text, type) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `message ${type}`;
            msgDiv.innerHTML = text;
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

                // Call Backend API
                fetch('{{ route("chatbot.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                })
                .then(response => response.json())
                .then(data => {
                    typing.remove();
                    addMessage(data.reply, 'bot');
                })
                .catch(error => {
                    typing.remove();
                    addMessage("Sorry, I'm having trouble connecting to the server.", 'bot');
                });
            }
        }

        sendBtn.addEventListener('click', handleSend);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') handleSend();
        });
    });
</script>
