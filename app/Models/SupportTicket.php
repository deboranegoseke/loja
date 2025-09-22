<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected $table = 'support_tickets';

    protected $fillable = [
        'user_id',
        'order_id',
        'status',     // pode vir 'open', 'aberto', 'closed', 'fechado'
        'subject',    // sua tabela tem essa coluna (nullable)
        'closed_at',
    ];

    protected $casts = [
        'closed_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Para aparecer como propriedade ($ticket->status_label) e em toArray()/JSON
    protected $appends = ['status_label', 'is_open'];

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

    // --- SCOPES ÚTEIS ---
    public function scopeOpen($q)
    {
        return $q->whereIn('status', ['open', 'aberto']);
    }

    public function scopeClosed($q)
    {
        return $q->whereIn('status', ['closed', 'fechado']);
    }

    // --- ACCESSORS / COMPUTEDS ---
    public function getStatusLabelAttribute(): string
    {
        $status = strtolower((string) $this->status);

        return match ($status) {
            'open', 'aberto'     => 'Aberto',
            'closed', 'fechado'  => 'Fechado',
            default              => ucfirst($status),
        };
    }

    public function getIsOpenAttribute(): bool
    {
        return in_array(strtolower((string) $this->status), ['open', 'aberto'], true);
    }
}
