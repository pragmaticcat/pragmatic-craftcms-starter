<?php

namespace propis\pragmaticseo\controllers;

use Craft;
use craft\web\Controller;
use propis\pragmaticseo\PragmaticSeo;
use yii\web\Response;

class RobotsController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionIndex(): Response
    {
        $settings = PragmaticSeo::getInstance()->getSettings();
        $content = $settings->robotsTxt;

        // Replace {{ siteUrl }} placeholder with actual site URL
        $siteUrl = Craft::$app->getSites()->getCurrentSite()->getBaseUrl();
        $content = str_replace('{{ siteUrl }}', $siteUrl, $content);

        $response = Craft::$app->getResponse();
        $response->format = Response::FORMAT_RAW;
        $response->getHeaders()->set('Content-Type', 'text/plain; charset=UTF-8');
        $response->data = $content;

        return $response;
    }
}
