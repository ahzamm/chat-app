<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp-Like Chat Interface</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
    </style>
</head>

<body>

    <div class="container-fluid chat-container">
        <div class="col-md-3 contacts-list">
            <div class="sidebar-header">
                <img src="https://via.placeholder.com/45" alt="Profile Picture">
                <span><strong></strong></span>
                <button class="btn btn-light btn-sm logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i></button>
            </div>

            <div class="p-2">
                <input type="text" id="newContact" class="form-control" placeholder="Add new contact">
                <button class="btn btn-success btn-sm mt-1 w-100" onclick="addContact()">Add</button>
            </div>

            <!-- Contacts List -->
            {{-- <div class="contact-item" onclick="openChat('John Doe')">
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
            </div> --}}

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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- <script src="{{ asset('ws.js') }}"></script> -->
     <!-- <script src="recource/js/bootstrap.js"></script> -->
     <!-- <script src="{{ asset('bootstrap.js') }}"></script> -->
     @vite('resources/js/app.js')






    <script>
        fetchContacts()
        let currentChatUserName = '';
        let currentChatUserId = '';

        console.log(window.Echo);

        // Echo.private(`chat.${currentChatUserId}`)
        //             .listen('.message.sent', (event) => {
        //                 console.log('event is ', event);
        //                 const messageClass = event.sender_id === currentChatUserId ? 'received' : 'sent';
        //                 $('#chatMessages').append(`
        //                     <div class="message ${messageClass}">${event.message}</div>
        //                 `);
        //                 $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
        //             });

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
                    // $('#chatMessages').append(`<div class="message sent">${messageText}</div>`);
                    $('#messageInput').val('');
                    $('#chatMessages').append(`<div class="message sent">${messageText}</div>`);
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    // $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    
                    // window.Echo.private(`chat.${currentChatUserId}`)
                    // .listen('.message.sent', (event) => {
                    //     console.log('event is ', event);
                    //     const messageClass = event.sender_id === currentChatUserId ? 'received' : 'sent';
                    //     $('#chatMessages').append(`
                    //         <div class="message ${messageClass}">${event.message}</div>
                    //     `);
                    //     $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    // });


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
                url: '{{ route('user.contacts') }}',
                method: 'GET',
                success: function(contacts) {
                    contacts.forEach(contact => {
                        console.log('contact id in fetch contact function is : ',contact.id);
                        $('#dynamic-contacts').append(`
                            <div class="contact-item" data-id="${contact.id}" onclick="openChat('${contact.name}', ${contact.id})">
                                <img src="${contact.profile_pic}" alt="User Image">
                                <div>
                                    <div class="contact-name">${contact.name}</div>
                                </div>
                            </div>
                        `);
                    });
                },
                error: function() {
                    console.error('Could not retrieve contacts');
                }
            });
        }

        function openChat(contactName, contactId) {
            currentChatUserName = contactName;
            currentChatUserId = contactId;

            $('#chatWith').text('Chat with: ' + contactName);
            $('#chatMessages').empty();


            let currentUserId = '{{ auth()->id() }}';

            window.Echo.private(`chat.${currentUserId}`)
                .listen('.message.sent', (event) => {
                    console.log('Message event received:', event);

                    // Determine if the message is sent or received
                    const messageClass = event.sender_id === currentUserId ? 'sent' : 'received';

                    $('#chatMessages').append(`
                        <div class="message ${messageClass}">${event.message}</div>
                    `);

                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
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



        function toggleDarkMode() {
            $('body').toggleClass('dark-mode');
        }
    </script>

</body>

</html>
