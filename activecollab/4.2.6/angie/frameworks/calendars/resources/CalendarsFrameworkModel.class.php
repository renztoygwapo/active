<?php

  /**
   * Calendars framework model definition
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  class CalendarsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct calendar framework model definition
     *
     * @param CalendarsFramework $parent
     */
    function __construct(CalendarsFramework $parent) {
      parent::__construct($parent);

      $this->addModel(DB::createTable('calendars')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create('UserCalendar'),
        DBNameColumn::create(255),
        DBStringColumn::create('color', 7),
        DBStateColumn::create(),
	      DBStringColumn::create('share_type'),
	      //DBIntegerColumn::create('share_type', 10, '0')->setUnsigned(true),
	      DBBoolColumn::create('share_can_add_events'),
        DBAdditionalPropertiesColumn::create(),
        DBActionOnByColumn::create('created', true),
        DBIntegerColumn::create('position', 10, '0')->setUnsigned(true),
      ))->addIndices(array(
        DBIndex::create('position'),
      )))->setOrderBy('position')->setTypeFromField('type');
      
      $this->addModel(DB::createTable('calendar_events')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('CalendarEvent'),
        DBParentColumn::create(),
        DBNameColumn::create(255),
        DBDateColumn::create('starts_on'), 
        DBTimeColumn::create('starts_on_time'), 
        DBDateColumn::create('ends_on'),
	      DBStringColumn::create('repeat_event', 150, 'dont'),
	      DBStringColumn::create('repeat_event_option', 150),
	      DBDateColumn::create('repeat_until'),
        DBStateColumn::create(),
        DBAdditionalPropertiesColumn::create(),
        DBActionOnByColumn::create('created', true), 
        DBIntegerColumn::create('position', 10, '0')->setUnsigned(true), 
      ))->addIndices(array(
        DBIndex::create('starts_on'), 
        DBIndex::create('starts_on_time', DBIndex::KEY, array('starts_on', 'starts_on_time')), 
        DBIndex::create('ends_on'), 
        DBIndex::create('position'), 
      )))->setOrderBy('starts_on', 'starts_on_time', 'position')->setTypeFromField('type');

	    $this->addTable(DB::createTable('calendar_users')->addColumns(array(
		    DBIntegerColumn::create('user_id', 10, '0'),
		    DBIntegerColumn::create('calendar_id', 10, '0')
	    ))->addIndices(array(
				DBIndex::create('calendar_id'),
		    DBIndex::create('user_id'),
	    )));
    } // __construct

	  /**
	   * Load initial data
	   *
	   * @param string $environment
	   */
	  function loadInitialData($environment = null) {
		  parent::loadInitialData($environment);

		  $this->addConfigOption('calendar_config');
		  $this->addConfigOption('calendar_sidebar_hidden');
		  $this->addConfigOption('default_project_calendar_filter', array(
			  'type' => 'everything_in_my_projects'
		  ));
	  } // loadInitialData
    
  }