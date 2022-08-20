<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_no',
        'street_address',
        'locality',
        'province',
        'postal_code',
        'country',
        'buzzer',
        'latitude',
        'longitude',
        'authorized',
        'owner_first_name',
        'owner_last_name',
        'owner_phone_no',
        'owner_email',
        'sf_id'
    ];

    protected $casts = [
        'authorized' => 'boolean'
    ];

    protected $appends = ['full_name'];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['first_name'] . ' ' . $attributes['last_name']
        );
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
