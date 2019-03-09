<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2018 Nav33d
 */

namespace nav33d\activitylog\models;

use Craft;
use DateTime;
use craft\base\Model;
use craft\elements\Asset;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use craft\base\ElementInterface;

use nav33d\activitylog\records\ActivityLog as ActivityLogRecord;

class ActivityLog extends Model
{

    // Public Properties
    // =========================================================================

    /**
     * @var integer|null
     */
    public $id = null;

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var integer|null
     */
    public $elementId = null;

    /**
     * @var string|null
     */
    public $elementType = null;

    /**
     * @var string|null
     */
    public $elementTypeDisplayName = null;

    /**
     * @var string
     */
    public $action = '';

    /**
     * @var array
     */
    public $log = [];

    /**
     * @var string
     */
    public $ip = '';

    /**
     * @var string
     */
    public $userAgent = '';

    /**
     * @var integer|null
     */
    public $userId = null;

    /**
     * @var integer|null
     */
    public $siteId = null;

    /**
     * @var DateTime|null
     */
    public $dateCreated = null;

    private $_user     = null;
    private $_element  = null;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();

        $app        = Craft::$app;
        $request    = $app->getRequest();

        $this->siteId    = $app->getSites()->currentSite->id;
        $this->ip        = $request->getUserIP();
        $this->userAgent = $request->getUserAgent();

        $currentUser = $app->getUser()->getIdentity();
        if( $currentUser )
        {
            $this->userId = $currentUser->id;
        }
    }


    /**
     * Get element
     */
    public function getElement()
    {
        if (!$this->elementId || !$this->elementType) {
            return null;
        }

        if (!$this->_element) {
            $this->_element = Craft::$app->getElements()->getElementById($this->elementId, $this->elementType, $this->siteId);
        }

        return $this->_element;
    }


    /**
     * Get element's CP edit url
     */
    public function getElementLink()
    {
        $element = $this->getElement();

        if (!$element) {
            return null;
        }

        if ($this->elementType === Asset::class) {
            return null;
        }

        $url  = $element->getCpEditUrl();

        return $url;
    }

    /**
     * Get user cp edit link related to the log
     */
    public function getUserLink()
    {
        $user = $this->getUser();

        if (!$user) {
            return null;
        }

        return $user->getCpEditUrl();
    }

    /**
     * Get user related to the log
     */
    public function getUser()
    {
        if ($this->userId && !$this->_user) {
            $this->_user = Craft::$app->getUsers()->getUserById($this->userId);
        }

        return $this->_user;
    }


    /**
     * Create ActivityLog model from record and return it
     */
    public static function createFromRecord(ActivityLogRecord $record)
    {
        $model                          = new self();
        $model->id                      = $record->id;
        $model->title                   = $record->title;
        $model->elementId               = $record->elementId;
        $model->elementType             = $record->elementType;
        $model->elementTypeDisplayName  = $record->elementTypeDisplayName;
        $model->action                  = $record->action;
        $model->log                     = unserialize($record->log);
        $model->ip                      = $record->ip;
        $model->userAgent               = $record->userAgent;
        $model->siteId                  = $record->siteId;
        $model->userId                  = $record->userId;
        $model->dateCreated             = $record->dateCreated;

        return $model;
    }

}
