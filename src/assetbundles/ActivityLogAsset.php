<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\activitylog\assetbundles;

use Craft;
use craft\web\View;
use craft\helpers\Json;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ActivityLogAsset extends AssetBundle
{

  // Public Methods
  // =========================================================================

  /**
   * Initializes the bundle.
   */
  public function init()
  {
      // define the path that your publishable resources live
    $this->sourcePath = "@nav33d/activitylog/assetbundles/dist";

    // define the dependencies
    $this->depends = [
      CpAsset::class,
    ];

    // define the relative path to CSS/JS files that should be registered with the page
    // when this asset bundle is registered
    $this->js = [
      'js/activitylog.js',
    ];

    $this->css = [
      'css/activitylog.css',
    ];

    parent::init();
  }

}
