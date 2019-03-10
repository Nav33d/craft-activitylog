<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\activitylog;

use Craft;
use craft\web\UrlManager;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\services\Elements;
use craft\services\UserPermissions;
use craft\events\PluginEvent;
use craft\events\ElementEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;

use yii\base\Event;
use yii\web\User;
use yii\web\UserEvent;

/**
 * @author    Naveed Ziarab
 * @package   Activity Log
 * @since     1.0.0
 */

class ActivityLog extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;

    public static $settings;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Initialize properties
        self::$settings = $this->getSettings();
        $this->name = self::$settings->pluginName;

        // Register services
        $this->setComponents([
            'activitylog' => \nav33d\activitylog\services\ActivityLog::class,
        ]);

        $request = Craft::$app->getRequest();
        if ( $request->getIsCpRequest() )
        {
            // Redirect to dashboard after install
            Event::on(
                Plugins::class,
                Plugins::EVENT_AFTER_INSTALL_PLUGIN,
                function(PluginEvent $event) {
                    if ( $event->plugin === $this )
                    {
                        $request = Craft::$app->getRequest();
                        if ($request->isCpRequest) {
                            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('activitylog/dashboard'))->send();
                        }
                    }
                }
            );

            $this->setupEventListeners();
        }

        // Control panel request
        if ($request->getIsCpRequest() && !$request->getIsConsoleRequest())
        {
            // Register CP routes
            Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
              $event->rules = array_merge($event->rules, $this->getCpUrlRules());
            });

            // Register custom user permissions
            Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function (RegisterUserPermissionsEvent $event) {
                // Register our custom permissions
                $event->permissions[Craft::t('activitylog', 'Activity Log')] = $this->customAdminCpPermissions();
            });
        }
    }


    /**
     * Plugin settings model
     */
    protected function createSettingsModel()
    {
        return new \nav33d\activitylog\models\Settings();
    }


    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('activitylog/settings'));
    }


    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        $subNavs = [];
        $navItem = parent::getCpNavItem();

        /** @var User $currentUser */
        $currentUser = Craft::$app->getUser()->getIdentity();

        // Only show sub-navs the user has permission to view
        if ($currentUser->can('activitylog:view-logs')) 
        {
            $subNavs['activitylog'] = [
                'label' => 'Logs',
                'url'   => 'activitylog',
            ];
        }

        if ($currentUser->can('activitylog:settings')) 
        {
            $subNavs['settings'] = [
            'label' => 'Settings',
            'url'   => 'activitylog/settings',
            ];
        }

        $navItem = array_merge($navItem, [
            'subnav' => $subNavs,
        ]);

        return $navItem;
    }


    /**
     * Determine whether our table schema exists or not; this is needed because
     * migrations such as the install migration and base_install migration may
     * not have been run by the time our init() method has been called
     *
     * @return bool
     */
    protected function tableSchemaExists(): bool
    {
        return (Craft::$app->db->schema->getTableSchema('{{%activitylog_logs}}') !== null);
    }


    /**
     * Install our event listeners.
     */
    protected function setupEventListeners()
    {
        // Setup our event listeners only if our table schema exists
        if( $this->tableSchemaExists() )
        {
            /**
             * Plugin events
             */
            if ( self::$settings->logPluginEvents )
            {
                Event::on(
                    Plugins::class,
                    Plugins::EVENT_AFTER_INSTALL_PLUGIN,
                    function(PluginEvent $event) {
                        $this->activitylog->handlePluginEvent($event->plugin, 'plugin-installed');
                    }
                );

                Event::on(
                    Plugins::class,
                    Plugins::EVENT_AFTER_UNINSTALL_PLUGIN,
                    function(PluginEvent $event) {
                        if ( $event->plugin != $this )
                        {
                            $this->activitylog->handlePluginEvent($event->plugin, 'plugin-uninstalled');
                        }
                    }
                );

                Event::on(
                    Plugins::class,
                    Plugins::EVENT_AFTER_DISABLE_PLUGIN,
                    function(PluginEvent $event) {
                        $this->activitylog->handlePluginEvent($event->plugin, 'plugin-disabled');
                    }
                );

                Event::on(
                    Plugins::class,
                    Plugins::EVENT_AFTER_ENABLE_PLUGIN,
                    function(PluginEvent $event) {
                        $this->activitylog->handlePluginEvent($event->plugin, 'plugin-enabled');
                    }
                );
            }

            /**
             * Auth events
             */
            if ( self::$settings->logAuthEvents )
            {
                Event::on(
                    User::class,
                    User::EVENT_AFTER_LOGIN,
                    function(UserEvent $event) {
                        $this->activitylog->handleAuthEvent($event->identity, 'logged-in');
                    }
                );

                Event::on(
                    User::class,
                    User::EVENT_BEFORE_LOGOUT,
                    function(UserEvent $event) {
                        $this->activitylog->handleAuthEvent($event->identity, 'logged-out');
                    }
                );
            }

            /**
             * Element events
             */
            if ( self::$settings->logElementEvents )
            {
                Event::on(
                    Elements::class,
                    Elements::EVENT_AFTER_SAVE_ELEMENT,
                    function(ElementEvent $event) {
                        $this->activitylog->handleElementEvent($event);
                    }
                );

                Event::on(
                    Elements::class,
                    Elements::EVENT_AFTER_DELETE_ELEMENT,
                    function(ElementEvent $event) {
                        $this->activitylog->handleElementEvent($event);
                    }
                );
            }
        }
    }


    /**
     * @return array
     */
    private function getCpUrlRules()
    {
      return [
        'activitylog' => 'activitylog/logs/index',
        'activitylog/<id:\d+>' => 'activitylog/logs/view',

        'activitylog/dashboard' => 'activitylog/dashboard/index',

        'activitylog/settings' => 'activitylog/settings/plugin-settings',
      ];
    }


    /**
     * Returns the custom Control Panel user permissions.
     *
     * @return array
     */
    protected function customAdminCpPermissions(): array
    {
        return [
            'activitylog:view-logs' => [
                'label' => Craft::t('activitylog', 'View logs'),
            ],
            'activitylog:prune-logs' => [
                'label' => Craft::t('activitylog', 'Prune logs'),
            ],
            'activitylog:delete-logs' => [
                'label' => Craft::t('activitylog', 'Delete logs'),
            ],
            'activitylog:settings' => [
                'label' => Craft::t('activitylog', 'Settings'),
            ],
        ];
    }

}
