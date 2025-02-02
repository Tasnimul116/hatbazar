function openMessageForm(agentId) {
    document.getElementById('message-form').style.display = 'block';
    document.getElementById('message-form').dataset.agentId = agentId;
}

function closeMessageForm() {
    document.getElementById('message-form').style.display = 'none';
}

function sendMessage() {
    const agentId = document.getElementById('message-form').dataset.agentId;
    const messageContent = document.getElementById('message-content').value;

    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('agentId', agentId);
    formData.append('message', messageContent);

    fetch('../../controllers/messageController.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Message sent successfully!");
            closeMessageForm();
        } else {
            alert("Error sending message.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("There was an error sending your message.");
    });
}
