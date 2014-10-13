<?php

  /**
   * on_admin_panel event handler
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function system_handle_on_admin_panel(AdminPanel &$admin_panel) {
    if (!(AngieApplication::isOnDemand() && !AngieApplication::isModuleLoaded('tracking'))) {
      $admin_panel->addToGeneral('activecollab', lang('General Settings'), Router::assemble('admin_settings_general'), AngieApplication::getImageUrl('admin_panel/general.gif', SYSTEM_MODULE), array(
        'begin_with' => true,
        'onclick' => new FlyoutFormCallback(array(
          'success_event' => 'general_settings_updated',
          'success_message' => lang('Settings updated'),
          'focus_first_field' => false,
        )),
      ));
    } // if
    
    $admin_panel->addToGeneral('identity', lang('Identity Settings'), Router::assemble('identity_admin'), AngieApplication::getImageUrl('admin_panel/identity.png', SYSTEM_MODULE), array(
      'after' => 'activecollab', 
      'onclick' => new FlyoutFormCallback(array(
        'success_event' => 'identity_settings_updated', 
        'success_message' => lang('Settings updated'),
      )), 
    ));

    $admin_panel->addToGeneral('repsite', lang('Manage Repsite'), Router::assemble('repsite_admin'), AngieApplication::getImageUrl('admin_panel/repsite.png', SYSTEM_MODULE));

    $admin_panel->addToProjects('projects', lang('Project Settings'), Router::assemble('admin_projects'), AngieApplication::getImageUrl('admin_panel/projects.png', SYSTEM_MODULE), array(
      'onclick' => new FlyoutFormCallback(array(
        'success_event' => 'projects_settings_updated', 
        'success_message' => lang('Settings updated'),
        'focus_first_field' => false,
      )), 
    ));
    $admin_panel->addToProjects('project_roles', lang('Project Roles'), Router::assemble('admin_project_roles'), AngieApplication::getImageUrl('admin_panel/project-roles.png', SYSTEM_MODULE));
    $admin_panel->addToProjects('project_requests', lang('Project Requests'), Router::assemble('admin_project_requests'), AngieApplication::getImageUrl('admin_panel/project-requests.png', SYSTEM_MODULE), array(
      'onclick' => new FlyoutFormCallback(array(
        'success_event' => 'project_requests_settings_updated', 
        'success_message' => lang('Settings updated'),
      )), 
    ));
    
    $admin_panel->addToProjects('projects_labels_admin', lang('Project Labels'), Router::assemble('projects_admin_labels'), AngieApplication::getImageUrl('admin_panel/project-labels.png', SYSTEM_MODULE));
    
    $admin_panel->addToProjects('categories', lang('Master Categories'), Router::assemble('admin_settings_categories'), AngieApplication::getImageUrl('admin_panel/categories.png', CATEGORIES_FRAMEWORK), array(
      'onclick' => new FlyoutFormCallback(array(
        'success_event' => 'categories_settings_updated', 
    		'success_message' => lang('Master categories have been updated'), 
      )), 
    ));

    $admin_panel->addToProjects('projects_data_cleanup', lang('Projects Data Cleanup'), Router::assemble('admin_projects_data_cleanup'), AngieApplication::getImageUrl('admin_panel/projects-data-cleanup.png', SYSTEM_MODULE));
  } // system_handle_on_admin_panel