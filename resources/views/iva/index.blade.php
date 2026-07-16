<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IVA - Asisten Virtual Notaris</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex flex-col">

    <header class="bg-red-700 text-white p-4 shadow-md flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight">IVA - Asisten Virtual</h1>
            <p class="text-xs text-red-100 uppercase tracking-widest">Kantor Notaris & PPAT</p>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-4 w-full max-w-3xl mx-auto space-y-4" id="chat-box">
        <div class="flex mb-4">
            <div class="bg-white rounded-2xl shadow-sm p-4 max-w-[80%] border-l-4 border-red-600">
                <p class="text-gray-800 leading-relaxed">
                    Halo! Saya IVA, asisten virtual Anda. Ada yang bisa saya bantu terkait layanan akta, balik nama, atau persyaratan dokumen lainnya?
                </p>
            </div>
        </div>
    </main>

    <div id="typing-indicator" class="hidden w-full max-w-3xl mx-auto px-4 pb-2">
        <p class="text-xs text-red-500 italic font-medium">IVA sedang mengetik...</p>
    </div>

    <footer class="bg-white border-t p-4">
        <div class="max-w-3xl mx-auto flex gap-2">
            <input type="text" id="user-input" 
                class="flex-1 border border-gray-200 rounded-full px-5 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-inner" 
                placeholder="Ketik pesan Anda di sini..." 
                autocomplete="off">
            <button id="send-btn" 
                class="bg-red-700 text-white rounded-full px-6 py-2.5 font-semibold hover:bg-red-800 transition duration-200 shadow-lg">
                Kirim
            </button>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.getElementById('chat-box');
            const userInput = document.getElementById('user-input');
            const sendBtn = document.getElementById('send-btn');
            const typingIndicator = document.getElementById('typing-indicator');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // 1. INISIALISASI MEMORI PERCAKAPAN
            let conversationHistory = [];

            async function sendMessage() {
                const message = userInput.value.trim();
                if (!message) return;

                appendMessage('user', message);
                
                // 2. SIMPAN PESAN USER KE DALAM MEMORI
                conversationHistory.push({
                    "role": "user",
                    "parts": [{ "text": message }]
                });

                userInput.value = '';
                typingIndicator.classList.remove('hidden');
                scrollToBottom();

                try {
                    const response = await fetch("{{ route('iva.sendMessage') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        // 3. KIRIM MEMORI (HISTORY) KE SERVER LARAVEL BERSAMAAN DENGAN PESAN
                        body: JSON.stringify({ 
                            message: message,
                            history: conversationHistory
                        })
                    });

                    const data = await response.json();
                    typingIndicator.classList.add('hidden');
                    
                    if (data.status === 'success' || data.status === 'fallback') {
                        // Merapikan format teks untuk ditampilkan di layar (Tebal, Miring, Baris Baru)
                        let formattedReply = data.reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                        formattedReply = formattedReply.replace(/\*(.*?)\*/g, '<em>$1</em>');
                        formattedReply = formattedReply.replace(/\n/g, '<br>');
                        
                        appendMessage('bot', formattedReply, data.status);

                        // 4. SIMPAN BALASAN BOT KE DALAM MEMORI (Menggunakan teks asli tanpa tag HTML)
                        conversationHistory.push({
                            "role": "model",
                            "parts": [{ "text": data.reply }]
                        });
                    }
                } catch (error) {
                    typingIndicator.classList.add('hidden');
                    appendMessage('bot', 'Maaf, terjadi kesalahan pada jaringan atau server.');
                    console.error("Error API:", error);
                }
            }

            function appendMessage(sender, text, status = 'success') {
                const wrapperDiv = document.createElement('div');
                wrapperDiv.className = `flex mb-4 ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

                let bubbleStyle = sender === 'user' 
                    ? 'bg-red-100 text-red-900 rounded-2xl rounded-br-none' 
                    : 'bg-white text-gray-800 rounded-2xl rounded-bl-none border-l-4 border-red-600 shadow-sm';
                
                if (status === 'fallback') {
                    bubbleStyle = 'bg-orange-50 text-orange-900 border-l-4 border-orange-500 rounded-2xl rounded-bl-none shadow-sm';
                }

                wrapperDiv.innerHTML = `
                    <div class="p-4 max-w-[80%] ${bubbleStyle}">
                        <p class="text-sm leading-relaxed">${text}</p>
                    </div>
                `;

                chatBox.appendChild(wrapperDiv);
                scrollToBottom();
            }

            function scrollToBottom() {
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            sendBtn.addEventListener('click', sendMessage);
            userInput.addEventListener('keypress', (e) => { 
                if (e.key === 'Enter') sendMessage(); 
            });
        });
    </script>
</body>
</html>