<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'cost_price',
        'stock',
        'image_url',
        'image_path',
        'active',
        'sku',
    ];

    protected $casts = [
        'active'      => 'boolean',
        'price'       => 'decimal:2',
        'cost_price'  => 'decimal:2',
        'stock'       => 'integer',
    ];

    // Para aparecer automaticamente em arrays/JSON (opcional)
    protected $appends = [
        'cover_url',
        // 'margin_percent', // habilite se quiser expor via JSON
    ];

    /**
     * URL da capa do produto:
     * 1) usa arquivo local (storage/app/public/...) se existir
     * 2) senão usa image_url
     * 3) senão cai num placeholder
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->image_path) {
            $path = ltrim($this->image_path, '/');
            if (Storage::disk('public')->exists($path)) {
                return Storage::url($path); // => /storage/...
            }
        }

        return $this->image_url
            ?? 'https://picsum.photos/seed/p'.$this->id.'/640/480';
    }

    /**
     * Margem estimada em %, apenas para áreas internas.
     */
    public function getMarginPercentAttribute(): ?float
    {
        $price = (float) $this->price;
        $cost  = (float) $this->cost_price;

        if ($price <= 0) {
            return null;
        }

        return round((($price - $cost) / $price) * 100, 2);
    }

    /**
     * Escopo: somente produtos ativos.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
