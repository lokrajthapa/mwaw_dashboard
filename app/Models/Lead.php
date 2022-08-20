<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'gclid', 'source', 'email', 'phone_no', 'conversion_datetime',
        'conversion_value', 'conversion_currency', 'job_id', 'uploaded'
    ];

    protected $casts = [
        'uploaded' => 'boolean'
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
