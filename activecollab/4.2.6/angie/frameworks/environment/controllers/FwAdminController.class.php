<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Abstract administration controller that application can inherit
   *
   * @package angie.frameworks.environments
   * @subpackage controllers
   */
  abstract class FwAdminController extends BackendController {

    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();

      if($this->logged_user->isAdministrator()) {
        $this->wireframe->tabs->clear();
        $this->wireframe->tabs->add('admin', lang('Administration'), Router::assemble('admin'), null, true);

        EventsManager::trigger('on_admin_tabs', array(&$this->wireframe->tabs, &$this->logged_user));

        $this->wireframe->breadcrumbs->add('admin', lang('Administration'), Router::assemble('admin'));
        $this->wireframe->hidePrintButton();

        $this->wireframe->setCurrentMenuItem('admin');
      } else {
        $this->response->forbidden();
      } // if
    } // __before
    
    /**
     * Display administration index page
     */
    function index() {
      $admin_panel = new AdminPanel($this->logged_user);

      $admin_panel->addToGeneral('appearance', lang('Appearance'), Router::assemble('appearance_admin'), AngieApplication::getImageUrl('admin_panel/theme.png', ENVIRONMENT_FRAMEWORK), array(
        'after' => 'identity',
      ));

      if(MODULES_MANAGEMENT_ENABLED) {
        $admin_panel->addToGeneral('modules', lang('Modules'), Router::assemble('modules_admin'), AngieApplication::getImageUrl('admin_panel/modules.png', ENVIRONMENT_FRAMEWORK));
      } // if

      $admin_panel->addToGeneral('disk_space', lang('Disk Space'), Router::assemble('disk_space_admin'), AngieApplication::getImageUrl('admin_panel/disk-space.png', ENVIRONMENT_FRAMEWORK));

      if (!AngieApplication::isOnDemand()) {
        $admin_panel->addToTools('scheduled_tasks', lang('Scheduled Tasks'), Router::assemble('scheduled_tasks_admin'), AngieApplication::getImageUrl('admin_panel/scheduled-tasks.png', ENVIRONMENT_FRAMEWORK));
      } // if

      $admin_panel->addToTools('indices', lang('Rebuild Indexes'), Router::assemble('indices_admin'), AngieApplication::getImageUrl('admin_panel/indices.png', ENVIRONMENT_FRAMEWORK));

      $admin_panel->addToTools('control_tower', lang('Control Tower'), Router::assemble('control_tower_settings'), AngieApplication::getImageUrl('admin_panel/control-tower.png', ENVIRONMENT_FRAMEWORK), array(
        'onclick' => new FlyoutFormCallback(array(
          'success_event' => 'control_tower_settings_updated',
          'success_message' => lang('Settings updated'),
          'width' => 650,
        )),
      ));

      $admin_panel->addToTools('maintenance_mode', lang('Maintenance Mode'), Router::assemble('maintenance_mode_settings'), AngieApplication::getImageUrl('admin_panel/maintenance-mode.png', ENVIRONMENT_FRAMEWORK), array(
        'onclick' => new FlyoutFormCallback(array(
          'success_event' => 'maintenance_settings_updated',
          'success_message' => lang('Settings updated'),
          'width' => 500,
        )),
      ));

      if (!AngieApplication::isOnDemand()) {
        $admin_panel->addToGeneral('network', lang('Network'), Router::assemble('network_settings'), AngieApplication::getImageUrl('admin_panel/network.png', ENVIRONMENT_FRAMEWORK), array(
          'onclick' => new FlyoutFormCallback('network_settings_updated'),
        ));
      } // if

	    $admin_panel->addToTools('firewall', lang('Firewall'), Router::assemble('firewall'), AngieApplication::getImageUrl('admin_panel/firewall.png', ENVIRONMENT_FRAMEWORK), array(
		    'onclick' => new FlyoutFormCallback('firewall_config_saved'),
	    ));
      
      EventsManager::trigger('on_admin_panel', array(&$admin_panel));
      
      $this->smarty->assign('admin_panel', $admin_panel);
    } // index
    
  }