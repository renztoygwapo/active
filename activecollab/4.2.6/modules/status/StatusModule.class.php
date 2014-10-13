<?php

  /**
   * Status module definition
   *
   * @package activeCollab.modules.status
   * @subpackage models
   */
  class StatusModule extends AngieModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'status';
    
    /**
     * Module version
     *
     * @var string
     */
    protected $version = '4.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('status_updates', 'status', array('controller' => 'status', 'action' => 'index'));
      Router::map('status_updates_add', 'status/add', array('controller' => 'status', 'action' => 'add'));
      Router::map('status_update_delete', 'status/delete', array('controller' => 'status', 'action' => 'delete'));
      Router::map('status_updates_rss', 'status/rss', array('controller' => 'status', 'action' => 'rss'));
      Router::map('status_updates_count_new_messages', 'status/count-new-messages', array('controller' => 'status', 'action' => 'count_new_messages'));
      
      Router::map('status_update', 'status/update/:status_update_id', array('controller' => 'status', 'action' => 'view'), array('status_update_id' => '\d+'));
      Router::map('status_update_reply', 'status/update/:status_update_id/reply', array('controller' => 'status', 'action' => 'view'), array('status_update_id' => '\d+'));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_status_bar', 'on_status_bar');
      EventsManager::listen('on_custom_user_permissions', 'on_custom_user_permissions');
      EventsManager::listen('on_wireframe_updates', 'on_wireframe_updates');
      EventsManager::listen('on_phone_homescreen', 'on_phone_homescreen');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Name
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Status');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds simple, globally available communication channel. Tell your team members or clients what you are working on or have a quick chat');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All data generated using it will be deleted');
    } // getUninstallMessage
    
  }