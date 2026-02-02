<?php

namespace propis\pragmaticseo;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use propis\pragmaticseo\fields\SeoField;
use propis\pragmaticseo\models\Settings;
use propis\pragmaticseo\services\MetaService;
use propis\pragmaticseo\services\SitemapService;
use propis\pragmaticseo\variables\PragmaticSeoVariable;
use yii\base\Event;

/**
 * @property MetaService $meta
 * @property SitemapService $sitemap
 */
class PragmaticSeo extends Plugin
{
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'meta' => MetaService::class,
                'sitemap' => SitemapService::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Register field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = SeoField::class;
            }
        );

        // Register site URL rules
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['sitemap.xml'] = 'pragmatic-seo/sitemap/index';
                $event->rules['robots.txt'] = 'pragmatic-seo/robots/index';
            }
        );

        // Register Twig variable
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $event->sender->set('pragmaticSeo', PragmaticSeoVariable::class);
            }
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'pragmatic-seo/_settings',
            ['settings' => $this->getSettings()]
        );
    }
}
