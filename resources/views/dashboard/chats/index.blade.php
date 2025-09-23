@extends('partials.dashboard.app')

@section('title', 'AI Chat')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <!-- Mobile Header -->
            <div class="mobile-header d-lg-none">
                <div class="d-flex align-items-center justify-content-between p-3 bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <button id="sidebarToggle" class="btn btn-link p-2 mr-3">
                            <i class="ft-menu"></i>
                        </button>
                        <h4 class="mb-0 font-weight-bold text-dark">AI Chat</h4>
                    </div>
                    <button id="newChatBtn" class="btn btn-primary btn-sm">
                        <i class="ft-plus"></i> New Chat
                    </button>
                </div>
            </div>

            <div class="content-body">
                <div class="row h-100">
                    <!-- Chat History Sidebar -->
                    <div class="col-lg-4 col-md-5">
                        <div class="card h-100">
                            <!-- Desktop Header -->
                            <div class="card-header d-none d-lg-block">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title mb-0 font-weight-bold">AI Chat</h4>
                                    <button id="newChatBtnDesktop" class="btn btn-primary btn-sm">
                                        <i class="ft-plus"></i> New Chat
                                    </button>
                                </div>
                            </div>

                            <!-- Chat List -->
                            <div class="card-content">
                                <div class="card-body p-0">
                                    <div id="chatList" class="chat-list">
                                        @forelse($chats as $chat)
                                            <div class="chat-item-wrapper" data-chat-id="{{ $chat->id }}">
                                                <div class="chat-item p-3 border-bottom cursor-pointer">
                                                    <div class="d-flex align-items-start justify-content-between">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 font-weight-medium text-dark">{{ $chat->title }}</h6>
                                                            <p class="mb-1 text-muted small">
                                                                @if($chat->latestMessage)
                                                                    {{ Str::limit($chat->latestMessage->message, 50) }}
                                                                @else
                                                                    No messages yet
                                                                @endif
                                                            </p>
                                                            <small class="text-muted">{{ $chat->updated_at->diffForHumans() }}</small>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-chat-btn ml-2"
                                                                data-chat-id="{{ $chat->id }}" title="Delete chat">
                                                            <i class="ft-trash-2"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="ft-message-circle" style="font-size: 48px; color: #6c757d;"></i>
                                                </div>
                                                <h5 class="text-muted">No conversations yet</h5>
                                                <p class="text-muted small">Start your first conversation with AI</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Chat Area -->
                    <div class="col-lg-8 col-md-7">
                        <div class="card h-100 d-flex flex-column">
                            <!-- Chat Header -->
                            <div class="card-header">
                                <h4 class="card-title mb-0" id="chatTitle">Select a chat or start a new one</h4>
                            </div>

                            <!-- Messages Area -->
                            <div class="card-content flex-grow-1">
                                <div class="card-body p-0">
                                    <div id="chatMessages" class="chat-messages">
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <div class="text-center">
                                                <div class="mb-4">
                                                    <i class="ft-message-circle" style="font-size: 64px; color: #007bff; opacity: 0.3;"></i>
                                                </div>
                                                <h4 class="text-muted">Welcome to AI Chat</h4>
                                                <p class="text-muted">Start a conversation with our AI assistant</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Area -->
                            <div class="card-footer">
                                <form id="chatForm" class="d-flex align-items-end">
                                    <div class="form-group flex-grow-1 mb-0 mr-2">
                                        <textarea id="messageInput"
                                                class="form-control auto-resize"
                                                placeholder="Type your message..."
                                                rows="1"
                                                disabled
                                                style="min-height: 48px; max-height: 120px; resize: none;"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" disabled>
                                        <i class="ft-send"></i> Send
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay d-lg-none"></div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3 mb-0">AI is thinking...</p>
        </div>
    </div>
@endsection

@push('css')
    @vite(['resources/css/chat.css'])
@endpush

@push('js')
    <script>
        // Pass user data to JavaScript
        window.userData = {
            name: '{{ Auth::user()->name }}',
            id: {{ Auth::id() }}
        };
    </script>
    @vite(['resources/js/chat.js'])
@endpush
