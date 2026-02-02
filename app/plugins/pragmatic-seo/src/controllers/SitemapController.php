<?php

namespace propis\pragmaticseo\controllers;

use craft\web\Controller;
use propis\pragmaticseo\PragmaticSeo;
use yii\web\Response;

class SitemapController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionIndex(): Response
    {
        $xml = PragmaticSeo::getInstance()->sitemap->generate();

        $response = \Craft::$app->getResponse();
        $response->format = Response::FORMAT_RAW;
        $response->getHeaders()->set('Content-Type', 'application/xml; charset=UTF-8');
        $response->data = $xml;

        return $response;
    }
}
