<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'category_id',
        'phone_no',
        'street_address',
        'locality',
        'province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'sf_id',
        'agent_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['display_role'];

    protected function displayRole(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => Str::headline($attributes['role'])
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class);
    }

    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class, 'user_id', 'id');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(FlexTask::class, 'task_worker', 'worker_id', 'task_id');
    }
}
