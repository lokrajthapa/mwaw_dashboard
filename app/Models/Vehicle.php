<?php

namespace App\Models;

use App\Fordpass\FordpassVehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'vin', 'user_id'];

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(VehicleHistory::class);
    }

    public function latestStatus(): HasMany
    {
        return $this->histories()->orderByDesc('created_at')->limit(1);
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getCurrentStatus(): array
    {
        $fordpassVehicle = new FordpassVehicle($this->vin);
        return $fordpassVehicle->status();
    }

}
