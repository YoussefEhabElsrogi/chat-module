class ChatApp {
    constructor() {
        this.currentChatId = null;
        this.isLoading = false;
        this.isTyping = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupCSRF();
        this.setupMobileSidebar();
        this.setupAutoResize();
        // Don't restore immediately - wait for DOM to be ready
        this.waitForDOMAndRestore();
    }

    setupCSRF() {
        this.csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
    }

    setupMobileSidebar() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');
        const sidebar = document.querySelector('.col-lg-4');

        if (sidebarToggle && sidebar && overlay) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.style.display = 'none';
            });

            // Close sidebar when clicking on chat items on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 992 && e.target.closest('.chat-item')) {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                }
            });
        }
    }

    setupAutoResize() {
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.addEventListener('input', () => {
                this.autoResizeTextarea(messageInput);
            });
        }
    }

    // Save active chat ID to localStorage
    saveActiveChat(chatId) {
        if (chatId) {
            localStorage.setItem('activeChatId', chatId);
        } else {
            localStorage.removeItem('activeChatId');
        }
    }

    // Wait for DOM to be ready and then restore active chat
    waitForDOMAndRestore() {
        // Try to restore immediately if DOM is ready
        if (document.readyState === 'complete') {
            this.restoreActiveChat();
        } else if (document.readyState === 'interactive') {
            // DOM is loaded but resources might still be loading
            this.restoreActiveChat();
        } else {
            // DOM is still loading
            document.addEventListener('DOMContentLoaded', () => {
                this.restoreActiveChat();
            });
        }
    }

    // Restore active chat from localStorage
    async restoreActiveChat() {
        const savedChatId = localStorage.getItem('activeChatId');

        if (savedChatId) {
            // Try to find the chat immediately
            let chatWrapper = document.querySelector(`.chat-item-wrapper[data-chat-id="${savedChatId}"]`);

            // If not found, wait a bit and try again (for dynamic content)
            if (!chatWrapper) {
                // Try multiple times with short intervals
                for (let i = 0; i < 5; i++) {
                    await new Promise(resolve => setTimeout(resolve, 100));
                    chatWrapper = document.querySelector(`.chat-item-wrapper[data-chat-id="${savedChatId}"]`);
                    if (chatWrapper) break;
                }
            }

            if (chatWrapper) {
                await this.loadChat(savedChatId);
            } else {
                localStorage.removeItem('activeChatId');
            }
        }
    }


    bindEvents() {
        // New chat buttons (mobile and desktop)
        const newChatBtn = document.getElementById('newChatBtn');
        const newChatBtnDesktop = document.getElementById('newChatBtnDesktop');

        if (newChatBtn) {
            newChatBtn.addEventListener('click', () => this.startNewChat());
        }
        if (newChatBtnDesktop) {
            newChatBtnDesktop.addEventListener('click', () => this.startNewChat());
        }

        // Chat form submission
        const chatForm = document.getElementById('chatForm');
        if (chatForm) {
            chatForm.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Chat item clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.chat-item')) {
                e.preventDefault();
                const chatWrapper = e.target.closest('.chat-item-wrapper');
                const chatId = chatWrapper ? chatWrapper.dataset.chatId : null;
                if (chatId) {
                    this.loadChat(chatId);
                }
            }
        });

        // Delete chat button
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-chat-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const chatId = e.target.closest('.delete-chat-btn').dataset.chatId;
                this.deleteChat(chatId);
            }
        });

        // Enter key handling
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.handleSubmit(e);
                }
            });
        }

        // Handle window resize for mobile sidebar
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                const sidebar = document.querySelector('.col-lg-4');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar && overlay) {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                }
            }
        });
    }

    async startNewChat() {
        try {
            const hasExistingChats = this.hasAnyChats();

            const response = await fetch("/dashboard/chat/start", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
            });

            const data = await response.json();

            if (data.success) {
                this.currentChatId = data.chat.id;
                this.saveActiveChat(data.chat.id); // Save to localStorage
                this.updateChatTitle(data.chat.title);
                this.clearMessages();
                this.enableInput();
                this.scrollToBottom();

                if (!hasExistingChats) {
                    await this.refreshChatList();
                } else {
                    this.addChatToList(data.chat);
                }
            }
        } catch (error) {
            this.showError("Failed to start new chat");
        }
    }

    async loadChat(chatId) {
        try {
            if (!chatId) {
                this.showError("No chat ID provided");
                return;
            }

            this.currentChatId = chatId;
            this.saveActiveChat(chatId);

            // Get chat wrapper first
            const chatWrapper = document.querySelector(`.chat-item-wrapper[data-chat-id="${chatId}"]`);
            if (!chatWrapper) {
                this.showError("Chat not found");
                return;
            }

            // Load messages first
            const response = await fetch(`/dashboard/chat/${chatId}/messages`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Clear messages and load new ones
                this.clearMessages();
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach((message) => this.addMessage(message));
                } else {
                    // Show empty chat message if no messages
                    this.showEmptyChatMessage();
                }
                this.scrollToBottom();

                // NOW update UI - everything together
                this.enableInput();

                // Update active state
                document.querySelectorAll('.chat-item').forEach((item) => {
                    item.classList.remove('active');
                });
                chatWrapper.querySelector('.chat-item').classList.add('active');

                // Update title
                const titleElement = chatWrapper.querySelector('h6');
                if (titleElement) {
                    this.updateChatTitle(titleElement.textContent);
                }

            } else {
                this.showError(data.error || "Failed to load chat messages");
            }
        } catch (error) {
            this.showError(`Failed to load chat: ${error.message}`);
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        if (this.isLoading || !this.currentChatId) return;

        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();

        if (!message) return;

        // Add user message
        this.addMessage({
            sender: "user",
            message: message,
            created_at: new Date().toISOString(),
        });

        messageInput.value = "";
        this.autoResizeTextarea(messageInput);
        this.disableInput();
        this.showLoading();

        try {
            const response = await fetch(`/dashboard/chat/${this.currentChatId}/send`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
                body: JSON.stringify({ message: message }),
            });

            const data = await response.json();

            if (data.success) {
                this.addMessage(data.ai_message);
                this.updateChatInList(this.currentChatId, data.ai_message.message);
            } else {
                this.showError("Failed to send message");
            }
        } catch (error) {
            this.showError("Failed to send message");
        } finally {
            this.hideLoading();
            this.enableInput();
        }
    }

    addMessage(message) {
        const messagesContainer = document.getElementById('chatMessages');

        // Remove welcome message or empty chat message if they exist
        const centerMessage = messagesContainer.querySelector('.d-flex.align-items-center.justify-content-center');
        if (centerMessage) {
            centerMessage.remove();
        }

        const messageElement = document.createElement('div');
        messageElement.className = `message ${message.sender}`;

        const time = new Date(message.created_at).toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
        });

        const avatarContent = message.sender === "user"
            ? (window.userData && window.userData.name ? window.userData.name.charAt(0).toUpperCase() : 'U')
            : '';

        messageElement.innerHTML = `
            <div class="message-avatar">
                ${avatarContent}
            </div>
            <div class="message-content">
                <div class="message-text">${this.formatMessage(message.message)}</div>
                <div class="message-time">${time}</div>
            </div>
        `;

        messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
    }

    formatMessage(message) {
        // Convert line breaks to HTML
        let formatted = message.replace(/\n/g, '<br>');

        // Convert URLs to links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        formatted = formatted.replace(urlRegex, '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 underline">$1</a>');

        // Convert code blocks
        formatted = formatted.replace(/```([\s\S]*?)```/g, '<pre class="bg-gray-100 p-3 rounded-lg overflow-x-auto my-2"><code>$1</code></pre>');

        // Convert inline code
        formatted = formatted.replace(/`([^`]+)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm">$1</code>');

        return formatted;
    }

    clearMessages() {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.innerHTML = '';
    }

    updateChatTitle(title) {
        const titleElement = document.getElementById('chatTitle');
        if (titleElement) {
            titleElement.textContent = title;
        }
    }

    enableInput() {
        const messageInput = document.getElementById('messageInput');
        const submitButton = document.querySelector('#chatForm button[type="submit"]');

        if (messageInput) messageInput.disabled = false;
        if (submitButton) submitButton.disabled = false;
    }

    disableInput() {
        const messageInput = document.getElementById('messageInput');
        const submitButton = document.querySelector('#chatForm button[type="submit"]');

        if (messageInput) messageInput.disabled = true;
        if (submitButton) submitButton.disabled = true;
    }

    showLoading() {
        this.isLoading = true;
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.classList.remove('hidden');
        }
    }

    hideLoading() {
        this.isLoading = false;
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    }

    showError(message) {
        // Use SweetAlert2 if available, otherwise use alert
        if (typeof Swal !== 'undefined') {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-danger",
                },
                buttonsStyling: true,
            });

            swalWithBootstrapButtons.fire({
                title: "Error!",
                text: message,
                icon: "error",
                confirmButtonText: "OK",
            });
        } else {
            alert(`Error: ${message}`);
        }
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chatMessages');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    async deleteChat(chatId) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger",
            },
            buttonsStyling: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true,
            focusConfirm: false,
            heightAuto: false,
        });

        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "You will not be able to recover this chat!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true,
            })
            .then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/dashboard/chat/${chatId}`, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": this.csrfToken,
                            },
                        });

                        const data = await response.json();

                        if (data.success) {
                            const remainingChats = this.getRemainingChatsCount();

                            // If this was the current chat, clear the chat area
                            if (this.currentChatId == chatId) {
                                this.currentChatId = null;
                                this.saveActiveChat(null); // Clear from localStorage
                                this.clearMessages();
                                this.updateChatTitle("Select a chat or start a new one");
                                this.disableInput();

                                // Show welcome message again
                                this.showWelcomeMessage();
                            }

                            if (remainingChats === 1) {
                                await this.refreshChatList();
                                swalWithBootstrapButtons.fire({
                                    title: "Deleted!",
                                    text: "Your chat has been deleted.",
                                    icon: "success",
                                });
                            } else {
                                const chatWrapper = document.querySelector(`.chat-item-wrapper[data-chat-id="${chatId}"]`);
                                if (chatWrapper) {
                                    chatWrapper.remove();
                                }
                                swalWithBootstrapButtons.fire({
                                    title: "Deleted!",
                                    text: "Your chat has been deleted.",
                                    icon: "success",
                                });
                            }
                        } else {
                            this.showError("Failed to delete chat");
                        }
                    } catch (error) {
                        this.showError("Failed to delete chat");
                    }
                }
            });
    }

    showWelcomeMessage() {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="ft-message-circle" style="font-size: 64px; color: #007bff; opacity: 0.3;"></i>
                    </div>
                    <h4 class="text-muted">Welcome to AI Chat</h4>
                    <p class="text-muted">Start a conversation with our AI assistant</p>
                </div>
            </div>
        `;
    }

    showEmptyChatMessage() {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="ft-message-square" style="font-size: 64px; color: #6c757d; opacity: 0.3;"></i>
                    </div>
                    <h4 class="text-muted">This chat is empty</h4>
                    <p class="text-muted">Start typing your message below to begin the conversation</p>
                </div>
            </div>
        `;
    }

    addChatToList(chat) {
        const chatList = document.getElementById('chatList');
        const chatWrapper = document.createElement('div');
        chatWrapper.className = 'chat-item-wrapper';
        chatWrapper.dataset.chatId = chat.id;

        chatWrapper.innerHTML = `
            <div class="chat-item p-3 border-bottom cursor-pointer">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-medium text-dark">${chat.title}</h6>
                        <p class="mb-1 text-muted small">New conversation started</p>
                        <small class="text-muted">Just now</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger delete-chat-btn ml-2" data-chat-id="${chat.id}" title="Delete chat">
                        <i class="ft-trash-2"></i>
                    </button>
                </div>
            </div>
        `;

        chatList.insertBefore(chatWrapper, chatList.firstChild);
    }

    updateChatInList(chatId, lastMessage) {
        const chatWrapper = document.querySelector(`.chat-item-wrapper[data-chat-id="${chatId}"]`);
        if (chatWrapper) {
            const messageElement = chatWrapper.querySelector('p');
            if (messageElement) {
                messageElement.textContent = lastMessage.length > 60
                    ? lastMessage.substring(0, 60) + "..."
                    : lastMessage;
            }
        }
    }

    getRemainingChatsCount() {
        const chatWrappers = document.querySelectorAll('.chat-item-wrapper');
        return chatWrappers.length;
    }

    hasAnyChats() {
        const chatList = document.getElementById('chatList');
        const emptyMessage = chatList.querySelector('.text-center');
        return !emptyMessage;
    }

    autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';

        const scrollHeight = textarea.scrollHeight;
        const minHeight = 48;
        const maxHeight = 120;

        const newHeight = Math.min(Math.max(scrollHeight, minHeight), maxHeight);

        textarea.style.height = newHeight + 'px';
        textarea.style.overflowY = scrollHeight > maxHeight ? 'auto' : 'hidden';
    }

    async refreshChatList() {
        try {
            const response = await fetch("/dashboard/chat", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
            });

            if (response.ok) {
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");
                const newChatList = doc.querySelector("#chatList");

                if (newChatList) {
                    const currentChatList = document.getElementById('chatList');
                    currentChatList.innerHTML = newChatList.innerHTML;
                    this.rebindChatEvents();
                }
            }
        } catch (error) {
            // Error refreshing chat list
        }
    }

    rebindChatEvents() {
        // Rebind events for dynamically added chat items
        document.querySelectorAll('.chat-item').forEach((chatItem) => {
            chatItem.addEventListener('click', (e) => {
                e.preventDefault();
                const chatWrapper = e.target.closest('.chat-item-wrapper');
                const chatId = chatWrapper ? chatWrapper.dataset.chatId : null;
                if (chatId) {
                    this.loadChat(chatId);
                }
            });
        });

        document.querySelectorAll('.delete-chat-btn').forEach((deleteBtn) => {
            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const chatId = e.target.closest('.delete-chat-btn').dataset.chatId;
                this.deleteChat(chatId);
            });
        });
    }
}

// Initialize the chat app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ChatApp();
});
