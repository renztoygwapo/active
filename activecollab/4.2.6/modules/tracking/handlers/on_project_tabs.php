<?php

  /**
   * Tracking module on_project_tabs event handler
   *
   * @package activeCollab.modules.tracking
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
  function tracking_handle_on_project_tabs(&$tabs, &$logged_user, &$project, &$tabs_settings, $interface) {
    if(in_array('time', $tabs_settings) && TrackingObjects::canAccess($logged_user, $project, false)) {
    	$tabs->add('time', array(
        'text' => lang('Time and Expenses'),
        'url' => Router::assemble('project_tracking', array('project_slug' => $project->getSlug())),
        'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? 
        	AngieApplication::getImageUrl('icons/16x16/tracking-tab-icon.png', TRACKING_MODULE) : 
        	AngieApplication::getImageUrl('icons/listviews/tracking.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // tracking_handle_on_project_tabs