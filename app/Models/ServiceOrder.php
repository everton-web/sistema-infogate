<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceOrder extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'vehicle_id',
        'number',
        'status',
        'priority',
        'complaint',
        'diagnosis',
        'internal_notes',
        'total_products',
        'total_services',
        'discount',
        'total',
        'opened_at',
        'started_at',
        'completed_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'total_products' => 'decimal:2',
            'total_services' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'opened_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceOrderItem::class);
    }

    public function recalculateTotals(): void
    {
        $this->total_products = $this->items()->where('type', 'product')->sum('total');
        $this->total_services = $this->items()->where('type', 'service')->sum('total');
        $this->total = $this->total_products + $this->total_services - $this->discount;
        $this->save();
    }

    public static function nextNumber(int $companyId): string
    {
        $last = static::where('company_id', $companyId)
            ->orderByDesc('id')
            ->value('number');

        $next = $last ? ((int) $last) + 1 : 1;

        return str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Aberta',
            'in_progress' => 'Em andamento',
            'waiting_parts' => 'Aguardando peças',
            'waiting_approval' => 'Aguardando aprovação',
            'completed' => 'Concluída',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelada',
            default => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'open' => 'waiting-pill',
            'in_progress' => 'info-pill',
            'waiting_parts', 'waiting_approval' => 'warning-pill',
            'completed', 'delivered' => 'success-pill',
            'cancelled' => 'danger-pill',
            default => 'neutral-pill',
        };
    }

    public function isFinalized(): bool
    {
        return in_array($this->status, ['delivered', 'cancelled'], true);
    }
}
