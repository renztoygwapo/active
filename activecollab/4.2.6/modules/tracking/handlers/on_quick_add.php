<?php

  /**
   * Tracking module on_quick_add event handler
   *
   * @package activeCollab.modules.tracking
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
  function tracking_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
    $time_entry_item_id = 'time_entry';
    $expense_item_id = 'expense';
    
    if(is_foreachable($projects)) {
      foreach($projects as $project) {
        if($project->tracking()->canAdd($logged_user)) {
          $map[$time_entry_item_id][] = 'project_' . $project->getId();
          $map[$expense_item_id][] = 'project_' . $project->getId();
        } // if
      } // foreach
      
      if(isset($map[$time_entry_item_id])) {
		    $items->add($time_entry_item_id, array(
		      'text' => lang('Time Entry'),
		    	'title' => lang('Log Time Entry in the Project'),
		    	'dialog_title' => lang('Log Time Entry in the :name Project'),
		    	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/time-entry.png', TRACKING_MODULE) : AngieApplication::getImageUrl('icons/96x96/timerecord.png', TRACKING_MODULE, $interface),
		      'url' => Router::assemble('project_tracking_time_records_add', array('project_slug' => '--PROJECT-SLUG--')),
		    	'group' => QuickAddCallback::GROUP_PROJECT,
          'handler_settings' => array(
            'width' => 'narrow'
          ),
		    	'event' => 'time_record_created',
		    ));
		    
		    $items->add($expense_item_id, array(
		    	'text' => lang('Expense'),
		    	'title' => lang('Log Expense in the Project'),
		    	'dialog_title' => lang('Log Expense in the :name Project'),
		    	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/expense.png', TRACKING_MODULE) : AngieApplication::getImageUrl('icons/96x96/expense.png', TRACKING_MODULE, $interface),
		    	'url' => Router::assemble('project_tracking_expenses_add', array('project_slug' => '--PROJECT-SLUG--')),
		    	'group' => QuickAddCallback::GROUP_PROJECT,
          'handler_settings' => array(
            'width' => 'narrow'
          ),
		    	'event' => 'expense_created',
		    ));
      } // if
    } // if

  } // tracking_handle_on_project_tabs