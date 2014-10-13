<?php

  /**
   * Update activeCollab 4.0.18 to activeCollab 4.1.0
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0095 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.18';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.1.0';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
	      'removeOldCalendarModule' => 'Remove old calendar module',
        'updateInvoicingOptions' => 'Update invoicing configuration options',
        'updateInvoiceAndQuotesState' => 'Updating invoices and quotes state',
        'createCalendarTables' => 'Create calendar tables',
        'createCalendarOptions' => 'Create calendar configuration options',
        'scheduleIndexesRebuild' => 'Schedule index rebuild'
      );
    } // getActions

	  /**
	   * Remove old calendar module
	   *
	   * @return string
	   */
	  function removeOldCalendarModule() {
		  DB::execute('DELETE FROM ' . TABLE_PREFIX . 'modules WHERE name = ?', 'calendar');
	  } // removeOldCalendarModule

    /**
     * Update invoicing configuration options
     *
     * @return bool|string
     */
    function updateInvoicingOptions() {
      try {
        if ($this->isModuleInstalled('invoicing')) {
          $this->addConfigOption('description_format_grouped_by_task', null, 'invoicing');
          $this->addConfigOption('description_format_grouped_by_project', null, 'invoicing');
          $this->addConfigOption('description_format_grouped_by_job_type', null, 'invoicing');
          $this->addConfigOption('description_format_separate_items', null, 'invoicing');
          $this->addConfigOption('first_record_summary_transformation', 'prefix_with_colon', 'invoicing');
          $this->addConfigOption('second_record_summary_transformation', null, 'invoicing');
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateInvoicingOptions

    /**
     * Update invoicing configuration options
     *
     * @return bool|string
     */
    function updateInvoiceAndQuotesState() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          try {
            $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
            if(in_array($invoice_objects_table, DB::listTables(TABLE_PREFIX))) {
              DB::execute("UPDATE $invoice_objects_table SET state = ? WHERE type IN (?)", 3, array('Invoice', 'Quote'));
            } // if
          } catch(Exception $e) {
            return $e->getMessage();
          } // try
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateInvoiceAndQuotesState

    /**
     * Create calendar configuration options
     *
     * @return bool|string
     */
    function createCalendarOptions() {
      try {
        $this->addConfigOption('calendar_config', null, 'calendars');
        $this->addConfigOption('calendar_sidebar_hidden', false, 'calendars');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // createCalendarOptions

    /**
     * Create Calendars Tables
     *
     * @return bool|string
     */
    function createCalendarTables() {
      try {
        $calendars_table = TABLE_PREFIX . 'calendars';
        $calendar_events_table = TABLE_PREFIX . 'calendar_events';
        $calendar_users_table = TABLE_PREFIX . 'calendar_users';

        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

        // create calendars table
        if(!in_array($calendars_table, DB::listTables(TABLE_PREFIX))) {
          DB::execute("CREATE TABLE $calendars_table (
            id int unsigned NOT NULL auto_increment,
            type varchar(50) NOT NULL DEFAULT 'UserCalendar',
            name varchar(255)  DEFAULT NULL,
            color varchar(7)  DEFAULT NULL,
            state tinyint(3) unsigned NOT NULL DEFAULT 0,
            original_state tinyint(3) unsigned NULL DEFAULT NULL,
            share_type varchar(255)  DEFAULT NULL,
            share_can_add_events tinyint(1) unsigned NOT NULL DEFAULT '0',
            raw_additional_properties longtext ,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            position int(10) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX type (type),
            INDEX created_on (created_on),
            INDEX position (position)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        } // if

        // create calendar events table
        if(!in_array($calendar_events_table, DB::listTables(TABLE_PREFIX))) {
          DB::execute("CREATE TABLE $calendar_events_table (
            id int unsigned NOT NULL auto_increment,
            type varchar(50) NOT NULL DEFAULT 'CalendarEvent',
            parent_type varchar(50)  DEFAULT NULL,
            parent_id int unsigned NULL DEFAULT NULL,
            name varchar(255)  DEFAULT NULL,
            starts_on date  DEFAULT NULL,
            starts_on_time time  DEFAULT NULL,
            ends_on date  DEFAULT NULL,
            repeat_event varchar(150) NOT NULL DEFAULT 'dont',
            repeat_event_option varchar(150)  DEFAULT NULL,
            repeat_until date  DEFAULT NULL,
            state tinyint(3) unsigned NOT NULL DEFAULT 0,
            original_state tinyint(3) unsigned NULL DEFAULT NULL,
            raw_additional_properties longtext ,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            position int(10) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX type (type),
            INDEX parent (parent_type, parent_id),
            INDEX created_on (created_on),
            INDEX starts_on (starts_on),
            INDEX starts_on_time (starts_on, starts_on_time),
            INDEX ends_on (ends_on),
            INDEX position (position)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        } // if

        // create calendar users table
        if(!in_array($calendar_users_table, DB::listTables(TABLE_PREFIX))) {
          DB::execute("CREATE TABLE $calendar_users_table (
            user_id int(10) NOT NULL DEFAULT 0,
            calendar_id int(10) NOT NULL DEFAULT 0,
            INDEX calendar_id (calendar_id),
            INDEX user_id (user_id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // createCalendarTables
  }