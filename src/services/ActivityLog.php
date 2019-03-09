<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\activitylog\services;

use Craft;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\UrlHelper;

use yii\base\Component;
use yii\data\Pagination;

use nav33d\activitylog\ActivityLog as ActivityLogPlugin;
use nav33d\activitylog\models\ActivityLog as ActivityLogModel;
use nav33d\activitylog\records\ActivityLog as ActivityLogRecord;

class ActivityLog extends Component
{

    /**
     * Get all activity logs
     */
    public function getAll($per_page, $page, $sort, $filter, $action_filter)
    {
        $sortField = 'dateCreated';
        $sortType = 'DESC';

        // Figure out the sorting type
        if ($sort !== '') {
            if (strpos($sort, '|') === false) {
                $sortField = $sort;
            } else {
                list($sortField, $sortType) = explode('|', $sort);
            }
        }
        
        $query = ActivityLogRecord::find()
            ->with('user')
            ->orderBy("{$sortField} {$sortType}");
        
        $countQuery = clone $query;
        $count = $countQuery->count();

        $offset = ($page - 1) * $per_page;
        $logs = $query->offset($offset)
            ->limit($per_page);
        
        if ($filter !== '') {
            $query->where(['like', 'title', $filter]);
        }

        if ($action_filter !== '') {
            $query->where(['like', 'action', $action_filter]);
        }

        $logs = $query->asArray()->all();
        
        foreach( $logs as &$log )
        {
            $log['viewLink'] = UrlHelper::cpUrl('activitylog/'.$log['id']);
        }
        
        $data['data'] = $logs;

        $data['links']['pagination'] = [
            'total' => $count,
            'per_page' => $per_page,
            'current_page' => $page,
            'last_page' => ceil($count / $per_page),
            'next_page_url' => null,
            'prev_page_url' => null,
            'from' => $offset + 1,
            'to' => $offset + ($count > $per_page ? $per_page : $count),
        ];

        return $data;
    }

    /**
     * Get a log by id
     */
    public function getById($id)
    {
        $log = ActivityLogRecord::find()
            ->where(['id' => $id])
            ->one();
        
        $log = ActivityLogModel::createFromRecord($log);

        $log->log = json_encode($log->log, JSON_PRETTY_PRINT);
        
        return $log;
    }

    /**
     * Save an activity log
     */
    public function saveActivityLog(ActivityLogModel $model)
    {
        $record = new ActivityLogRecord();

        $record->title                  = $model->title;
        $record->elementId              = $model->elementId;
        $record->elementType            = $model->elementType;
        $record->elementTypeDisplayName = $model->elementTypeDisplayName;
        $record->action                 = $model->action;
        $record->log                    = serialize($model->log);
        $record->ip                     = $model->ip;
        $record->userAgent              = $model->userAgent;
        $record->userId                 = $model->userId;
        $record->siteId                 = $model->siteId;

        $db = Craft::$app->getDb();
        $transaction = $db->beginTransaction();

        try {
            // Save the activitylog
            $record->save(false);

            // Now that we have a activitylog ID, save it on the model
            if (!$model->id) {
                $model->id = $record->id;
            }

            $transaction->commit();

        } catch(\Exception $e) {
            $transaction->rollBack();

            Craft::error( Craft::t('activitylog', 'An error occured while saving activity log: {error}', ['error' => print_r($record->getErrors(), true),]), 'activitylog');
        }
    }


    /**
     * Prune logs based on settings
     */
    public function prune()
    {
        $settings = ActivityLogPlugin::$settings;
        
        $limit = $settings->logsLimit;
        $interval = 'P'.$limit.'D';
        
        $date = new \DateTime();
        $date->sub(new \DateInterval($interval));

        $rows = ActivityLogRecord::deleteAll(['<', 'dateCreated', Db::prepareDateForDb($date)]);
        return $rows;
    }


    /**
     * Delete all logs
     */
    public function deleteAll()
    {
        $cache = Craft::$app->getCache();

        $rows = ActivityLogRecord::deleteAll();

        if ( $rows )
        {
            $currentUser = Craft::$app->getUser()->getIdentity();
            $cacheKey = "activitylog_logs_last_deleted";
            $cacheData = ['rows' => $rows, 'time' => time(), 'user' => $currentUser->username];
            $cache->set($cacheKey, $cacheData);
        }

        return $rows;
    }


    /*====================================================================
    *
    *   Activity Log event handlers
    *
    =====================================================================*/

    /**
     * Handle plugin related events
     */
    public function handlePluginEvent($plugin, $event)
    {
        $model = new ActivityLogModel();
        $model->title = $plugin->name;
        $model->action = $event;

        $pluginSettings = $plugin->settings ? $plugin->settings->toArray() : null;
        $model->log = [
            'name' => $plugin->name,
            'version' => $plugin->version,
            'schemaVersion' => $plugin->schemaVersion,
            'settings' => $pluginSettings,
        ];

        $this->saveActivityLog($model);
    }


    /**
     * Handle auth related events
     */
    public function handleAuthEvent($user, $event)
    {
        $model = new ActivityLogModel();
        $model->title = $user->email;
        $model->action = $event;

        $model->log = [
            'Username' => $user->username,
            'userDetails' => $user->toArray(),
        ];

        $this->saveActivityLog($model);
    }


    /**
     * Handle element related events
     */
    public function handleElementEvent($craftEvent)
    {
        $element = $craftEvent->element;
        $isNew = $craftEvent->isNew;

        if ( method_exists($element, 'getOwner') && $element->getOwner() )
        {
            return true;
        }

        $event = "saved-element";
        if ( $isNew )
        {
            $event = "created-element";
        }

        if ( $craftEvent->name == 'afterDeleteElement' )
        {
            $event = "deleted-element";
        }

        $model = new ActivityLogModel();
        $model->title = $element->title ?? $element->username ?? $element->name ?? null;
        $model->elementId = $element->id;
        $model->elementType = get_class($element);
        $model->elementTypeDisplayName = $element->displayName();
        $model->action = $event;

        $model->log = [
            'element' => $element->toArray(),
        ];

        $this->saveActivityLog($model);
    }
}
