<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'fulfillment_status',
        'total',
        'pix_txid',
        'pix_payload',
        'customer_name',
        'customer_email',
        'tracking_code',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    protected $appends = ['status_label'];

    // --- Relacionamentos ---
    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Helpers ---
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    // Label PT-BR do PAGAMENTO
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Pendente',
            'paid'      => 'Pago',
            'cancelled' => 'Cancelado',
            default     => ucfirst((string) $this->status),
        };
    }

    // Label do RASTREIO (rota_entrega removido)
    public function getFulfillmentStatusLabelAttribute(): string
    {
        return match ($this->fulfillment_status) {
            'separacao'     => 'Separação',
            'em_transito'   => 'Em trânsito',
            'entregue'      => 'Entregue',
            'problema'      => 'Ocorrência',
            'cancelado'     => 'Cancelado',
            default         => 'Aguardando',
        };
    }

    // Classe visual (badge) do RASTREIO (rota_entrega removido)
    public function getFulfillmentBadgeClassAttribute(): string
    {
        return match ($this->fulfillment_status) {
            'separacao'     => 'bg-amber-100 text-amber-800',
            'em_transito'   => 'bg-blue-100 text-blue-800',
            'entregue'      => 'bg-green-100 text-green-800',
            'problema'      => 'bg-red-100 text-red-800',
            'cancelado'     => 'bg-gray-200 text-gray-700',
            default         => 'bg-gray-100 text-gray-800',
        };
    }
}
