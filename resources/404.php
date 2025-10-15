<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - SYSTEM HACKED</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(45deg, #0a0e27, #1a1a3e, #0a0e27);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #00ff00;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            text-align: center;
            position: relative;
            z-index: 10;
            max-width: 600px;
            padding: 40px;
        }

        .glitch-text {
            font-size: 120px;
            font-weight: bold;
            margin-bottom: 20px;
            position: relative;
            animation: glitch 0.3s infinite;
        }

        @keyframes glitch {
            0% {
                text-shadow: 
                    -2px 0 #ff00ff,
                    2px 0 #00ffff,
                    0 0 20px rgba(0, 255, 0, 0.8);
            }
            50% {
                text-shadow: 
                    2px 0 #ff00ff,
                    -2px 0 #00ffff,
                    0 0 20px rgba(0, 255, 0, 0.8);
            }
            100% {
                text-shadow: 
                    -2px 0 #ff00ff,
                    2px 0 #00ffff,
                    0 0 20px rgba(0, 255, 0, 0.8);
            }
        }

        .scanlines {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 0, 0, 0.15),
                rgba(0, 0, 0, 0.15) 2px,
                transparent 2px,
                transparent 4px
            );
            pointer-events: none;
            z-index: 1;
            animation: flicker 0.15s infinite;
        }

        @keyframes flicker {
            0% { opacity: 0.95; }
            50% { opacity: 1; }
            100% { opacity: 0.95; }
        }

        .matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .matrix-char {
            position: absolute;
            color: rgba(0, 255, 0, 0.1);
            font-size: 20px;
            font-family: 'Courier New', monospace;
            animation: matrixFall linear infinite;
        }

        @keyframes matrixFall {
            0% {
                transform: translateY(-100%);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh);
                opacity: 0;
            }
        }

        h1 {
            font-size: 48px;
            margin: 20px 0;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { text-shadow: 0 0 10px rgba(0, 255, 0, 0.5); }
            50% { text-shadow: 0 0 30px rgba(0, 255, 0, 1); }
        }

        .warning {
            background: rgba(255, 0, 0, 0.1);
            border: 2px solid #ff0000;
            padding: 20px;
            margin: 20px 0;
            animation: borderFlash 1s infinite;
        }

        @keyframes borderFlash {
            0%, 100% { border-color: #ff0000; }
            50% { border-color: #ff00ff; }
        }

        .message {
            font-size: 18px;
            line-height: 1.8;
            margin: 20px 0;
            animation: typewriter 4s steps(40, end) 1s 1 normal both;
        }

        @keyframes typewriter {
            0% { width: 0; }
            100% { width: 100%; }
        }

        .button-group {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        button {
            padding: 15px 30px;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            background: transparent;
            border: 2px solid #00ff00;
            color: #00ff00;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        button:hover {
            background: #00ff00;
            color: #000;
            text-shadow: none;
            box-shadow: 0 0 20px #00ff00;
        }

        button:active {
            transform: scale(0.95);
        }

        .easter-egg {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 12px;
            opacity: 0.3;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .easter-egg:hover {
            opacity: 1;
            animation: bounce 0.5s;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .code-block {
            background: rgba(0, 0, 0, 0.5);
            border-left: 4px solid #00ff00;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            font-size: 12px;
            overflow-x: auto;
            animation: codeGlow 3s ease-in-out infinite;
        }

        @keyframes codeGlow {
            0%, 100% { box-shadow: inset 0 0 10px rgba(0, 255, 0, 0.1); }
            50% { box-shadow: inset 0 0 20px rgba(0, 255, 0, 0.3); }
        }
    </style>
</head>
<body>
    <div class="matrix-bg" id="matrixBg"></div>
    <div class="scanlines"></div>
    
    <div class="container">
        <div class="glitch-text">404</div>
        <h1>⚠️ SYSTEM HACKED ⚠️</h1>
        
        <div class="warning">
            ⛔ CRITICAL ERROR - PAGE NOT FOUND ⛔
        </div>

        <div class="message">
            Looks like someone deleted this page! Or maybe it never existed? Either way, the digital ninjas couldn't find it.
        </div>

        <div class="code-block">
            > accessing_page.exe<br>
            > ERROR 404: File not found<br>
            > location: /lost/in/the/void<br>
            > attempt: FAILED ✗
        </div>

        <div class="button-group">
            <button onclick="window.location.href='/'">🏠 Return Home</button>
            <button onclick="window.history.back()">🔙 Go Back</button>
            <button onclick="location.reload()">🔄 Retry</button>
        </div>

        <div class="easter-egg" onclick="hackTheMainframe()">
            🕵️ click here?
        </div>
    </div>

    <script>
        // Generate matrix characters falling in background
        function createMatrixChar() {
            const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
            const char = chars[Math.floor(Math.random() * chars.length)];
            const left = Math.random() * 100;
            const duration = 5 + Math.random() * 10;
            
            const el = document.createElement('div');
            el.className = 'matrix-char';
            el.textContent = char;
            el.style.left = left + '%';
            el.style.animationDuration = duration + 's';
            el.style.animationDelay = Math.random() * 2 + 's';
            
            document.getElementById('matrixBg').appendChild(el);
            
            setTimeout(() => el.remove(), duration * 1000 + 2000);
        }

        // Continuously create matrix characters
        setInterval(createMatrixChar, 300);

        // Easter egg
        function hackTheMainframe() {
            alert('🎯 SYSTEM COMPROMISED 🎯\n\nYou\'ve found the secret!\n\n[HACKING_INTENSIFIES]\n\nJK, just a 404 page. Go home! 🏠');
        }
    </script>
</body>
</html>