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

class LogsController extends Controller
{
    public function beforeAction($action)
    {
        $this->view->registerAssetBundle(ActivityLogAsset::class);
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->requirePermission('activitylog:view-logs');

        $settings = ActivityLogPlugin::$settings;
        $pluginName = $settings->pluginName;

        $templateTitle = Craft::t('activitylog', 'Logs');

        $variables['title'] = $templateTitle;
        $variables['crumbs'] = [
            [
                'label' => $pluginName,
                'url' => UrlHelper::cpUrl('activitylog'),
            ],
        ];

        $variables['logsLimit'] = $settings->logsLimit;

        // Check last deleted cache key exists
        $cache = Craft::$app->getCache();
        $lastDeleted = $cache->get('activitylog_logs_last_deleted');
        $variables['lastDeleted'] = $lastDeleted;

        $this->renderTemplate('activitylog/logs/_index', $variables);
    }

    public function actionView($id)
    {
        $this->requirePermission('activitylog:view-logs');

        $settings = ActivityLogPlugin::$settings;
        $pluginName = $settings->pluginName;
        
        $templateTitle = Craft::t('activitylog', 'Logs');

        $variables['title'] = $templateTitle;
        $variables['crumbs'] = [
            [
                'label' => $pluginName,
                'url' => UrlHelper::cpUrl('activitylog'),
            ],
        ];

        $log = ActivityLogPlugin::$plugin->activitylog->getById($id);
        $variables['log'] = $log;

        if ( $log && $log->title )
        {
            $variables['title'] = $log->title;
        }

        $this->view->registerCssFile("//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/styles/default.min.css");

        $this->renderTemplate('activitylog/logs/_view', $variables);
    }

    public function actionGetAll($per_page = 10, $page = 1, $sort = 'dateCreated|desc', $filter = '', $action_filter = '') : Response
    {
        $this->requirePermission('activitylog:view-logs');

        $data = ActivityLogPlugin::$plugin->activitylog->getAll($per_page, $page, $sort, $filter, $action_filter);   
        return $this->asJson($data);
    }


    public function actionPrune() : Response
    {
        $this->requirePermission('activitylog:prune-logs');

        $rows = ActivityLogPlugin::$plugin->activitylog->prune();
        return $this->asJson(['rows' => $rows]);
    }


    public function actionDeleteAll() : Response
    {
        $this->requirePermission('activitylog:delete-logs');

        $rows = ActivityLogPlugin::$plugin->activitylog->deleteAll();
        return $this->asJson(['rows' => $rows]);
    }
}
