<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'sender_type',
        'body',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
