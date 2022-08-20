<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FlexCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'sid', 'conference_id', 'status', 'direction', 'from', 'to', 'duration', 'created_at', 'updated_at'
    ];

    public function recording(): HasOne
    {
        return $this->hasOne(FlexRecording::class, 'call_id', 'id');
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(FlexConference::class, 'conference_id', 'id');
    }
}
