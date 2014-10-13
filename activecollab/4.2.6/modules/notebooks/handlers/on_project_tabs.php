<?php

  /**
   * Notebooks module on_project_tabs event handler
   *
   * @package activeCollab.modules.notebooks
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
  function notebooks_handle_on_project_tabs(&$tabs, &$logged_user, &$project, &$tabs_settings, $interface) {
    if(in_array('notebooks', $tabs_settings) && Notebooks::canAccess($logged_user, $project, false)) {
    	$tabs->add('notebooks', array(
        'text' => lang('Notebooks'),
        'url' => Router::assemble('project_notebooks', array('project_slug' => $project->getSlug())),
        'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? 
        	AngieApplication::getImageUrl('icons/16x16/notebooks-tab-icon.png', NOTEBOOKS_MODULE) : 
        	AngieApplication::getImageUrl('icons/listviews/notebooks.png', NOTEBOOKS_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // notebooks_handle_on_project_tabs