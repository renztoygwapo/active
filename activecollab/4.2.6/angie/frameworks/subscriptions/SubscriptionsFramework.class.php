<?php

  /**
   * Subscriptions framework definition
   *
   * @package angie.frameworks.subscriptions
   */
  class SubscriptionsFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'subscriptions';

    /**
     * Event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_handle_public_unsubscribe', 'on_handle_public_unsubscribe');
    } // defineHandlers
    
    /**
     * Define subscription routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineSubscriptionRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_subscriptions", "$context_path/subscriptions", array('controller' => $controller_name, 'action' => "{$context}_manage_subscriptions", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_subscribe", "$context_path/subscribe", array('controller' => $controller_name, 'action' => "{$context}_subscribe", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_unsubscribe", "$context_path/unsubscribe", array('controller' => $controller_name, 'action' => "{$context}_unsubscribe", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_unsubscribe_all", "$context_path/unsubscribe_all", array('controller' => $controller_name, 'action' => "{$context}_unsubscribe_all", 'module' => $module_name), $context_requirements);
    } // defineSubscriptionRoutesFor
    
  }