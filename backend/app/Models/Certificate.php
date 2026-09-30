<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasLocalizedFields;

    /** Days before expiry when a certificate is flagged as "expiring soon". */
    public const EXPIRY_WARNING_DAYS = 60;

    protected $fillable = [
        'title',
        'title_en',
        'issuer',
        'issuer_en',
        'number',
        'issued_at',
        'expires_at',
        'file',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * valid | expiring | expired | open (no expiry date)
     */
    public function validityStatus(): string
    {
        if (! $this->expires_at) {
            return 'open';
        }

        if ($this->expires_at->isPast() && ! $this->expires_at->isToday()) {
            return 'expired';
        }

        if ($this->expires_at->lte(now()->addDays(self::EXPIRY_WARNING_DAYS))) {
            return 'expiring';
        }

        return 'valid';
    }

    public function isPdf(): bool
    {
        return $this->file && str_ends_with(strtolower($this->file), '.pdf');
    }
}
