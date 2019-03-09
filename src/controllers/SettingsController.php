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
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use nav33d\activitylog\ActivityLog;
use nav33d\activitylog\models\Settings;

class SettingsController extends Controller
{

    // Public Methods
    // =========================================================================
    /**
     * Plugin settings
     *
     * @param null|bool|Settings $settings
     *
     * @return Response The rendered result
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionPluginSettings($settings = null): Response
    {
        $this->requirePermission('activitylog:settings');

        $variables = [];

        if ($settings === null) {
            $settings = ActivityLog::$settings;
        }
        
        /** @var Settings $settings */
        $pluginName = $settings->pluginName;
        $templateTitle = Craft::t('activitylog', 'Settings');
        
        // Basic variables
        $variables['fullPageForm'] = true;
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['crumbs'] = [
            [
                'label' => $pluginName,
                'url' => UrlHelper::cpUrl('activitylog'),
            ],
            [
                'label' => $templateTitle,
                'url' => UrlHelper::cpUrl('activitylog/settings'),
            ],
        ];
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'settings';
        $variables['settings'] = $settings;

        // Render the template
        return $this->renderTemplate('activitylog/settings', $variables);
    }


    /**
     * Saves a plugin’s settings.
     *
     * @return Response|null
     * @throws NotFoundHttpException if the requested plugin cannot be found
     * @throws \yii\web\BadRequestHttpException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionSavePluginSettings()
    {
        $this->requirePermission('activitylog:settings');
        
        $this->requirePostRequest();

        $pluginHandle = Craft::$app->getRequest()->getRequiredBodyParam('pluginHandle');
        
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);
        
        $plugin = Craft::$app->getPlugins()->getPlugin($pluginHandle);
        
        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }
        
        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));
            
            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin,
            ]);
            
            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));
        return $this->redirectToPostedUrl();
    }
}