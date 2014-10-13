<?php

  /**
   * Calendars framework definition class
   *
   * @package angie.frameworks.calendars
   */
  class CalendarsFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'calendars';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('calendars', 'calendars', array('controller' => 'calendars', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO));
	    Router::map('calendars_monthly', 'calendars/monthly/:year/:month', array('controller' => 'calendars', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('year' => Router::MATCH_WORD, 'month' => Router::MATCH_WORD));
      Router::map('calendars_import_feed', 'calendars/import/feed', array('controller' => 'calendars', 'action' => 'import_feed', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO));
      Router::map('calendars_import_file', 'calendars/import/file', array('controller' => 'calendars', 'action' => 'import_file', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO));
      Router::map('calendars_sidebar_toggle', 'calendars/sidebar', array('controller' => 'calendars', 'action' => 'sidebar', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO));
      Router::map('calendars_add', 'calendars/add', array('controller' => 'calendars', 'action' => 'add', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO));

      //Router::map('ical_subscribe', 'ical-subscribe', array('controller' => 'calendars', 'action' => 'ical_subscribe'));
	    Router::map('calendar_ical', 'calendars/:calendar_id/ical', array('controller' => 'calendars', 'action' => 'ical', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));
	    Router::map('calendar_ical_subscribe', 'calendars/:calendar_id/ical-subscribe', array('controller' => 'calendars', 'action' => 'ical_subscribe', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));

      Router::map('calendar', 'calendars/:calendar_id', array('controller' => 'calendars', 'action' => 'view', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));
      Router::map('calendar_edit', 'calendars/:calendar_id/edit', array('controller' => 'calendars', 'action' => 'edit', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));
      Router::map('calendar_delete', 'calendars/:calendar_id/delete', array('controller' => 'calendars', 'action' => 'delete', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));

	    Router::map('calendar_change_visibility', 'calendars/:calendar_id/change-visibility', array('controller' => 'calendars', 'action' => 'change_visibility', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));
	    Router::map('calendar_change_visibility_by_type', 'calendars/:type/:type_id/change-visibility', array('controller' => 'calendars', 'action' => 'change_visibility', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('type' => Router::MATCH_WORD, 'type_id' => Router::MATCH_ID));

      Router::map('calendar_mass_change_visibility', 'calendars/mass-change-visibility', array('controller' => 'calendars', 'action' => 'mass_change_visibility', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO));

	    Router::map('calendar_change_color', 'calendars/:calendar_id/change-color', array('controller' => 'calendars', 'action' => 'change_color', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));
	    Router::map('calendar_change_color_by_type', 'calendars/:type/:type_id/change-color', array('controller' => 'calendars', 'action' => 'change_color', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('type' => Router::MATCH_WORD, 'type_id' => Router::MATCH_ID));
	    Router::map('events_add', 'calendars/events/add', array('controller' => 'calendars', 'action' => 'add_event', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO));

      Router::map('calendar_events', 'calendars/:calendar_id/events', array('controller' => 'calendar_events', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));
      Router::map('calendar_events_add', 'calendars/:calendar_id/events/add', array('controller' => 'calendar_events', 'action' => 'add', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID));

      Router::map('calendar_event', 'calendars/:calendar_id/events/:calendar_event_id', array('controller' => 'calendar_events', 'action' => 'view', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID, 'calendar_event_id' => Router::MATCH_ID));
      Router::map('calendar_event_edit', 'calendars/:calendar_id/events/:calendar_event_id/edit', array('controller' => 'calendar_events', 'action' => 'edit', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID, 'calendar_event_id' => Router::MATCH_ID));
      Router::map('calendar_event_delete', 'calendars/:calendar_id/events/:calendar_event_id/delete', array('controller' => 'calendar_events', 'action' => 'delete', 'module' => CALENDARS_FRAMEWORK_INJECT_INTO), array('calendar_id' => Router::MATCH_ID, 'calendar_event_id' => Router::MATCH_ID));

	    // State implementation routes
	    // @todo definisi state rute
	    AngieApplication::getModule('environment')->defineStateRoutesFor('calendar', 'calendar/:calendar_id', 'calendars', CALENDARS_FRAMEWORK_INJECT_INTO);
	    AngieApplication::getModule('environment')->defineStateRoutesFor('calendar_event', 'calendar/:calendar_id/events/:calendar_event_id', 'calendar_events', CALENDARS_FRAMEWORK_INJECT_INTO);

	    // Calendar footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    // Calendar
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('calendar', 'calendars/:calendar_id', 'calendars', CALENDARS_FRAMEWORK_INJECT_INTO);
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('calendar', 'calendars/:calendar_id', 'calendars', CALENDARS_FRAMEWORK_INJECT_INTO);

		    // CalendarEvent
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('calendar_event', 'calendars/:calendar_id/events/:calendar_event_id', 'calendar_events', CALENDARS_FRAMEWORK_INJECT_INTO);
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('calendar_event', 'calendars/:calendar_id/events/:calendar_event_id', 'calendar_events', CALENDARS_FRAMEWORK_INJECT_INTO);
	    } // if
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
	    EventsManager::listen('on_trash_map', 'on_trash_map');
	    EventsManager::listen('on_empty_trash', 'on_empty_trash');
	    EventsManager::listen('on_trash_sections', 'on_trash_sections');
      EventsManager::listen('on_main_menu', 'on_main_menu');
	    EventsManager::listen('on_history_field_renderers', 'on_history_field_renderers');
    } // defineHandlers
    
  }