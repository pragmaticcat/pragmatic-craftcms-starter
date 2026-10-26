<?php

namespace craft\contentmigrations;

use Craft;
use craft\db\Migration;
use craft\elements\Entry;
use DateTime;
use RuntimeException;

class m261026_130000_create_actualitat_page extends Migration
{
    public function safeUp(): bool
    {
        $site = Craft::$app->getSites()->getPrimarySite();
        $existing = Entry::find()
            ->section('page')
            ->slug('actualitat')
            ->siteId($site->id)
            ->status(null)
            ->one();

        if ($existing) {
            // Keep any editorial content that already owns this URL.
            return true;
        }

        $section = Craft::$app->getEntries()->getSectionByHandle('page');
        $type = Craft::$app->getEntries()->getEntryTypeByHandle('page');
        if (!$section || !$type) {
            throw new RuntimeException('The page section and entry type must exist before creating Actualitat.');
        }

        $page = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'siteId' => $site->id,
            'title' => 'Actualitat',
            'slug' => 'actualitat',
            'enabled' => true,
            'postDate' => new DateTime(),
        ]);
        $page->setFieldValue('slices', [
            'new1' => [
                'type' => 'sliceActualitat',
                'title' => 'Actualitat',
                'fields' => [],
            ],
        ]);

        if (!Craft::$app->getElements()->saveElement($page)) {
            throw new RuntimeException('Could not create Actualitat: ' . json_encode($page->getErrors()));
        }

        return true;
    }

    public function safeDown(): bool
    {
        // The page may contain edits after installation; rollback must preserve them.
        return true;
    }
}
