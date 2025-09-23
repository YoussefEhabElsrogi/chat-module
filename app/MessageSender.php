<?php

namespace App;

enum MessageSender: string
{
    case USER = 'user';
    case AI = 'ai';
    case ASSISTANT = 'assistant';
}
