<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    use HasFactory;

    protected $table = 'support_messages';

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'sender_type',   // 'store' | 'customer'
        'body',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array', // sua coluna é JSON (CHECK json_valid)
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
