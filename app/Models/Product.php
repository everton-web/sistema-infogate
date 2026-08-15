<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'name',
        'sku',
        'barcode',
        'unit',
        'description',
        'cost_price',
        'sale_price',
        'stock_quantity',
        'stock_minimum',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_quantity' => 'decimal:2',
            'stock_minimum' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
