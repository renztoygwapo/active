<?php

  /**
   * Calendars class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  class Calendars extends FwCalendars {

	  /**
	   * Return if user can add calendar
	   *
	   * @param IUser $user
	   * @return bool
	   */
	  static function canAdd(IUser $user) {
		  if ($user instanceof Subcontractor || $user instanceof Client) {
			  return false;
		  } // if

		  return parent::canAdd($user);
	  } // canAdd

	  /**
	   * Get Share Types by User
	   *
	   * @param User $user
	   * @return array
	   */
	  static function getShareTypesByUser(User $user) {
		  $additional_share_types = array();
		  if ($user->isManager()) {
			  $additional_share_types[] = Calendar::SHARE_WITH_MANAGERS_ONLY;
		  } // if
		  if ($user instanceof Subcontractor || $user->isMember()) {
			  $additional_share_types[] = Calendar::SHARE_WITH_TEAM_AND_SUBCONTRACTORS;
		  } // if

		  return parent::getShareTypesByUser($user, $additional_share_types);
	  } // getShareTypesByUser

//	  /**
//	   * Get visible calendars
//	   *
//	   * @param User $user
//	   * @param int $min_state
//	   * @param string $order_by
//	   * @return DbResult
//	   */
//	  static function getCalendarsByUser(User $user, $min_state=STATE_VISIBLE, $order_by='position', $share_can_add_event=false) {
//		  $share_types = array();
//		  if ($user->isManager()) {
//			  $share_types[] = Calendar::SHARE_WITH_MANAGERS_ONLY;
//		  } // if
//		  if ($user instanceof Subcontractor || $user->isMember()) {
//			  $share_types[] = Calendar::SHARE_WITH_TEAM_AND_SUBCONTRACTORS;
//		  } // if
//
//		  return parent::getCalendarsByUser($user, $min_state, $order_by, $share_can_add_event, $share_types);
//	  } // getCalendarsByUser

	  /**
	   * Find calendars for list
	   *
	   * @param User $user
	   * @return array
	   */
	  static function findForList(User $user) {
		  $result = array();

		  $order_by = 'created_by_id=' . $user->getId() . ' DESC, created_by_id ASC, position ASC';
		  $calendars = parent::getCalendarsByUser($user, STATE_VISIBLE, $order_by);

		  if ($calendars) {
			  if (is_foreachable($calendars)) {
				  $calendar_id_prefix_pattern = '--CALENDAR-ID--';
				  $calendar_url_parameters = array('calendar_id' => $calendar_id_prefix_pattern);
				  $trash_calendar_url_pattern = Router::assemble('calendar_trash', $calendar_url_parameters);
				  $change_visibility_url_pattern = Router::assemble('calendar_change_visibility', array('calendar_id' => $calendar_id_prefix_pattern));
				  $ical_url_pattern = Router::assemble('calendar_ical_subscribe', array('calendar_id' => $calendar_id_prefix_pattern));

				  $calendar_config = ConfigOptions::getValueFor('calendar_config', $user);

				  foreach ($calendars as $subobject) {
					  $id = $subobject['id'];
					  $type = $subobject['type'];
					  $config = array_var(array_var($calendar_config, $type, array()), $id, array());

					  $created_by_id = (integer) $subobject['created_by_id'];
					  $is_creator = $created_by_id == $user->getId();

					  // if you are not creator or admin you can only change color of calendar
					  if ($is_creator || $user->isAdministrator()) {
						  $edit_calendar_url_pattern = Router::assemble('calendar_edit', $calendar_url_parameters);
					  } else {
						  $edit_calendar_url_pattern = Router::assemble('calendar_change_color', $calendar_url_parameters);
					  } // if

					  $result[] = array(
						  'id'          => $id,
						  'type'        => $type,
						  'name'        => $subobject['name'],
						  'color'       => array_var($config, 'color', Calendar::DEFAULT_COLOR),
						  'visible'     => array_var($config, 'visible', 1),
						  'created_by_id' => $subobject['created_by_id'],
						  'created_by_name' => $subobject['created_by_name'],
						  'permissions' => array(
							  'can_edit'    => true,
							  'can_trash'   => $is_creator || $user->isAdministrator() // all calendars which user can see are already present
						  ),
						  'urls'        => array(
							  'edit'              => str_replace($calendar_id_prefix_pattern, $id, $edit_calendar_url_pattern),
							  'trash'             => str_replace($calendar_id_prefix_pattern, $id, $trash_calendar_url_pattern),
							  'change_visibility' => str_replace($calendar_id_prefix_pattern, $id, $change_visibility_url_pattern),
							  'ical'              => str_replace($calendar_id_prefix_pattern, $id, $ical_url_pattern)
						  )
					  );
				  } // foreach
			  } // if
		  } // if

		  return $result;
	  } // findForList
  
  }