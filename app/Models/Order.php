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


    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }


    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    // Label amigável do rastreio
    public function getFulfillmentStatusLabelAttribute(): string
    {
        return match ($this->fulfillment_status) {
            'separacao'     => 'Separação',
            'em_transito'   => 'Em trânsito',
            'rota_entrega'  => 'Rota de entrega',
            'entregue'      => 'Entregue',
            'problema'      => 'Ocorrência',
            'cancelado'     => 'Cancelado',
            default         => 'Aguardando',
        };
    }

    // Classe visual (badge)
    public function getFulfillmentBadgeClassAttribute(): string
    {
        return match ($this->fulfillment_status) {
            'separacao'     => 'bg-amber-100 text-amber-800',
            'em_transito'   => 'bg-blue-100 text-blue-800',
            'rota_entrega'  => 'bg-indigo-100 text-indigo-800',
            'entregue'      => 'bg-green-100 text-green-800',
            'problema'      => 'bg-red-100 text-red-800',
            'cancelado'     => 'bg-gray-200 text-gray-700',
            default         => 'bg-gray-100 text-gray-800',
        };
    }
}
