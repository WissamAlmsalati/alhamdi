<?php

namespace App\Models\Concerns;

/**
 * Display helpers for bilingual records: $model->display_name / display_description /
 * display_title return the English value when the app locale is English and it's filled in,
 * otherwise the Arabic value.
 */
trait HasLocalizedFields
{
    public function localized(string $field): ?string
    {
        $english = $this->getAttribute($field.'_en');

        return app()->getLocale() === 'en' && filled($english) ? $english : $this->getAttribute($field);
    }

    public function getDisplayNameAttribute(): ?string
    {
        return $this->localized('name');
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        return $this->localized('description');
    }

    public function getDisplayTitleAttribute(): ?string
    {
        return $this->localized('title');
    }

    public function getDisplayIssuerAttribute(): ?string
    {
        return $this->localized('issuer');
    }
}
