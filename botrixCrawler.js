const crawlChat = () => {
    const chatEntries = document.querySelectorAll('[data-chat-entry]');

    chatEntries.forEach((entry) => {
        const messageId = entry.getAttribute('data-chat-entry');

        if (!localStorage.getItem(messageId)) {
            const usernameElement = entry.querySelector('.chat-entry-username');
            const messageElement = entry.querySelector('.chat-entry-content');

            if (usernameElement && messageElement) {
                const username = usernameElement.textContent;
                const message = messageElement.textContent;

                console.log(`Username: ${username}`);
                console.log(`Message: ${message}`);

                const formData = new FormData();
                formData.append('message', message.replace(/(!|@)herika/i, ''));
                formData.append('user', username);
                formData.append('platform', 'Kick');

                fetch('http://localhost:8080/saig-gwserver/connect.php', {
                    method: 'POST',
                    body: formData,
                })
                    .then((response) => {
                        if (response.ok) {
                            console.log('HTTP request successful');
                        } else {
                            console.error('HTTP request failed');
                        }
                    })
                    .catch((error) => {
                        console.error('Error making HTTP request:', error);
                    });

                localStorage.setItem(messageId, true);
            }
        }
    });
};

crawlChat();
setInterval(crawlChat, 1000);