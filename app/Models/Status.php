<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'sf_id', 'color'];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
