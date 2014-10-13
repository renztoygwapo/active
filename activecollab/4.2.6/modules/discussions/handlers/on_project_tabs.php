<?php

  /**
   * Discussions module on_project_tabs event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_project_tabs(&$tabs, &$logged_user, &$project, &$tabs_settings, $interface) {
    if(in_array('discussions', $tabs_settings) && Discussions::canAccess($logged_user, $project, false)) {
    	$tabs->add('discussions', array(
        'text' => lang('Discussions'),
        'url' => Router::assemble('project_discussions', array('project_slug' => $project->getSlug())),
        'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? 
        	AngieApplication::getImageUrl('icons/16x16/discussions-tab-icon.png', DISCUSSIONS_MODULE) : 
        	AngieApplication::getImageUrl('icons/listviews/discussions.png', DISCUSSIONS_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // discussions_handle_on_project_tabs