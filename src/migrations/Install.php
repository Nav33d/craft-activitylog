<?php

/**
 * Activity Log plugin for Craft CMS 3
 *
 * Log activity inside Craft CMS control panel
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2018 Nav33d
 */

namespace nav33d\activitylog\migrations;

use craft\db\Migration;

class Install extends Migration
{

  public function safeUp()
  {
  	$this->createTables();
    $this->createIndexes();
    $this->addForeignKeys();
  }


  public function safeDown()
  {
    $this->dropTableIfExists('{{%activitylog_logs}}');
  }


  /**
   * Creates tables
   *
   * @return void
   */
  protected function createTables()
  {
  	$this->createTable('{{%activitylog_logs}}', [
  		'id'                      => $this->primaryKey(),
      'title'                   => $this->string(),
      'elementId'               => $this->integer(),
      'elementType'             => $this->string(),
      'elementTypeDisplayName'  => $this->string(),
      'action'                  => $this->string(),
      'log'                     => $this->text(),
      'ip'                      => $this->string(),
      'userAgent'               => $this->string(),
      'siteId'                  => $this->integer()->notNull(),
      'userId'                  => $this->integer(),
  		'dateCreated'             => $this->dateTime()->notNull(),
  		'dateUpdated'             => $this->dateTime()->notNull(),
  		'uid'                     => $this->uid()
  	]);
  }


  /**
   * Creates the indexes.
   *
   * @return void
   */
  protected function createIndexes()
  {
  	$this->createIndex(null, '{{%activitylog_logs}}', 'userId');
    $this->createIndex(null, '{{%activitylog_logs}}', 'elementId');
  }


  /**
   * Add foreign keys
   *
   * @return void
   */
  protected function addForeignKeys()
  {

    $this->addForeignKey(
      $this->db->getForeignKeyName('{{%activitylog_logs}}', 'userId'),
      '{{%activitylog_logs}}',
      'userId',
      '{{%users}}',
      'id',
      'SET NULL',
      'SET NULL'
    );

    $this->addForeignKey(
      $this->db->getForeignKeyName('{{%activitylog_logs}}', 'elementId'),
      '{{%activitylog_logs}}',
      'elementId',
      '{{%elements}}',
      'id',
      'SET NULL',
      'SET NULL'
    );

    $this->addForeignKey(
      $this->db->getForeignKeyName('{{%activitylog_logs}}', 'siteId'),
      '{{%activitylog_logs}}',
      'siteId',
      '{{%sites}}',
      'id',
      'CASCADE',
      'CASCADE'
    );
  }

}
