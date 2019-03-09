<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\activitylog\records;

use Craft;
use craft\records\User;
use craft\records\Element;
use craft\db\ActiveRecord;

use yii\db\ActiveQueryInterface;

class ActivityLog extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activitylog_logs}}';
    }


    /**
     * Returns the user related to activity log.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getUser(): ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }


    /**
     * Returns the element related to activity log.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'elementId']);
    }

}
