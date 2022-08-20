<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FlexTask extends Model
{
    use HasFactory;

    protected $fillable = ['sid', 'age', 'status', 'attributes', 'queue_name', 'channel_name', 'created_at'];

    protected $casts = [
        'attributes' => 'array'
    ];

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_worker', 'task_id', 'worker_id');
    }
}
