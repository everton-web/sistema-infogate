<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'company_id',
        'customer_id',
        'vehicle_brand_id',
        'vehicle_model_id',
        'plate',
        'version',
        'year_manufacture',
        'year_model',
        'color',
        'chassis',
        'odometer',
        'notes',
        'status',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(
            VehicleBrand::class,
            'vehicle_brand_id'
        );
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(
            VehicleModel::class,
            'vehicle_model_id'
        );
    }
}
