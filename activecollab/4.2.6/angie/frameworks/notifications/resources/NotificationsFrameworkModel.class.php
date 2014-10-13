<?php

  /**
   * Notifications framework model definition
   *
   * @package angie.frameworks.notifications
   * @subpackage resources
   */
  class NotificationsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct subscriptions framework model definition
     *
     * @param SubscriptionsFramework $parent
     */
    function __construct(SubscriptionsFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('notifications')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create('Notification'),
        DBParentColumn::create(),
        DBUserColumn::create('sender'),
        DBDateTimeColumn::create('created_on'),
        DBAdditionalPropertiesColumn::create(),
      ))->addIndices(array(
        DBIndex::create('created_on'),
      )))->setTypeFromField('type')->setObjectIsAbstract(true);

      $this->addTable(DB::createTable('notification_recipients')->addColumns(array(
        DBIdColumn::create(),
        DBIntegerColumn::create('notification_id')->setUnsigned(true),
        DBUserColumn::create('recipient'),
        DBDateTimeColumn::create('seen_on'),
        DBDateTimeColumn::create('read_on'),
        DBBoolColumn::create('is_mentioned', false),
      ))->addIndices(array(
        DBIndex::create('notification_recipient', DBIndex::UNIQUE, array('notification_id', 'recipient_email')),
        DBIndex::create('seen_on'),
        DBIndex::create('read_on'),
      )));
    } // __construct

    /**
     * Load initial data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      parent::loadInitialData($environment);

      $this->addConfigOption('notifications_show_indicators', 2); // Badge plus message
      $this->addConfigOption('email_notifications_enabled', true);
      $this->addConfigOption('popup_show_only_unread', false);
      $this->addConfigOption('who_can_override_channel_settings', array(
        'email' => array('Member'),
      ));
    } // loadInitialData
    
  }