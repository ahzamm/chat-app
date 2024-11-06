<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsApp-Like Chat Interface</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    /* Custom CSS for the interface */
    body {
      background-color: #f8f9fa;
    }
    .chat-container {
      height: 100vh;
      display: flex;
    }
    /* Sidebar Styling */
    .contacts-list {
      background-color: #f0f0f0;
      overflow-y: auto;
      border-right: 1px solid #ddd;
    }
    .contact-item {
      padding: 15px;
      cursor: pointer;
      display: flex;
      align-items: center;
    }
    .contact-item:hover {
      background-color: #e7e7e7;
    }
    .contact-item img {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      margin-right: 10px;
    }
    .contact-name {
      font-weight: bold;
    }
    /* Chat Window Styling */
    .chat-window {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .chat-header, .chat-footer {
      background-color: #f0f0f0;
      padding: 10px 20px;
    }
    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background-color: #ffffff;
    }
    .message {
      margin-bottom: 15px;
      max-width: 75%;
      padding: 10px;
      border-radius: 10px;
      position: relative;
      clear: both;
    }
    .message.sent {
      background-color: #dcf8c6;
      float: right;
    }
    .message.received {
      background-color: #ffffff;
      border: 1px solid #ddd;
      float: left;
    }
    /* Chat Footer */
    .chat-footer input {
      width: calc(100% - 40px);
      margin-right: 10px;
      border: none;
      border-radius: 20px;
      padding: 10px 15px;
      box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>

<div class="container-fluid chat-container">
  <!-- Contacts Sidebar -->
  <div class="col-md-3 contacts-list">
    <div class="contact-item" onclick="openChat('John Doe')">
      <img src="https://via.placeholder.com/40" alt="User Image">
      <div>
        <div class="contact-name">John Doe</div>
        <small>Last message...</small>
      </div>
    </div>
    <!-- Add more contact items similarly -->
  </div>

  <!-- Chat Window -->
  <div class="col-md-9 chat-window">
    <!-- Chat Header -->
    <div class="chat-header">
      <h5 id="chatWith">Chat with: Select a contact</h5>
    </div>
    <!-- Chat Messages Area -->
    <div class="chat-messages" id="chatMessages">
      <!-- Messages will appear here dynamically -->
    </div>
    <!-- Chat Footer -->
    <div class="chat-footer d-flex align-items-center">
      <input type="text" id="messageInput" placeholder="Type a message...">
      <button class="btn btn-primary" onclick="sendMessage()">
        <i class="fas fa-paper-plane"></i>
      </button>
    </div>
  </div>
</div>

<!-- jQuery and Bootstrap Bundle (including Popper) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
  // Placeholder for current chat user
  let currentChatUser = '';

  function openChat(contactName) {
    currentChatUser = contactName;
    $('#chatWith').text('Chat with: ' + contactName);
    $('#chatMessages').empty(); // Clear previous chat
    // You could load chat messages from a server here if needed
  }

  function sendMessage() {
    const messageText = $('#messageInput').val().trim();
    if (messageText === '' || currentChatUser === '') return; // Ignore empty messages or if no user selected

    // Append sent message to chat
    $('#chatMessages').append(`
      <div class="message sent">
        ${messageText}
      </div>
    `);

    $('#messageInput').val(''); // Clear input

    // Scroll to the bottom of the chat
    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);

    // Simulate receiving a response
    setTimeout(() => {
      $('#chatMessages').append(`
        <div class="message received">
          This is a reply to: ${messageText}
        </div>
      `);
      $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
    }, 1000);
  }
</script>

</body>
</html>
