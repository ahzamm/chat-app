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
        /* Custom CSS */
        :root {
            --primary-bg: #e5ddd5;
            --chat-bg: #f0f0f0;
            --sent-bg: #dcf8c6;
            --received-bg: #ffffff;
            --text-color: #333;
        }

        body.dark-mode {
            --primary-bg: #1e1e1e;
            --chat-bg: #333333;
            --sent-bg: #128c7e;
            --received-bg: #2a2a2a;
            --text-color: #e5e5e5;
        }

        body {
            background-color: var(--primary-bg);
            color: var(--text-color);
        }

        .chat-container {
            height: 100vh;
            display: flex;
        }

        /* Sidebar Styling */
        .contacts-list {
            background-color: var(--chat-bg);
            overflow-y: auto;
            border-right: 1px solid #ddd;
            position: relative;
        }

        .contact-item {
            padding: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }

        .contact-item:hover {
            background-color: #d0e0d0;
        }

        .contact-item img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        /* Header with Profile and Logout */
        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background-color: var(--primary-bg);
        }

        .sidebar-header img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .sidebar-header .logout-btn {
            margin-left: auto;
        }

        /* Chat Window Styling */
        .chat-window {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header,
        .chat-footer {
            background-color: var(--chat-bg);
            padding: 10px 20px;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: var(--primary-bg);
        }

        .message {
            margin-bottom: 15px;
            max-width: 75%;
            padding: 10px;
            border-radius: 10px;
            position: relative;
            clear: both;
            color: var(--text-color);
            /* Text color adjusted based on theme */
        }

        .message.sent {
            background-color: var(--sent-bg);
            float: right;
        }

        .message.received {
            background-color: var(--received-bg);
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
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <img src="https://via.placeholder.com/45" alt="Profile Picture">
                <span>User Name</span>
                <button class="btn btn-light btn-sm logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i></button>
            </div>

            <!-- Add Contact Field -->
            <div class="p-2">
                <input type="text" id="newContact" class="form-control" placeholder="Add new contact">
                <button class="btn btn-success btn-sm mt-1 w-100" onclick="addContact()">Add</button>
            </div>

            <!-- Contacts List -->
            <div class="contact-item" onclick="openChat('John Doe')">
                <img src="https://via.placeholder.com/40" alt="User Image">
                <div>
                    <div class="contact-name">John Doe</div>
                    <small>Last message...</small>
                </div>
            </div>
            <div class="contact-item" onclick="openChat('Ahzam Ahmed')">
                <img src="https://via.placeholder.com/40" alt="User Image">
                <div>
                    <div class="contact-name">Ahzam Ahmed</div>
                    <small>Asslam-o-Alakum...</small>
                </div>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="col-md-9 chat-window">
            <!-- Chat Header -->
            <div class="chat-header d-flex justify-content-between align-items-center">
                <h5 id="chatWith">Chat with: Select a contact</h5>
                <button class="btn btn-sm btn-light" onclick="toggleDarkMode()">Dark Mode</button>
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
        let currentChatUser = '';

        function openChat(contactName) {
            currentChatUser = contactName;
            $('#chatWith').text('Chat with: ' + contactName);
            $('#chatMessages').empty();
        }

        function sendMessage() {
            const messageText = $('#messageInput').val().trim();
            if (messageText === '' || currentChatUser === '') return;

            $('#chatMessages').append(`<div class="message sent">${messageText}</div>`);
            $('#messageInput').val('');
            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);

            setTimeout(() => {
                $('#chatMessages').append(`<div class="message received">Reply to: ${messageText}</div>`);
                $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
            }, 1000);
        }

        function addContact() {
            const contactName = $('#newContact').val().trim();
            if (contactName) {
                $('.contacts-list').append(`
        <div class="contact-item" onclick="openChat('${contactName}')">
          <img src="https://via.placeholder.com/40" alt="User Image">
          <div><div class="contact-name">${contactName}</div></div>
        </div>
      `);
                $('#newContact').val('');
            }
        }

        function logout() {
            alert('Logged out successfully!');
        }

        function toggleDarkMode() {
            $('body').toggleClass('dark-mode');
        }
    </script>

</body>

</html>
