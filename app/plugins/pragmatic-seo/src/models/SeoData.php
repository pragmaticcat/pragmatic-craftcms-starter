<?php

namespace propis\pragmaticseo\models;

use craft\base\Model;

class SeoData extends Model
{
    public ?string $seoTitle = null;
    public ?string $seoDescription = null;

    public function defineRules(): array
    {
        return [
            ['seoTitle', 'string', 'max' => 70],
            ['seoDescription', 'string', 'max' => 160],
        ];
    }
}
