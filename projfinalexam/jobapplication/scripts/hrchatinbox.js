
          document.getElementById('sendMessage').addEventListener('click', (e) => {
        e.preventDefault();  // Prevent default form submission to handle it via AJAX or programmatically

        const messageInput = document.getElementById('messageInput');
        const messageText = messageInput.value.trim();
        const receiverUserID = document.querySelector('input[name="receiverUserID"]').value; // Get receiverUserID

        if (messageText) {
            // Create a new sender message in the chat window dynamically
            const messageElement = document.createElement('div');
            messageElement.classList.add('message', 'sender');
            messageElement.innerHTML = `
                <p>${messageText}</p>
                <span class="time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
            `;

            // Append to chat window
            const chatWindow = document.querySelector('.chat-window');
            chatWindow.appendChild(messageElement);

            // Scroll to the bottom of the chat
            chatWindow.scrollTop = chatWindow.scrollHeight;

            // Clear the input field
            messageInput.value = '';

            // Submit the form to send the message to the backend
            const form = document.querySelector('form');
            const formData = new FormData(form);
            formData.append('messageContent', messageText); // Add message content to FormData

            // Send the data using AJAX (fetch API or XMLHttpRequest)
            fetch('main/newhandleform.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json()) // Assuming the server returns JSON response
            .then(data => {
                if (data.success) {
                    console.log('Message sent successfully!');
                } else {
                    console.error('Failed to send message:', data.error);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
            });
        }
    });
   