<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Job extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'category_id', 'sub_category_id', 'status_id', 'customer_id',
        'sf_id', 'sf_job_number', 'created_at', 'updated_at', 'start_date', 'end_date',
        'start_time', 'end_time', 'po'];

    protected $appends = ['start_datetime', 'end_datetime'];

    public function startDatetime(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => ($attributes['start_date'] && $attributes['start_time']) ?
            new Carbon($attributes['start_date'] . ' ' . $attributes['start_time']
            ) : null);
    }

    public function endDatetime(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => ($attributes['start_date'] && $attributes['end_time']) ?
            new Carbon(($attributes['end_date'] ?: $attributes['start_date']) . ' ' . $attributes['end_time']
            ) : null);
    }

    public function technicians(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }
}
