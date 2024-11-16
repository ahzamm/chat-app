<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp-Like Chat Interface</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"/>
    <style>
        :root {
            --primary-bg: #e5ddd5;
            --chat-bg: #f0f0f0;
            --sent-bg: #dcf8c6;
            --received-bg: #ffffff;
            --text-color: #333;
            --hover-bg-light: #d0e0d0;
            --hover-bg-dark: #444444;
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
            transition: background-color 0.3s;
        }

        .contact-item:hover {
            background-color: var(--hover-bg-light);
        }

        body.dark-mode .contact-item:hover {
            background-color: var(--hover-bg-dark);
        }

        .contact-item img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

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

        .chat-footer input {
            width: calc(100% - 40px);
            margin-right: 10px;
            border: none;
            border-radius: 20px;
            padding: 10px 15px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        .notification-container {
            position: relative;
        }

        #notificationIcon {
            border: none;
            background: none;
            cursor: pointer;
        }

        #notificationCount {
            font-size: 0.75rem;
            padding: 0.25em 0.5em;
            border-radius: 50%;
            line-height: 1;
        }

        #notificationItems {
            max-height: 200px;
            overflow-y: auto;
            width: 250px;
        }

        .dropdown-menu {
            font-size: 0.9rem;
            background-color: var(--chat-bg);
            color: var(--text-color);
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: var(--hover-bg-light);
        }

        body.dark-mode .dropdown-menu {
            background-color: var(--received-bg);
            color: var(--text-color);
        }

        body.dark-mode .dropdown-menu .dropdown-item:hover {
            background-color: var(--hover-bg-dark);
        }
    </style>
