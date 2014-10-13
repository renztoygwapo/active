<?php

  /**
   * Authentication mode framwork definition class
   *
   * @package angie.frameworks.authentication
   */
  class AuthenticationFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'authentication';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('login', 'login', array('controller' => 'authentication', 'action' => 'login', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      Router::map('logout', 'logout', array('controller' => 'authentication', 'action' => 'logout', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      Router::map('forgot_password', 'lost-password', array('controller' => 'authentication', 'action' => 'forgot_password', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      Router::map('reset_password', 'reset-password', array('controller' => 'authentication', 'action' => 'reset_password', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      
      // Users
      Router::map('users', 'users', array('controller' => 'users', 'action' => 'index', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      Router::map('users_add', 'users/add', array('controller' => 'users', 'action' => 'add', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      
      Router::map('user', 'users/:user_id', array('controller' => 'users', 'action' => 'view', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      Router::map('user_edit', 'users/:user_id/edit', array('controller' => 'users', 'action' => 'edit', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      Router::map('user_edit_password', 'users/:user_id/edit-password', array('controller' => 'users', 'action' => 'edit_password', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      Router::map('user_edit_profile', 'users/:user_id/edit-profile', array('controller' => 'users', 'action' => 'edit_profile', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      Router::map('user_edit_settings', 'users/:user_id/edit-settings', array('controller' => 'users', 'action' => 'edit_settings', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      Router::map('user_delete', 'users/:user_id/delete', array('controller' => 'users', 'action' => 'delete', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      Router::map('user_export_vcard', 'users/:user_id/export-vcard', array('controller' => 'users', 'action' => 'export_vcard', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      Router::map('user_set_as_invited', 'users/:user_id/set-as-invited', array('controller' => 'users', 'action' => 'set_as_invited'), array('user_id' => Router::MATCH_ID));
      Router::map('user_login_as', 'users/:user_id/login-as', array('controller' => 'users', 'action' => 'login_as'), array('user_id' => Router::MATCH_ID));
      
      AngieApplication::getModule('authentication')->defineApiClientSubscriptionsRoutesFor('user', 'users/:user_id', 'users', AUTHENTICATION_FRAMEWORK_INJECT_INTO, array('user_id' => Router::MATCH_ID));
      AngieApplication::getModule('environment')->defineStateRoutesFor('user', 'users/:user_id', 'users', AUTHENTICATION_FRAMEWORK_INJECT_INTO, array('user_id' => Router::MATCH_ID));
      AngieApplication::getModule('avatar')->defineAvatarRoutesFor('user', 'users/:user_id', 'users', AUTHENTICATION_FRAMEWORK_INJECT_INTO, array('user_id' => Router::MATCH_ID));
      AngieApplication::getModule('activity_logs')->defineActivityLogsRoutesFor('user', 'users/:user_id', 'users', AUTHENTICATION_FRAMEWORK_INJECT_INTO, array('user_id' => Router::MATCH_ID));
      AngieApplication::getModule('homescreens')->defineHomescreenRoutesFor('user', 'users/:user_id', 'users', AUTHENTICATION_FRAMEWORK_INJECT_INTO, array('user_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('user', 'users/:user_id', 'users', AUTHENTICATION_FRAMEWORK_INJECT_INTO, array('user_id' => Router::MATCH_ID));

      // API
      Router::map('system_roles_info', 'info/roles', array('controller' => 'roles_info', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      Router::map('system_role_info', 'info/roles/:role_id', array('controller' => 'roles_info', 'action' => 'role', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('role_id' => Router::MATCH_ID));

      // Administration
      Router::map('maintenance_mode_settings', 'admin/maintenance-mode', array('controller' => 'maintenance_mode', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      // Roles Administration
      Router::map('admin_roles', AUTHENTICATION_FRAMEWORK_ADMIN_ROUTE_BASE . '/roles', array('controller' => 'roles_admin', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO));
      Router::map('admin_role', AUTHENTICATION_FRAMEWORK_ADMIN_ROUTE_BASE . '/roles/:user_role_name', array('controller' => 'roles_admin', 'action' => 'view', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO), array('user_role_name' => Router::MATCH_SLUG));
      
      // Search Index
      Router::map('users_search_index_admin_build', AUTHENTICATION_FRAMEWORK_ADMIN_ROUTE_BASE . '/search/users/build', array('controller' => 'users_search_index_admin', 'action' => 'build', 'module' => AUTHENTICATION_FRAMEWORK_INJECT_INTO, 'search_index_name' => 'users'));
    } // defineRoutes
    
    /**
     * Define API client subscriptions routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineApiClientSubscriptionsRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $api_client_subscription_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('api_client_subscription_id' => Router::MATCH_ID)) : array('api_client_subscription_id' => Router::MATCH_ID);
      
      Router::map("{$context}_api_client_subscriptions", "$context_path/api-subscriptions", array('controller' => $controller_name, 'action' => "{$context}_api_client_subscriptions", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_api_client_subscriptions_add", "$context_path/api-subscriptions/add", array('controller' => $controller_name, 'action' => "{$context}_add_api_client_subscription", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_api_client_subscription", "$context_path/api-subscriptions/:api_client_subscription_id", array('controller' => $controller_name, 'action' => "{$context}_view_api_client_subscription", 'module' => $module_name), $api_client_subscription_requirements);
      Router::map("{$context}_api_client_subscription_enable", "$context_path/api-subscriptions/:api_client_subscription_id/enable", array('controller' => $controller_name, 'action' => "{$context}_enable_api_client_subscription", 'module' => $module_name), $api_client_subscription_requirements);
      Router::map("{$context}_api_client_subscription_disable", "$context_path/api-subscriptions/:api_client_subscription_id/disable", array('controller' => $controller_name, 'action' => "{$context}_disable_api_client_subscription", 'module' => $module_name), $api_client_subscription_requirements);
      Router::map("{$context}_api_client_subscription_edit", "$context_path/api-subscriptions/:api_client_subscription_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_api_client_subscription", 'module' => $module_name), $api_client_subscription_requirements);
      Router::map("{$context}_api_client_subscription_delete", "$context_path/api-subscriptions/:api_client_subscription_id/delete", array('controller' => $controller_name, 'action' => "{$context}_delete_api_client_subscription", 'module' => $module_name), $api_client_subscription_requirements);
    } // defineApiClientSubscriptionsRoutesFor
    
    /**
     * Define framework handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
      EventsManager::listen('on_search_indices', 'on_search_indices');
      EventsManager::listen('on_main_menu', 'on_main_menu');
      EventsManager::listen('on_context_domains', 'on_context_domains');
      EventsManager::listen('on_daily', 'on_daily');
    } // defineHandlers
    
  }