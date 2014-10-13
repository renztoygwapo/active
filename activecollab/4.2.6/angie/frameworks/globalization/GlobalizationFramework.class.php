<?php

  /**
   * Globalization framework definition
   *
   * @package angie.frameworks.globalization
   */
  class GlobalizationFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'globalization';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('date_time_settings', GLOBALIZATION_ADMIN_ROUTE_BASE . '/date-time', array('controller' => 'globalization_admin', 'action' => 'date_time', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      Router::map('workweek_settings', GLOBALIZATION_ADMIN_ROUTE_BASE . '/workweek', array('controller' => 'globalization_admin', 'action' => 'workweek', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      
      // Currencies
      Router::map('admin_currencies', 'admin/currencies', array('controller' => 'currencies_admin', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      Router::map('admin_currencies_add', 'admin/currencies/add', array('controller' => 'currencies_admin', 'action' => 'add', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      
      Router::map('admin_currency', 'admin/currencies/:currency_id', array('controller' => 'currencies_admin', 'action' => 'view', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('currency_id' => Router::MATCH_ID));
      Router::map('admin_currency_edit', 'admin/currencies/:currency_id/edit', array('controller' => 'currencies_admin', 'action' => 'edit', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('currency_id' => Router::MATCH_ID));
      Router::map('admin_currency_set_as_default', 'admin/currencies/:currency_id/set-as-default', array('controller' => 'currencies_admin', 'action' => 'set_as_default', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('currency_id' => Router::MATCH_ID));
      Router::map('admin_currency_delete', 'admin/currencies/:currency_id/delete', array('controller' => 'currencies_admin', 'action' => 'delete', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('currency_id' => Router::MATCH_ID));
      
      // Languages
      Router::map('admin_languages', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages', array('controller' => 'languages_admin', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      Router::map('admin_languages_add', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/add', array('controller' => 'languages_admin', 'action' => 'add', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      Router::map('admin_languages_set_default', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/set-default', array('controller' => 'languages_admin', 'action' => 'set_default', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      Router::map('admin_languages_import', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/import', array('controller' => 'languages_admin', 'action' => 'import', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      Router::map('admin_language_do_import', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/do-import', array('controller' => 'languages_admin', 'action' => 'do_import', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));

      Router::map('execute_import_steps', GLOBALIZATION_ADMIN_ROUTE_BASE . '/execute-import-steps', array('controller' => 'languages_admin', 'action' => 'execute_import_steps', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO));
      
      Router::map('admin_language', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id', array('controller' => 'languages_admin', 'action' => 'view', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));
      Router::map('admin_language_export', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id/export', array('controller' => 'languages_admin', 'action' => 'export', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));
      Router::map('admin_language_update', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/update', array('controller' => 'languages_admin', 'action' => 'update', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));
      Router::map('admin_language_do_update', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id/do-update', array('controller' => 'languages_admin', 'action' => 'do_update', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));

      Router::map('admin_language_edit', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id/edit', array('controller' => 'languages_admin', 'action' => 'edit', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));
      Router::map('admin_language_delete', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id/delete', array('controller' => 'languages_admin', 'action' => 'delete', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));
      Router::map('admin_language_edit_translation', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id/edit-translation', array('controller' => 'languages_admin', 'action' => 'edit_translation', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));
      Router::map('admin_language_save_single_translation', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id/save-translation', array('controller' => 'languages_admin', 'action' => 'save_single', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));
      Router::map('admin_language_translate_letter', GLOBALIZATION_ADMIN_ROUTE_BASE . '/languages/:language_id/translate-letter', array('controller' => 'languages_admin', 'action' => 'translate_letter', 'module' => GLOBALIZATION_FRAMEWORK_INJECT_INTO), array('language_id' => Router::MATCH_ID));

      // API
      Router::map('days_currencies', 'info/currencies', array('controller' => 'api', 'action' => 'currencies', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      Router::map('days_off_info', 'info/days-off', array('controller' => 'api', 'action' => 'days_off', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
    /**
     * Define framework handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
    } // defineHandlers
    
  }