<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
};

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender',
        'message',
    ];


    # START RELATIONS
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
    # END RELATIONS

    # START ATTRIBUTES
    public function isUser(): bool
    {
        return $this->sender === 'user';
    }
    public function isAi(): bool
    {
        return $this->sender === 'ai';
    }
    # END ATTRIBUTES
}
