<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    HasOne
};


class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
    ];

    # START RELATIONS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }
    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }
    # END RELATIONS
}
