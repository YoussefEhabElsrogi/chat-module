<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\{
    DeleteChatRequest,
    GetChatMessagesRequest,
    SendMessageRequest,
};
use App\Services\ChatService;
use App\Models\{
    Chat,
    ChatMessage,
};
use Illuminate\Contracts\View\View;
use Illuminate\Http\{
    JsonResponse,
};
use Illuminate\Support\Facades\{
    Auth,
    Http,
    Log,
};

class ChatController extends Controller
{
    public function __construct(protected ChatService $chatService)
    {
    }
    /**
     * Get the chat index view
     *
     * @return View
     */
    public function index(): View
    {
        $chats = $this->chatService->getUserChats(Auth::id());
        return view('dashboard.chats.index', compact('chats'));
    }

    /**
     * Start a new chat session
     *
     * @return JsonResponse
     */
    public function startChat(): JsonResponse
    {
        $chat = $this->chatService->createChat(Auth::id());

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    /**
     * Get chat messages
     *
     * @param GetChatMessagesRequest $request
     * @param Chat $chat
     *
     * @return JsonResponse
     */
    public function getMessages(GetChatMessagesRequest $request, Chat $chat): JsonResponse
    {
        $messages = $this->chatService->getMessages($chat);

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Send a message and get AI response
     */
    public function sendMessage(SendMessageRequest $request, Chat $chat): JsonResponse
    {
        $result = $this->chatService->sendMessage($chat, $request->message);

        return response()->json([
            'success' => true,
            'user_message' => $result['user_message'],
            'ai_message' => $result['ai_message'],
        ]);
    }

    /**
     * Get user's chat history
     */
    public function getChatHistory(): JsonResponse
    {
        $chats = $this->chatService->getChatHistory(Auth::id());

        return response()->json([
            'success' => true,
            'chats' => $chats
        ]);
    }

    /**
     * Delete a chat
     *
     * @param DeleteChatRequest $request
     * @param Chat $chat
     *
     * @return JsonResponse
     */
    public function deleteChat(DeleteChatRequest $request, Chat $chat): JsonResponse
    {
        $this->chatService->deleteChat($chat);

        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully'
        ]);
    }
}
