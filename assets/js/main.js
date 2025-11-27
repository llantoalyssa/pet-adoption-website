// ===== Smooth Scrolling for Navigation =====
document.querySelectorAll('header nav ul li a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        const targetSection = document.getElementById(targetId);
        if(targetSection) {
            window.scrollTo({
                top: targetSection.offsetTop - 70, // adjust for sticky header
                behavior: 'smooth'
            });
        }
    });
});

// ===== Adopt Me Button Alert =====
const adoptButtons = document.querySelectorAll('.adopt-btn');

adoptButtons.forEach(button => {
    button.addEventListener('click', () => {
        alert("Thank you for your interest! Please contact us via the form to complete the adoption process.");
    });
});

// ===== Placeholder for Chat Plugin =====
const chatPluginContainer = document.getElementById('chat-plugin');

if(chatPluginContainer) {
    // Create chat icon
    const chatIcon = document.createElement('div');
    chatIcon.classList.add('chat-icon');
    chatIcon.innerHTML = "💬"; // chat bubble emoji
    chatPluginContainer.appendChild(chatIcon);

    // Create chat box
    const chatBox = document.createElement('div');
    chatBox.classList.add('chat-box');
    chatBox.innerHTML = `
        <header>Chat with us!</header>
        <div class="messages">
            <p>Hi! How can we help you today?</p>
        </div>
        <div class="chat-input-container">
            <input type="text" placeholder="Type a message..." />
            <button class="send-btn">Send</button>
        </div>
    `;
    chatPluginContainer.appendChild(chatBox);

    const messagesDiv = chatBox.querySelector('.messages');
    const inputField = chatBox.querySelector('input');
    const sendBtn = chatBox.querySelector('.send-btn');

    // Toggle chat box on icon click
    chatIcon.addEventListener('click', () => {
        chatBox.style.display = chatBox.style.display === 'flex' ? 'none' : 'flex';
        if(chatBox.style.display === 'flex') {
            inputField.focus();
        }
    });

    // Function to add a new message
    function addMessage(text, isUser = true) {
        const msg = document.createElement('p');
        msg.textContent = text;
        if(isUser) {
            msg.style.backgroundColor = '#ff6b6b';
            msg.style.color = '#fff';
            msg.style.alignSelf = 'flex-end';
        }
        messagesDiv.appendChild(msg);
        messagesDiv.scrollTop = messagesDiv.scrollHeight; // auto-scroll
    }

    // Send button click
    sendBtn.addEventListener('click', () => {
        const text = inputField.value.trim();
        if(text) {
            addMessage(text, true);
            inputField.value = '';

            // Auto-response placeholder
            setTimeout(() => {
                addMessage("Thanks for your message! We'll get back to you soon.", false);
            }, 1000);
        }
    });

    // Press Enter key to send
    inputField.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') {
            sendBtn.click();
        }
    });
}
