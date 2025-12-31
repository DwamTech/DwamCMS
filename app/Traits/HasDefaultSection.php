<?php

namespace App\Traits;

use App\Models\Section;

trait HasDefaultSection
{
    /**
     * Boot the trait.
     */
    protected static function bootHasDefaultSection(): void
    {
        static::creating(function ($model) {
            if (empty($model->section_id)) {
                $model->setDefaultSection();
            }
        });

        static::updating(function ($model) {
            // Check if section_id is explicitly set to empty/null during update
            if (array_key_exists('section_id', $model->getAttributes()) && empty($model->section_id)) {
                $model->setDefaultSection();
            }
        });
    }

    /**
     * Set the default section for the model.
     */
    public function setDefaultSection(): void
    {
        $defaultSection = Section::where('slug', 'general')->first();

        if ($defaultSection) {
            $this->section_id = $defaultSection->id;
        }
    }
}