</head>
<body>
    <div class="container-fluid chat-container">
        <div class="col-md-3 contacts-list">
            <div class="sidebar-header">
                <img src="https://via.placeholder.com/45" alt="Profile Picture">
                <span><strong></strong></span>
                <div class="dropdown ml-auto">
                    <button class="btn btn-light position-relative dropdown-toggle" id="notificationIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge badge-danger position-absolute top-0 start-100 translate-middle" id="notificationCount">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationIcon" id="notificationDropdown">
                        <div id="notificationItems" style="max-height: 200px; overflow-y: auto;">
                            <span class="dropdown-item">No new notifications</span>
                        </div>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item text-center text-primary" onclick="markAllAsRead()">Mark all as read</button>
                    </div>
                </div>
                <button class="btn btn-light btn-sm logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i></button>
            </div>          
            <div class="p-2">
                <button class="btn btn-primary btn-sm w-100" data-toggle="modal" data-target="#createGroupModal">Create Group</button>
            </div>
            <div class="p-2">
                <input type="text" id="newContact" class="form-control" placeholder="Add new contact">
                <button class="btn btn-success btn-sm mt-1 w-100" onclick="addContact()">Add</button>
            </div>
            <div id="dynamic-contacts"></div>
        </div>
        <div class="col-md-9 chat-window">
            <div class="chat-header d-flex justify-content-between align-items-center">
                <h5 id="chatWith">Chat with: Select a contact</h5>
                <button class="btn btn-sm btn-light" onclick="toggleDarkMode()">Dark Mode</button>
            </div>
            <div class="chat-messages" id="chatMessages">
            </div>
            <div class="chat-footer d-flex align-items-center">
                <input type="text" id="messageInput" placeholder="Type a message...">
                <button class="btn btn-primary" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGroupModalLabel">Create Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createGroupForm">
                        <div class="form-group">
                            <label for="groupImage">Group Profile Picture</label>
                            <input type="file" class="form-control" id="groupImage" accept="image/*">
                        </div>                        
                        <div class="form-group">
                            <label for="groupName">Group Name</label>
                            <input type="text" class="form-control" id="groupName" placeholder="Enter group name" required>
                        </div>
                        <div class="form-group">
                            <label for="groupMembers">Add Members</label>
                            <select class="form-control" id="groupMembers" multiple="multiple" style="width: 100%;"></select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createGroup()">Create Group</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
     @vite('resources/js/app.js')
    <script>
        fetchContacts()
        let currentChatUserName = '';
        let currentChatUserId = '';

        function sendMessage() {
            const messageText = $('#messageInput').val().trim();
            if (messageText === '' || currentChatUserId === '') return;

            $.ajax({
                url: '{{ route('send.message') }}',
                method: 'POST',
                data: {
                    receiver_id: currentChatUserId,
                    message: messageText,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#messageInput').val('');
                    $('#chatMessages').append(`<div class="message sent">${messageText}</div>`);
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                },
                error: function(xhr) {
                    toastr.error('Failed to send message');
                    console.error(xhr.responseText);
                }
            });
        }

        function addContact() {
            const contactEmail = $('#newContact').val().trim();
            if (contactEmail === '') {
                toastr.error('Please enter an email.');
                return;
            }

            $.ajax({
                url: '{{ route('add.contact') }}',
                method: 'POST',
                data: {
                    email: contactEmail,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);

                    fetchContacts()

                    $('#newContact').val('');
                },
                error: function(xhr) {
                    const error = xhr.responseJSON.message || 'Error adding contact';
                    toastr.error(error);
                }
            });
        }

        function logout() {
            if (confirm('Are you sure you want to log out?')) {
                $.ajax({
                    url: '{{ route('logout') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        toastr.success('Logged out successfully!');
                        setTimeout(() => {
                            window.location.href = '/';
                        }, 1000);
                    },
                    error: function() {
                        toastr.error('Logout failed. Please try again.');
                    }
                });
            } else {
                toastr.info('Logout canceled.');
            }
        }

        $.ajax({
            url: '{{ route('user.details') }}',
            method: 'GET',
            success: function(response) {
                if (response.name && response.profile_pic) {
                    $('.sidebar-header img').attr('src', `storage/${response.profile_pic}`);
                    $('.sidebar-header span strong').text(response.name);
                }
            },
            error: function() {
                console.error('Could not retrieve user details');
            }
        });

        function fetchContacts() {
            $('#dynamic-contacts').empty();

            $.ajax({
                url: '{{ route('user.contacts.and.groups') }}', // Update this route
                method: 'GET',
                success: function(data) {
                    const { contacts, groups } = data;

                    // Render individual contacts
                    contacts.forEach(contact => {
                        $('#dynamic-contacts').append(`
                            <div class="contact-item" data-id="${contact.id}" onclick="openChat('${contact.name}', ${contact.id})">
                                <img src="${contact.profile_pic}" alt="User Image">
                                <div>
                                    <div class="contact-name">${contact.name}</div>
                                </div>
                            </div>
                        `);
                    });

                    // Render groups
                    groups.forEach(group => {
                        $('#dynamic-contacts').append(`
                            <div class="contact-item group-item" data-id="${group.id}" onclick="openGroupChat('${group.name}', ${group.id})">
                                <img src="https://via.placeholder.com/40?text=Group" alt="Group Icon">
                                <div>
                                    <div class="contact-name">${group.name}</div>
                                </div>
                            </div>
                        `);
                    });
                },
                error: function() {
                    console.error('Could not retrieve contacts and groups');
                }
            });
        }

        function openChat(contactName, contactId) {
                currentChatUserName = contactName;
                currentChatUserId   = contactId;
            let currentUserId       = '{{ auth()->id() }}';

            $('#chatWith').text('Chat with: ' + contactName);
            $('#chatMessages').empty();

            window.Echo.private(`chat.${currentUserId}`).stopListening('.message.sent');

            window.Echo.private(`chat.${currentUserId}`)
            .listen('.message.sent', (event) => {
                if(event.receiver_id==currentUserId){
                    $('#chatMessages').append(`<div class="message received">${event.message}</div>`);
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                }
            });

            $.ajax({
                url: '{{ route('get.messages') }}',
                method: 'POST',
                data: {
                    contact_id: contactId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(messages) {
                    messages.forEach(message => {
                        const messageClass = message.sender_id === currentChatUserId ? 'received' : 'sent';
                        $('#chatMessages').append(`
                            <div class="message ${messageClass}">${message.message}</div>
                        `);
                    });

                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                },
                error: function(xhr) {
                    toastr.error('Failed to load messages');
                    console.error(xhr.responseText);
                }
            });
        }

        function openGroupChat(groupName, groupId) {
            $('#chatWith').text('Group Chat: ' + groupName);
            $('#chatMessages').empty();

            let currentUserId = '{{ auth()->id() }}';

            // Listen for group messages
            window.Echo.private(`group.${groupId}`).stopListening('.message.sent');
            window.Echo.private(`group.${groupId}`)
                .listen('.message.sent', (event) => {
                    $('#chatMessages').append(`<div class="message received">${event.message}</div>`);
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                });

            // Fetch group messages
            $.ajax({
                url: '{{ route('get.group.messages') }}',
                method: 'POST',
                data: {
                    group_id: groupId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(messages) {
                    messages.forEach(message => {
                        const messageClass = message.sender_id === currentUserId ? 'sent' : 'received';
                        $('#chatMessages').append(`
                            <div class="message ${messageClass}">${message.message}</div>
                        `);
                    });

                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                },
                error: function(xhr) {
                    toastr.error('Failed to load group messages');
                    console.error(xhr.responseText);
                }
            });
        }

        function toggleDarkMode() {
            $('body').toggleClass('dark-mode');
        }

        $('#groupMembers').select2({
            placeholder: 'Select members',
            allowClear: true,
            ajax: {
                url: '{{ route('user.contacts') }}',
                method: 'GET',
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: data.map(contact => ({
                            id: contact.id,
                            text: `${contact.name} (${contact.email})`
                        }))
                    };
                }
            }
        });

        function createGroup() {
            const groupName = $('#groupName').val().trim();
            const memberIds = $('#groupMembers').val();

            if (!groupName || !memberIds.length) {
                toastr.error('Please enter a group name and select members.');
                return;
            }

            $.ajax({
                url: '{{ route('create.group') }}',
                method: 'POST',
                data: {
                    name: groupName,
                    members: memberIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#createGroupModal').modal('hide');
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error('Failed to create group. Please try again.');
                    console.error(xhr.responseText);
                }
            });
        }

        function updateNotificationCount(count) {
            const badge = $('#notificationCount');
            badge.text(count);

            if (count > 0) {
                badge.show();
            } else {
                badge.hide();
            }
        }

        // Simulate notifications update
        setInterval(() => {
            const count = Math.floor(Math.random() * 10); // Replace with actual API call
            updateNotificationCount(count);
        }, 5000);

        function updateNotificationDropdown(notifications) {
            const notificationItems = $('#notificationItems');
            notificationItems.empty();

            if (notifications.length === 0) {
                notificationItems.append('<span class="dropdown-item">No new notifications</span>');
            } else {
                notifications.forEach(notification => {
                    notificationItems.append(`
                        <a href="${notification.link}" class="dropdown-item">
                            ${notification.message}
                        </a>
                    `);
                });
            }

            updateNotificationCount(notifications.length);
        }

        function updateNotificationCount(count) {
            const badge = $('#notificationCount');
            badge.text(count);

            if (count > 0) {
                badge.show();
            } else {
                badge.hide();
            }
        }

        function markAllAsRead() {
           
        }

        // Simulated notifications fetch (Replace this with API call)
        function fetchNotifications() {
            const exampleNotifications = [
                { message: 'New message from John', link: '/chat/john' },
                { message: 'Your group project is due tomorrow', link: '/tasks' },
                { message: 'Alice commented on your post', link: '/comments' }
            ];

            updateNotificationDropdown(exampleNotifications);
        }

        // Fetch notifications on load
        fetchNotifications();

        // Periodically fetch new notifications
        setInterval(fetchNotifications, 10000);


    </script>

</body>

</html>
