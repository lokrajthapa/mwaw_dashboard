<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobNumberAlert extends Model
{
    use HasFactory;

    protected $fillable = ['no_of_jobs', 'days', 'condition', 'category_id', 'receivers', 'last_alert'];

    protected $casts = [
        'receivers' => 'array'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
