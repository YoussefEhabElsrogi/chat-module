# AI Chat System

A real-time chat system that allows users to have conversations with AI (ChatGPT) with full chat history and session management.

## Features

### ✅ Implemented Features

1. **Real-time Chat Interface**
   - Modern, responsive chat UI
   - Instant message display
   - Loading indicators during AI processing
   - Message timestamps

2. **Chat Session Management**
   - Create new chat sessions
   - Switch between different chat conversations
   - Chat history sidebar
   - Automatic chat titles with timestamps

3. **AI Integration**
   - ChatGPT API integration (GPT-3.5-turbo)
   - Conversation context awareness
   - Error handling for API failures
   - Configurable AI parameters

4. **Database Storage**
   - Persistent chat history
   - User-specific chat isolation
   - Message sender tracking (user/ai)
   - Optimized database relationships

5. **Security**
   - User authentication required
   - CSRF protection
   - User ownership validation
   - Input validation and sanitization

## System Architecture

### Database Schema

#### `chats` Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `title` - Chat session title
- `created_at`, `updated_at` - Timestamps

#### `chat_messages` Table
- `id` - Primary key
- `chat_id` - Foreign key to chats table
- `sender` - Enum: 'user' or 'ai'
- `message` - Text content
- `created_at`, `updated_at` - Timestamps

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard/chat` | Chat interface page |
| POST | `/dashboard/chat/start` | Start new chat session |
| GET | `/dashboard/chat/history` | Get user's chat history |
| GET | `/dashboard/chat/{chat}/messages` | Get messages for specific chat |
| POST | `/dashboard/chat/{chat}/send` | Send message and get AI response |
| DELETE | `/dashboard/chat/{chat}` | Delete chat session |

### Frontend Components

- **ChatApp Class**: Main JavaScript application
- **Real-time Updates**: Immediate message display
- **Loading States**: Visual feedback during AI processing
- **Responsive Design**: Works on desktop and mobile

## Setup Instructions

### 1. Environment Configuration

Add to your `.env` file:
```env
OPENAI_API_KEY=your_openai_api_key_here
```

### 2. Database Migration

Run the migrations to create the required tables:
```bash
php artisan migrate
```

### 3. OpenAI API Key

1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Create an account and get your API key
3. Add the key to your `.env` file

### 4. Start the Application

```bash
php artisan serve
```

Visit: `http://localhost:8000/dashboard/chat`

## Usage

### Starting a New Chat
1. Click "New Chat" button
2. A new chat session is created
3. Start typing your message

### Sending Messages
1. Type your message in the input field
2. Press Enter or click Send
3. Your message appears immediately
4. AI response appears after processing

### Chat History
- All previous chats appear in the sidebar
- Click any chat to load its history
- Each chat maintains its own conversation context

## Technical Details

### AI Integration
- Uses OpenAI's GPT-3.5-turbo model
- Maintains conversation context (last 10 messages)
- Configurable parameters:
  - Max tokens: 1000
  - Temperature: 0.7
  - Model: gpt-3.5-turbo

### Real-time Behavior
- User messages appear instantly
- AI responses show loading indicator
- Automatic scroll to latest messages
- Input disabled during AI processing

### Error Handling
- API failures show user-friendly messages
- Network errors are caught and handled
- Invalid requests return appropriate HTTP status codes

## File Structure

```
app/
├── Models/
│   ├── Chat.php              # Chat model with relationships
│   └── ChatMessage.php       # Message model
├── Http/Controllers/Dashboard/
│   └── ChatController.php    # Main chat controller
resources/views/dashboard/chats/
└── index.blade.php           # Chat interface
database/migrations/
├── create_chats_table.php    # Chats table migration
└── create_chat_messages_table.php # Messages table migration
routes/
└── dashboard.php             # Chat routes
config/
└── services.php              # OpenAI configuration
```

## Future Enhancements

### Planned Features
- [ ] WebSocket integration for true real-time updates
- [ ] File upload support
- [ ] Message search functionality
- [ ] Chat export/import
- [ ] Multiple AI model support
- [ ] Chat sharing capabilities
- [ ] Message reactions/ratings
- [ ] Chat categories/tags

### Performance Optimizations
- [ ] Message pagination for large chats
- [ ] Redis caching for frequent requests
- [ ] Database indexing optimization
- [ ] CDN for static assets

## Troubleshooting

### Common Issues

1. **AI not responding**
   - Check OpenAI API key in `.env`
   - Verify API key has sufficient credits
   - Check Laravel logs for errors

2. **Messages not saving**
   - Ensure database migrations are run
   - Check database connection
   - Verify user authentication

3. **CSRF errors**
   - Ensure CSRF token meta tag is present
   - Check that routes are properly protected

### Debug Mode

Enable debug mode in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check logs in `storage/logs/laravel.log` for detailed error information.

## Security Considerations

- All routes require authentication
- User can only access their own chats
- Input validation prevents XSS attacks
- CSRF protection on all forms
- API rate limiting recommended for production

## License

This project is part of the chat-module system and follows the same licensing terms.
