<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'due_date',
        'status',
        'taskable_id',
        'taskable_type',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    protected $appends = [
        'taskable_label',
        'taskable_type_label',
        'taskable_type_key',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTaskableLabelAttribute(): ?string
    {
        $taskable = $this->relationLoaded('taskable') ? $this->getRelation('taskable') : $this->taskable;

        if (! $taskable) {
            return null;
        }

        foreach (['name', 'title', 'description'] as $attribute) {
            if (! empty($taskable->{$attribute})) {
                return $attribute === 'description'
                    ? Str::limit($taskable->{$attribute}, 60)
                    : $taskable->{$attribute};
            }
        }

        return '#' . $taskable->getKey();
    }

    public function getTaskableTypeLabelAttribute(): ?string
    {
        if (! $this->taskable_type) {
            return null;
        }

        return Str::headline(class_basename($this->taskable_type));
    }

    public function getTaskableTypeKeyAttribute(): ?string
    {
        if (! $this->taskable_type) {
            return null;
        }

        return Str::snake(class_basename($this->taskable_type));
    }
}
