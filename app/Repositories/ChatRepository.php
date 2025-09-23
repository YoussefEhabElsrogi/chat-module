<?php

namespace App\Repositories;

use App\Models\
{
    Chat,
    ChatMessage,
};

class ChatRepository
{
    public function getUserChats(int $userId)
    {
        return Chat::query()
            ->where('user_id', $userId)
            ->with('latestMessage')
            ->latest('updated_at')
            ->get();
    }

    public function createChat(int $userId, string $title)
    {
        return Chat::create([
            'user_id' => $userId,
            'title' => $title
        ]);
    }

    public function getMessages(Chat $chat)
    {
        return $chat->messages()->get();
    }

    public function createMessage(Chat $chat, string $message, string $sender)
    {
        return ChatMessage::create([
            'chat_id' => $chat->id,
            'message' => $message,
            'sender' => $sender
        ]);
    }

    public function updateChatTimestamp(Chat $chat)
    {
        $chat->touch();
    }

    public function getRecentMessages(Chat $chat, int $limit = 10)
    {
        return $chat->messages()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->reverse();
    }
    public function getUserChatsWithLatestMessage(int $userId)
    {
        return Chat::where('user_id', $userId)
            ->with('latestMessage')
            ->orderBy('updated_at', 'desc')
            ->get();
    }
    public function deleteChat(Chat $chat)
    {
        return $chat->delete();
    }
}
