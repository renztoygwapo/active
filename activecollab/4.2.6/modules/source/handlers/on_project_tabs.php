<?php

  /**
   * Source control module on_project_tabs event handler
   *
   * @package activeCollab.modules.source
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
  function source_handle_on_project_tabs(&$tabs, &$logged_user, &$project, &$tabs_settings, $interface) {
    if(in_array('source', $tabs_settings) && ProjectSourceRepositories::canAccess($logged_user, $project, false)) {
      $tabs->add('source', array(
        'text' => lang('Source'),
        'url' => source_module_url($project),
        'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? 
        	AngieApplication::getImageUrl('icons/16x16/source-tab-icon.png', SOURCE_MODULE) : 
        	AngieApplication::getImageUrl('icons/listviews/source.png', SOURCE_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // source_handle_on_project_tabs