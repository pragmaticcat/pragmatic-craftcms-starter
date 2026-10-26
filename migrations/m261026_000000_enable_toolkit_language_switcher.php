<?php

namespace craft\contentmigrations;

use craft\db\Migration;
use pragmatic\webtoolkit\PragmaticWebToolkit;
use RuntimeException;

class m261026_000000_enable_toolkit_language_switcher extends Migration
{
    public function safeUp(): bool
    {
        $plugin = \Craft::$app->getPlugins()->getPlugin('pragmatic-web-toolkit');
        if (!$plugin instanceof PragmaticWebToolkit) {
            throw new RuntimeException('Pragmatic Web Toolkit must be installed before enabling the language switcher.');
        }

        $providers = $plugin->domains->all();
        $configuration = $plugin->domainConfig->getConfiguration($providers);
        $configuration['languageRedirect']['enabled'] = true;

        if (!$plugin->domainConfig->saveConfiguration($providers, $configuration)) {
            throw new RuntimeException('Could not enable the Pragmatic Web Toolkit language switcher.');
        }

        return true;
    }

    public function safeDown(): bool
    {
        return true;
    }
}
