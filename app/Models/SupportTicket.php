<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'status',
        'closed_at',
        // 'subject',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    // Para aparecer como propriedade ($ticket->status_label) e em toArray()/JSON
    protected $appends = ['status_label'];

    // --- RELACIONAMENTOS ---
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'support_ticket_id')
            ->orderBy('created_at');
    }

    // --- ACCESSORS ---
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            // 'Open' grava no BD e na view aparece 'Aberto'
            'open'     => 'Aberto', 
            // 'closed' grava no BD e na view aparece 'Fechado'
            'closed'   => 'Fechado',
            default    => ucfirst((string) $this->status),
        };
    }
}
