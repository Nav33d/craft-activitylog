<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\activitylog\console\controllers;

use Craft;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

use nav33d\activitylog\ActivityLog;

class LogsController extends Controller
{
    public $defaultAction = '';

    // Public Methods
    // =========================================================================

    /**
     * Prune activity logs
     */
    public function actionPrune()
    {
        $this->stdout("Gathering logs to prune ... \n\n", Console::FG_YELLOW);

        $rows = ActivityLog::$plugin->activitylog->prune();

        if ( !$rows )
        {
            $this->stdout('No logs due for deletion' . PHP_EOL, Console::FG_GREEN);
            return ExitCode::OK;
        }

        $this->stdout($rows .' logs pruned' . PHP_EOL, Console::FG_GREEN);
        return ExitCode::OK;
    }

}
