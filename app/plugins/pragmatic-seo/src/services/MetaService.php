<?php

namespace propis\pragmaticseo\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use propis\pragmaticseo\models\SeoData;
use propis\pragmaticseo\PragmaticSeo;

class MetaService extends Component
{
    public function render(?Entry $entry = null): string
    {
        $settings = PragmaticSeo::getInstance()->getSettings();
        $html = '';

        // Build title
        $title = $this->buildTitle($entry, $settings);
        $html .= "<title>{$title}</title>\n";

        // Meta description
        $description = $this->getDescription($entry, $settings);
        if ($description) {
            $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
            $html .= "    <meta name=\"description\" content=\"{$description}\">\n";
        }

        // Canonical URL
        if ($entry && $entry->url) {
            $canonical = $entry->url;
            $html .= "    <link rel=\"canonical\" href=\"{$canonical}\">\n";
        }

        // Hreflang tags
        if ($entry) {
            $html .= $this->renderHreflangTags($entry);
        }

        return $html;
    }

    public function buildTitle(?Entry $entry, $settings): string
    {
        $siteName = Craft::$app->getSystemName();
        $separator = $settings->titleSeparator;

        $seoTitle = null;
        if ($entry) {
            $seoData = $this->getSeoData($entry);
            if ($seoData && $seoData->seoTitle) {
                $seoTitle = $seoData->seoTitle;
            } else {
                $seoTitle = $entry->title;
            }
        }

        if (!$seoTitle) {
            return $siteName;
        }

        if (!$settings->appendSiteName) {
            return htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8');
        }

        $seoTitle = htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8');
        $separator = htmlspecialchars($separator, ENT_QUOTES, 'UTF-8');
        $siteName = htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');

        if ($settings->siteNamePosition === 'before') {
            return "{$siteName} {$separator} {$seoTitle}";
        }

        return "{$seoTitle} {$separator} {$siteName}";
    }

    public function getDescription(?Entry $entry, $settings): ?string
    {
        if ($entry) {
            $seoData = $this->getSeoData($entry);
            if ($seoData && $seoData->seoDescription) {
                return $seoData->seoDescription;
            }
        }

        return $settings->defaultDescription ?: null;
    }

    public function renderHreflangTags(Entry $entry): string
    {
        $html = '';
        $sites = Craft::$app->getSites()->getAllSites();

        foreach ($sites as $site) {
            $otherEntry = Entry::find()
                ->id($entry->id)
                ->siteId($site->id)
                ->status('live')
                ->one();

            if ($otherEntry && $otherEntry->url) {
                $lang = $site->language;
                $url = $otherEntry->url;
                $html .= "    <link rel=\"alternate\" hreflang=\"{$lang}\" href=\"{$url}\">\n";
            }
        }

        // x-default pointing to primary site
        $primarySite = Craft::$app->getSites()->getPrimarySite();
        $primaryEntry = Entry::find()
            ->id($entry->id)
            ->siteId($primarySite->id)
            ->status('live')
            ->one();

        if ($primaryEntry && $primaryEntry->url) {
            $html .= "    <link rel=\"alternate\" hreflang=\"x-default\" href=\"{$primaryEntry->url}\">\n";
        }

        return $html;
    }

    private function getSeoData(Entry $entry): ?SeoData
    {
        foreach ($entry->getFieldLayout()?->getCustomFields() ?? [] as $field) {
            if ($field instanceof \propis\pragmaticseo\fields\SeoField) {
                $value = $entry->getFieldValue($field->handle);
                if ($value instanceof SeoData) {
                    return $value;
                }
            }
        }

        return null;
    }
}
