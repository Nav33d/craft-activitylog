<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\activitylog\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var string The public-facing name of the plugin
     */
    public $pluginName = 'Activity Log';

    /**
     * @var bool Should the plugin events *(installed, uninstalled, enabled, disabled)* be recorded?
     */
    public $logPluginEvents = true;

    /**
     * @var bool Should the auth events *(login, logout)* be recorded?
     */
    public $logAuthEvents = true;

    /**
     * @var bool Should the element events *(create, update, delete)* be recorded?
     */
    public $logElementEvents = true;

    /**
     * @var int How many days of logs should be kept?
     */
    public $logsLimit = 30;

    public function rules()
    {
        return [
            ['pluginName', 'required'],
            ['pluginName', 'string'],
            [['logPluginEvents', 'logAuthEvents', 'logElementEvents'], 'boolean'],
            ['logsLimit', 'integer', 'min' => 1],
            ['logsLimit', 'default', 'value' => 30],
        ];
    }
}