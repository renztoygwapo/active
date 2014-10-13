<?php

  /**
   * Discussions module on_quick_add event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
  	$item_id = 'discussion';

  	if(is_foreachable($projects)) {
  		foreach($projects as $project) {
  			if(Discussions::canAdd($logged_user, $project)) {
  				$map[$item_id][] = 'project_' . $project->getId();
  			} // if
  		} // foreach
  		
  		if(isset($map[$item_id])) {
		  	$items->add($item_id, array(
		  		'text'	=> lang('Discussion'),
		  		'title' => lang('Add Discussion to the Project'),
		  		'dialog_title' => lang('Add Discussion to the :name Project'),
		  		'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/discussion.png', DISCUSSIONS_MODULE) : AngieApplication::getImageUrl('icons/96x96/discussion.png', DISCUSSIONS_MODULE, $interface),
		  		'url'		=> Router::assemble('project_discussions_add', array('project_slug' => '--PROJECT-SLUG--')),
		  		'group' => QuickAddCallback::GROUP_PROJECT,
		  		'event' => 'discussion_created'
		  	));  			
  		} // if
  	} // if

  } // discussions_handle_on_quick_add