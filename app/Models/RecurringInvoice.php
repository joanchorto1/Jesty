<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'client_id',
        'expected_total',
        'status',
        'invoice_state',
        'frequency_unit',
        'frequency_interval',
        'first_issue_on',
        'next_run_at',
        'last_run_at',
        'ends_at',
        'active',
    ];

    protected $casts = [
        'expected_total' => 'float',
        'first_issue_on' => 'date',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'ends_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecurringInvoiceItem::class);
    }

    public function generatedInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', true)->where('status', 'active');
    }

    public function markNextRunFrom(Carbon $from): void
    {
        $this->next_run_at = self::calculateNextRunDate($from, $this->frequency_unit, (int) $this->frequency_interval);
    }

    public static function calculateNextRunDate(Carbon $from, string $unit, int $interval): Carbon
    {
        $date = $from->copy();

        return match ($unit) {
            'day', 'daily' => $date->addDays($interval),
            'week', 'weekly' => $date->addWeeks($interval),
            'month', 'monthly' => $date->addMonths($interval),
            'year', 'annually', 'yearly' => $date->addYears($interval),
            default => $date->addMonths($interval),
        };
    }
}
