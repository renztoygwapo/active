<?php

  /**
   * on_trash_map event handler
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_trash_ma[ event
   *
   * @param NamedList $sections
   * @param array $map
   * @param User $user
   */
  function tracking_handle_on_trash_map(&$map, User &$user) {
  	$map = array_merge(
  		(array) $map,
  		(array) TimeRecords::getTrashedMap($user),
  		(array) Expenses::getTrashedMap($user)
  	);
  } // tracking_handle_on_trash_map