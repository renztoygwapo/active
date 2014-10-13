<?php

  /**
   * System module on_quick_add event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle on quick add event
   *
   * @param NamedList $items
   * @param NamedList $subitems
   * @param array $map
   * @param User $logged_user
   * @param DBResult $projects 
   * @param DBResult $companies
   * @param string $interface
   */
  function system_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
  	if(Projects::canAdd($logged_user)) {
  		$items->add('project', array(
	  		'text' => lang('Project'),
  			'title' => lang('New Project'),
	  		'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/project.png', SYSTEM_MODULE) : AngieApplication::getImageUrl('icons/96x96/project.png', SYSTEM_MODULE, $interface),
	  		'url' => Router::assemble('projects_add'),
	    	'group' => QuickAddCallback::GROUP_MAIN,
	    	'event' => 'project_created',
  		));
  	} // if
  	
  	if(ConfigOptions::getValue('project_requests_enabled') === true && ProjectRequests::canAdd($logged_user) && $interface == AngieApplication::INTERFACE_DEFAULT) {
  		$items->add('project_request', array(
  			'text' => lang('Project Request'),
  			'title' => lang('New Project Request'),
  			'icon' => AngieApplication::getImageUrl('icons/32x32/project-request.png', SYSTEM_MODULE),
  			'url' => Router::assemble('project_requests_add'),
  			'group' => QuickAddCallback::GROUP_MAIN,
  			'event' => 'project_request_created',
        'width' => 400
  		));
  	} // if
  	
  	if(Companies::canAdd($logged_user)) {
  		$items->add('company', array(
	  		'text' => lang('Company'),
  			'title' => lang('Add Company'),
  			'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/company.png', SYSTEM_MODULE) : AngieApplication::getImageUrl('icons/96x96/company.png', SYSTEM_MODULE, $interface),
	  		'url' => Router::assemble('people_companies_add'),
	    	'group' => QuickAddCallback::GROUP_MAIN,
	    	'event' => 'company_created',
  		));
  	} // if
  	
  	if(is_foreachable($companies)) {
  		$user_item_id = 'user';
  		foreach ($companies as $company) {
				if(Users::canAdd($logged_user, $company)) {
					$map[$user_item_id][] = 'company_' . $company->getId();
				} // if
  		} // foreach
  		
  		if(isset($map[$user_item_id])) {
	  		$items->add('user', array(
		  		'text' => lang('User'),
	  			'title' => lang('Add User to the Company'),
	  			'dialog_title' => lang('Add User to the :name Company'),
		  		'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/user.png', SYSTEM_MODULE) : AngieApplication::getImageUrl('icons/96x96/user.png', SYSTEM_MODULE, $interface),
		  		'url' => Router::assemble('people_company_user_add', array('company_id' => '--COMPANY-ID--')),
		    	'group' => QuickAddCallback::GROUP_MAIN,
          'event' => 'user_created',
	  		));
  		} // if
  	} // if
  	
  	$milestone_item_id = 'milestone';
  	if(is_foreachable($projects)) {
  		foreach ($projects as $project) {
  			if (Milestones::canAdd($logged_user, $project)) {  				
  				$map[$milestone_item_id][] = 'project_' . $project->getId();
  			} // if
  		} // foreach
  		
  		if (isset($map[$milestone_item_id])) {
		  	$items->add($milestone_item_id, array(
		  		'text' => lang('Milestone'),
		  		'title' => lang('Add Milestone to the Project'),
		  		'dialog_title' => lang('Add Milestone to the :name Project'),
		  		'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/milestone.png', SYSTEM_MODULE) : AngieApplication::getImageUrl('icons/96x96/milestone.png', SYSTEM_MODULE, $interface),
		  		'url' => Router::assemble('project_milestones_add', array('project_slug' => '--PROJECT-SLUG--')),
		    	'group' => QuickAddCallback::GROUP_PROJECT,
		    	'event' => 'milestone_created',
		  	));
  		} // if
  	} // if

  } // system_handle_on_project_tabs