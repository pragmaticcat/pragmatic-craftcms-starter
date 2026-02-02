<?php

namespace propis\pragmaticseo\models;

use craft\base\Model;

class Settings extends Model
{
    public string $titleSeparator = '|';
    public string $siteNamePosition = 'after';
    public bool $appendSiteName = true;
    public string $defaultDescription = '';
    public string $robotsTxt = "User-agent: *\nAllow: /\n\nSitemap: {{ siteUrl }}sitemap.xml";

    public function defineRules(): array
    {
        return [
            [['titleSeparator', 'siteNamePosition'], 'required'],
            ['siteNamePosition', 'in', 'range' => ['before', 'after']],
            ['titleSeparator', 'string', 'max' => 10],
            ['defaultDescription', 'string', 'max' => 320],
        ];
    }
}
