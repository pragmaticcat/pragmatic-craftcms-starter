<?php

namespace propis\pragmaticseo\variables;

use craft\elements\Entry;
use propis\pragmaticseo\PragmaticSeo;

class PragmaticSeoVariable
{
    public function render(?Entry $entry = null): string
    {
        return PragmaticSeo::getInstance()->meta->render($entry);
    }
}
