<?php

  /**
   * on_trash_sections event handler
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_trash_sections event
   *
   * @param NamedList $sections
   * @param array $map
   * @param User $user
   */
  function tracking_handle_on_trash_sections(NamedList &$sections, &$map, User &$user) {
    
		// time records in trash
		$trashed_time_records = TimeRecords::findTrashed($user, $map);
		if (is_foreachable($trashed_time_records)) {
			$sections->add('time_records', array(
				'label' => lang('Time Records'),
				'count' => count($trashed_time_records),
				'items' => $trashed_time_records
			));
		} // if	
		
		// expenses in trash
		$trashed_expenses = Expenses::findTrashed($user, $map);
		if (is_foreachable($trashed_expenses)) {
			$sections->add('expenses', array(
				'label' => lang('Expenses'),
				'count' => count($trashed_expenses),
				'items' => $trashed_expenses
			));
		} // if		
		
  } // tracking_handle_on_trash_sections