<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\activitylog\controllers;

use Craft;
use craft\db\Query;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\ForbiddenHttpException;
use yii\web\Response;

use nav33d\activitylog\assetbundles\ActivityLogAsset;
use nav33d\activitylog\ActivityLog as ActivityLogPlugin;

class DashboardController extends Controller
{
    public function beforeAction($action)
    {
        $this->view->registerAssetBundle(ActivityLogAsset::class);
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $settings = ActivityLogPlugin::$settings;
        $pluginName = $settings->pluginName;

        $templateTitle = Craft::t('activitylog', 'Dashboard');

        $variables['title'] = $templateTitle;
        $variables['crumbs'] = [
            [
                'label' => $pluginName,
                'url' => UrlHelper::cpUrl('activitylog'),
            ],
        ];

        $variables['baseAssetsUrl'] = Craft::$app->assetManager->getPublishedUrl(
            '@nav33d/activitylog/assetbundles/dist',
            true
        );

        $this->renderTemplate('activitylog/dashboard/_index', $variables);
    }

}
