<?php

namespace App\Services;

use Exception;
use App\MessageSender;
use App\Models\Chat;
use App\Repositories\ChatRepository;
use Illuminate\Support\Facades\Http;

class ChatService
{
    public function __construct(protected ChatRepository $chatRepository)
    {
    }

    public function getUserChats(int $userId)
    {
        return $this->chatRepository->getUserChats($userId);
    }

    public function createChat(int $userId)
    {
        $title = 'New Chat ' . now()->format('M j, Y g:i A');
        return $this->chatRepository->createChat($userId, $title);
    }

    public function getMessages(Chat $chat)
    {
        return $this->chatRepository->getMessages($chat);
    }

    public function sendMessage(Chat $chat, string $message): array
    {
        // Save user message
        $userMessage = $this->chatRepository->createMessage($chat, $message, MessageSender::USER->value);

        // Get AI response
        $aiResponse = $this->getAiResponse($message, $chat);

        // Save AI message
        $aiMessage = $this->chatRepository->createMessage($chat, $aiResponse, MessageSender::AI->value);

        // Update chat timestamp
        $this->chatRepository->updateChatTimestamp($chat);

        return [
            'user_message' => $userMessage,
            'ai_message' => $aiMessage
        ];

    }

    private function getAiResponse(string $message, Chat $chat): string
    {
        try {
            // Get recent messages for context
            $recentMessages = $this->chatRepository->getRecentMessages($chat);

            // Build conversation context
            $conversation = $this->buildConversation($recentMessages->toArray(), $message);

            // Send conversation to Gemini API
            $response = $this->getGeminiResponse($conversation);

            if ($response->successful()) {
                return $response->json()['candidates'][0]['content']['parts'][0]['text'];
            } else {
                return 'Sorry, I am having trouble connecting to the AI service. Please try again later.';
            }

        } catch (Exception $e) {
            return 'Sorry, I am having trouble connecting to the AI service. Please try again later.';
        }
    }

    public function getChatHistory(int $userId)
    {
        return $this->chatRepository->getUserChatsWithLatestMessage($userId);
    }

    public function deleteChat(Chat $chat)
    {
        return $this->chatRepository->deleteChat($chat);
    }

    private function buildConversation(array $recentMessages, string $currentMessage): array
    {
        $conversation = [];

        foreach ($recentMessages as $message) {
            $role = $message['sender'] === MessageSender::USER->value
                ? MessageSender::USER->value
                : MessageSender::ASSISTANT->value;

            $conversation[] = [
                'role' => $role,
                'content' => $message['message']
            ];
        }

        // Add current user message
        $conversation[] = [
            'role' => MessageSender::USER->value,
            'content' => $currentMessage
        ];

        return $conversation;
    }

    private function getGeminiResponse(array $conversation): object
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => config('services.gemini.key')
        ])->post(
                config('services.gemini.endpoint'),
                $this->getGeminiData($conversation)
            );
    }

    private function getGeminiData(array $conversation): array
    {
        return [
            'contents' => array_map(function ($msg) {
                return [
                    'role' => $msg['role'],
                    'parts' => [
                        ['text' => $msg['content']]
                    ]
                ];
            }, $conversation)
        ];
    }
}
