<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FlexConference extends Model
{
    use HasFactory;

    protected $fillable = ['sid', 'direction', 'created_at'];

    public function calls(): HasMany
    {
        return $this->hasMany(FlexCall::class, 'conference_id', 'id');
    }

    public function recording(): HasOne
    {
        return $this->hasOne(FlexRecording::class, 'conference_id', 'id');
    }
}
