<?php

  /**
   * Framework level calendar manager implementation
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  abstract class FwCalendars extends BaseCalendars {

    /**
     * Returns true if $user can use calendars section
     *
     * @param IUser $user
     * @return boolean
     */
    static function canUse(IUser $user) {
      return $user instanceof User;
    } // canUse

    /**
     * Returns true if $user can create a new calendars
     *
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return $user instanceof User;
    } // canAdd

    /**
     * Returns true if $user can manage calendars
     *
     * @param User $user
     * @return boolean
     */
    static function canManage(User $user) {
      return $user instanceof User;
    } // canManage

    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------

    /**
     * Return default group options for a given user
     *
     * @param User $user
     * @return array
     */
    static function getDefaultGroupOptions(User $user) {
      $options = array();

      if(Calendars::canAdd($user)) {
        $options['new'] = array(
          'label' => lang('New Calendar'),
          'url' => Router::assemble('calendars_add'),
          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          'onclick' => new FlyoutFormCallback(array(
	          'title' => lang('New Calendar'),
            'success_event' => 'calendar_created',
            'width' => '350'
          ))
        );

//        $options[] = array(
//          'label' => lang('Import iCalendar Feed'),
//          'url' => Router::assemble('calendars_import_feed'),
//          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
//          'onclick' => new FlyoutFormCallback(array(
//	          'title' => lang('Import iCalendar Feed'),
//            'success_event' => 'calendar_created',
//            'width' => 'narrow'
//          ))
//        );
//
//        $options[] = array(
//          'label' => lang('Import iCalendar File'),
//          'url' => Router::assemble('calendars_import_file'),
//          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
//          'onclick' => new FlyoutFormCallback(array(
//	          'title' => lang('Import iCalendar File'),
//            'success_event' => 'calendar_created',
//            'width' => 'narrow'
//          ))
//        );
      } // if

//      if($user->isFeedUser()) {
//        $options[] = array(
//          'label' => lang('Subscribe to Feed'),
//          'url' => Router::assemble('ical_subscribe'),
//          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
//          'onclick' => new FlyoutFormCallback(array(
//	          'title' => lang('Subscribe to Feed'),
//            'width' => 'narrow'
//          ))
//        );
//      } // if

      EventsManager::trigger('on_default_calendar_group_options', array(&$options, &$user));

      return count($options) > 0 ? $options : null;
    } // getDefaultGroupOptions

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

	  /**
	   * Get trashed map
	   *
	   * @param User $user
	   * @return array
	   */
	  static function getTrashedMap($user) {
		  return array(
			  'calendar' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'calendars WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED)
		  );
	  } // getTrashedMap

	  /**
	   * Find trashed calendars
	   *
	   * @param User $user
	   * @param array $map
	   * @return array
	   */
	  static function findTrashed(User $user, &$map) {
		  $trashed_calendars = DB::execute('SELECT id, name, type FROM ' . TABLE_PREFIX . 'calendars WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
		  if (!is_foreachable($trashed_calendars)) {
			  return null;
		  } // if

		  $view_url = Router::assemble('calendar', array('calendar_id' => '--CALENDAR-ID--'));

		  $items = array();
		  foreach ($trashed_calendars as $calendar) {
			  $id = $calendar['id'];
			  $items[] = array(
				  'id'              => $id,
				  'name'            => $calendar['name'],
				  'type'            => $calendar['type'],
				  'permalink'       => str_replace('--CALENDAR-ID--', $id, $view_url),
				  'can_be_parent'   => true,
			  );
		  } // foreach

		  return $items;
	  } // findTrashed

	  /**
	   * Delete trashed calendars
	   */
	  static function deleteTrashed() {
		  $calendars = Calendars::find(array(
			  'conditions' => array('state = ?', STATE_TRASHED)
		  ));

		  if (is_foreachable($calendars)) {
			  foreach ($calendars as $calendar) {
				  $calendar->state()->delete();
			  } // foreach
		  } // if

		  return true;
	  } // deleteTrashed

	  /**
	   * Get Share Types by User
	   *
	   * @param User $user
	   * @param null $additional_share_types
	   * @return array
	   */
	  static function getShareTypesByUser(User $user, $additional_share_types=null) {
		  // define share types
		  $share_types = array(Calendar::SHARE_WITH_EVERYONE);
		  if ($user->isMember(true)) {
			  $share_types[] = Calendar::SHARE_WITH_MEMBERS_ONLY;
		  } // if
		  if ($user->isAdministrator()) {
			  $share_types[] = Calendar::SHARE_WITH_ADMINS_ONLY;
		  } // if

		  if (is_foreachable($additional_share_types)) {
			  foreach($additional_share_types as $value) {
				  $share_types[] = $value;
			  } // foreach
		  } // if

		  return $share_types;
	  } // getShareTypesByUser

	  /**
	   * Get calendars by user
	   *
	   * @param User $user
	   * @param int $min_state
	   * @param string $order_by
	   * @param bool $filter_by_can_add_events
	   * @return DbResult
	   */
	  static function getCalendarsByUser(User $user, $min_state=STATE_VISIBLE, $order_by='position', $filter_by_can_add_events=false) {
		  $table_calendars = TABLE_PREFIX . "calendars";
		  $table_calendar_users = TABLE_PREFIX . "calendar_users";

		  $shared_calendar_ids = DB::executeFirstColumn("SELECT calendar_id FROM $table_calendar_users WHERE user_id = ?", $user->getId());

		  // get share types by user
		  $share_types = Calendars::getShareTypesByUser($user);

		  if ($filter_by_can_add_events) {
			  return DB::execute("SELECT * FROM $table_calendars WHERE (created_by_id = ? OR (share_type IN (?) AND share_can_add_events = ?) OR (share_type = ? AND id IN (?) AND share_can_add_events = ?)) AND state >= ? ORDER BY $order_by", $user->getId(), $share_types, 1, Calendar::SHARE_WITH_SELECTED_USERS, $shared_calendar_ids, 1, $min_state);
		  } else {
			  return DB::execute("SELECT * FROM $table_calendars WHERE (created_by_id = ? OR share_type IN (?) OR (share_type = ? AND id IN (?))) AND state >= ? ORDER BY $order_by", $user->getId(), $share_types, Calendar::SHARE_WITH_SELECTED_USERS, $shared_calendar_ids, $min_state);
		  } // if
	  } // getCalendarsByUser

	  /**
	   * Find grouped by user
	   *
	   * @param User $user
	   * @param bool $filter_can_add_events
	   * @return array
	   */
	  static function findGroupedByUserId(User $user, $filter_can_add_events=false) {
		  $order_by = 'created_by_id=' . $user->getId() . ' DESC, created_by_id ASC, position ASC';

		  $calendars = self::getCalendarsByUser($user, STATE_VISIBLE, $order_by, $filter_can_add_events);

		  $groups = array();

		  if (is_foreachable($calendars)) {
			  foreach ($calendars as $calendar) {
					$user_id = $calendar['created_by_id'];
				  if (!isset($groups[$user_id])) {
					  $groups[$user_id] = array();
				  } // if

				  $groups[$user_id][] = $calendar;
			  } // foreach
		  } // if

		  return $groups;
	  } // findGroupedByUser

	  /**
	   * Get Id, Name map of calendars by user
	   *
	   * @param IUser $user
	   * @param int $min_state
	   * @return array
	   */
	  static function getIdNameMapByUser(IUser $user, $min_state=STATE_VISIBLE) {
			$calendars = self::getCalendarsByUser($user, $min_state);

		  $map = array();

		  if (is_foreachable($calendars)) {
			  $calendars->setCasting(array(
				  'id' => DBResult::CAST_INT
			  ));

			  foreach ($calendars as $subobject) {
				  $calendar_id = $subobject['id'];
				  $calendar_name = $subobject['name'];
				  $created_by_id = $subobject['created_by_id'];
				  $share_can_add_events = (boolean) $subobject['share_can_add_events'];

				  if ($user->getId() == $created_by_id || $share_can_add_events) {
						$map[$calendar_id] = $calendar_name;
				  } // if
			  } // foreach;
		  } // if

		  return $map;
	  } // getIdNameMapByUser

	  /**
	   * Get visible calendar ids
	   *
	   * @param IUser $user
	   * @param int $min_state
	   * @return array|null
	   */
	  static function getCalendarIdsByUser(IUser $user, $min_state=STATE_VISIBLE) {
		  $calendars = self::getCalendarsByUser($user, $min_state);

		  if (is_foreachable($calendars)) {
			  $ids = array();
			  foreach ($calendars as $subobject) {
					$ids[] = $subobject['id'];
			  } // foreach
			  return $ids;
		  } else {
			  return null;
		  } // if
	  } // getCalendarIdsByUser

	  /**
	   * Get config for logged user by type id
	   *
	   * @param $type string
	   * @param $type_id integer
	   * @return array
	   */
	  static function getLoggedUserConfigByTypeId($type, $type_id) {
		  $user = Authentication::getLoggedUser();
		  $calendar_config = ConfigOptions::getValueFor('calendar_config', $user);

		  $config = array_var(array_var($calendar_config, $type, array()), $type_id, array());

		  return $config;
	  } // getLoggedUserConfigByTypeId

	  /**
	   * Set config for logged user by type id
	   *
	   * @param $type string
	   * @param $type_id integer
	   * @param $config array
	   */
	  static function setConfigForLoggedUserByTypeId($type, $type_id, $config) {
		  $user = Authentication::getLoggedUser();
		  $calendar_config = ConfigOptions::getValueFor('calendar_config', $user);

		  if (!isset($calendar_config[$type])) {
			  $calendar_config[$type] = array();
		  } // if
		  if (!isset($calendar_config[$type][$type_id])) {
			  $calendar_config[$type][$type_id] = array();
		  } // if

		  if (is_foreachable($config)) {
			  foreach ($config as $key => $value) {
				  $calendar_config[$type][$type_id][$key] = $value;
			  } // foreach

			  ConfigOptions::setValueFor('calendar_config', $user, $calendar_config);
		  } // if
	  } // setConfigForLoggedUserByTypeId

	  /**
	   * Find and load calendar data for manager
	   *
	   * @param IUser $user
	   * @param $from
	   * @param $to
	   * @return array
	   */
	  static function findForManager(IUser $user, $from, $to) {
      $custom_id = 0;
      $subscriptions_id = 1;

      $result = array(
        $custom_id => array(
          'label' => lang('Calendars'),
          'calendars' => array(),
        ),
        $subscriptions_id => array(
          'label' => lang('Subscriptions'),
          'calendars' => array(),
        ),
      );

      $calendars = Calendars::findByUser($user);

      if($calendars) {
        foreach($calendars as $calendar) {
          $result[$custom_id]['calendars'][$calendar->getId()] = array(
            'name' => $calendar->getName(),
            'color' => $calendar->getColor(),
            'events' => array(),
          );
        } // foreach
      } // if

      return $result;
    } // findForManager

    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------

    /**
     * Return next calendar position
     *
     * @return integer
     */
    static function getNextPosition() {
      return ((integer) DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'calendars')) + 1;
    } // getNextPosition

  }