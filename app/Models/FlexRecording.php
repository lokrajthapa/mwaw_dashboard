<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlexRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'sid', 'conference_sid', 'conference_id', 'call_sid', 'call_id', 'account_sid', 'duration', 'source', 'media_url', 'json',
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'json' => 'array'
    ];

    public function call(): BelongsTo
    {
        return $this->belongsTo(FlexCall::class, 'call_id', 'id');
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(FlexCall::class, 'conference_id', 'id');
    }
}
