<?php

  /**
   * Files module on_project_tabs event handler
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */
  
  /**
   * Handle on prepare project overview event
   *
   * @param NamedList $tabs
   * @param User $logged_user
   * @param Project $project
   * @param array $tabs_settings
   * @param string $interface
   */
  function files_handle_on_project_tabs(&$tabs, &$logged_user, &$project, &$tabs_settings, $interface) {
    if(in_array('files', $tabs_settings) && ProjectAssets::canAccess($logged_user, $project, false)) {
      $tabs->add('files', array(
        'text' => lang('Files'),
        'url' => Router::assemble('project_assets', array('project_slug' => $project->getSlug())),
        'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? 
        	AngieApplication::getImageUrl('icons/16x16/files-tab-icon.png', FILES_MODULE) : 
        	AngieApplication::getImageUrl('icons/listviews/files.png', FILES_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // files_handle_on_project_tabs