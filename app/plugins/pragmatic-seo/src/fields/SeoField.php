<?php

namespace propis\pragmaticseo\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;
use propis\pragmaticseo\models\SeoData;
use yii\db\Schema;

class SeoField extends Field
{
    public static function displayName(): string
    {
        return 'Pragmatic SEO';
    }

    public static function valueType(): string
    {
        return SeoData::class . '|null';
    }

    public function getContentColumnType(): array|string
    {
        return Schema::TYPE_JSON;
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element = null): ?SeoData
    {
        if ($value instanceof SeoData) {
            return $value;
        }

        $data = new SeoData();

        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }

        if (is_array($value)) {
            $data->seoTitle = $value['seoTitle'] ?? null;
            $data->seoDescription = $value['seoDescription'] ?? null;
        }

        return $data;
    }

    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        if ($value instanceof SeoData) {
            return [
                'seoTitle' => $value->seoTitle,
                'seoDescription' => $value->seoDescription,
            ];
        }

        return $value;
    }

    protected function inputHtml(mixed $value, ?ElementInterface $element, bool $inline): string
    {
        if (!$value instanceof SeoData) {
            $value = new SeoData();
        }

        return Craft::$app->getView()->renderTemplate(
            'pragmatic-seo/_field/input',
            [
                'field' => $this,
                'value' => $value,
                'namePrefix' => $this->handle,
            ]
        );
    }
}
