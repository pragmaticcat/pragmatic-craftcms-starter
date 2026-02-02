<?php

namespace propis\pragmaticseo\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;

class SitemapService extends Component
{
    public function generate(): string
    {
        $sites = Craft::$app->getSites()->getAllSites();
        $primarySite = Craft::$app->getSites()->getPrimarySite();

        $entries = Entry::find()
            ->siteId($primarySite->id)
            ->status('live')
            ->all();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($entries as $entry) {
            if (!$entry->url) {
                continue;
            }

            // Collect all alternate URLs for this entry
            $alternates = [];
            foreach ($sites as $site) {
                $otherEntry = Entry::find()
                    ->id($entry->id)
                    ->siteId($site->id)
                    ->status('live')
                    ->one();

                if ($otherEntry && $otherEntry->url) {
                    $alternates[] = [
                        'lang' => $site->language,
                        'url' => $otherEntry->url,
                    ];
                }
            }

            // Output a <url> block for each site version
            foreach ($sites as $site) {
                $siteEntry = Entry::find()
                    ->id($entry->id)
                    ->siteId($site->id)
                    ->status('live')
                    ->one();

                if (!$siteEntry || !$siteEntry->url) {
                    continue;
                }

                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($siteEntry->url, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <lastmod>" . $siteEntry->dateUpdated->format('Y-m-d') . "</lastmod>\n";

                // Add hreflang alternates
                foreach ($alternates as $alt) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="' . $alt['lang'] . '" href="' . htmlspecialchars($alt['url'], ENT_XML1, 'UTF-8') . '" />' . "\n";
                }

                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
