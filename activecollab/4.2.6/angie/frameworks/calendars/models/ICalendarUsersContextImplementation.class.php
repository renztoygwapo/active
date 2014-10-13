<?php

/**
 * Calendar users context implementation
 *
 * @package activeCollab.frameworks.calendars
 * @subpackage models
 */
class ICalendarUsersContextImplementation extends IUsersContextImplementation {

	/**
	 * Construct calendar users helper implementation
	 *
	 * @param Calendar $object
	 * @throws InvalidInstanceError
	 */
	function __construct(Calendar $object) {
		if($object instanceof Calendar) {
			parent::__construct($object);
		} else {
			throw new InvalidInstanceError('object', $object, 'Calendar');
		} // if
	} // __construct

	/**
	 * Describe object
	 *
	 * @param IUser $user
	 * @param boolean $detailed
	 * @param boolean $for_interface
	 */
	function describe(IUser $user, $detailed = false, $for_interface = false) {
		// TODO: Implement describe() method.
	}

	/**
	 * Cached values for isMember function
	 *
	 * @var array
	 */
	private $calendar_members = array();

	/**
	 * Return true if $user is member of this users context
	 *
	 * @param User $user
	 * @param bool $use_cache
	 * @return bool
	 */
	function isMember(User $user, $use_cache = true) {
		$user_id = $user->getId();

		if($use_cache && array_key_exists($user_id, $this->calendar_members)) {
			return $this->calendar_members[$user_id];
		} // if

		$this->calendar_members[$user_id] = (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'calendar_users WHERE user_id = ? AND calendar_id = ?', $user_id, $this->object->getId());

		return $this->calendar_members[$user_id];
	} // isMember

	/**
	 * Return array of user ids
	 * @return array
	 */
	function getIds() {
		return DB::executeFirstColumn('SELECT user_id FROM ' . TABLE_PREFIX . 'calendar_users WHERE calendar_id = ?', $this->object->getId());
	} // getIds

	/**
	 * Add user to this context
	 *
	 * @param User $user
	 */
	function add(User $user) {
		// TODO: Implement add() method.
		if(!$this->isMember($user, false)) {
			try {
				DB::beginWork('Adding user to calendar @ ' . __CLASS__);

				DB::execute('INSERT INTO ' . TABLE_PREFIX . 'calendar_users (user_id, calendar_id) VALUES (?, ?)', $user->getId(), $this->object->getId());

				$this->calendar_members = array(); // Reset internal isMember cache

				AngieApplication::cache()->removeByModel('users');
				AngieApplication::cache()->removeByModel('calendars');

				EventsManager::trigger('on_calendar_user_added', array($this->object, $user));

				DB::commit('User added to calendar @ ' . __CLASS__);
			} catch(Exception $e) {
				DB::rollback('Failed to add user to calendar @ ' . __CLASS__);
				throw $e;
			} // try
		} // if

		return $user;
	} // add

	/**
	 * Remove user from this context
	 *
	 * @param User $user
	 * @param User $by
	 * @throws Exception
	 */
	function remove(User $user, User $by) {
		// TODO: Implement remove() method.
		if($this->isMember($user, false)) {
			try {
				DB::beginWork('Removing user from calendar @ ' . __CLASS__);

				DB::execute('DELETE FROM ' . TABLE_PREFIX . 'calendar_users WHERE user_id = ? AND calendar_id = ?', $user->getId(), $this->object->getId());

				$this->calendar_members = array(); // Reset interal is member cache

				AngieApplication::cache()->removeByModel('users');
				AngieApplication::cache()->removeByModel('calendars');

				EventsManager::trigger('on_calendar_user_removed', array($this->object, $user));

				DB::commit('User removed from calendar @ ' . __CLASS__);
			} catch(Exception $e) {
				DB::rollback('Failed to remove user from calendar @ ' . __CLASS__);

				throw $e;
			} // try
		} // if
	} // remove

	/**
	 * Clear all relations
	 *
	 * @param User $user
	 */
	function clear(User $user) {
		// TODO: Implement clear() method.
		DB::execute('DELETE FROM ' . TABLE_PREFIX . 'calendar_users WHERE calendar_id = ?', $this->object->getId());

		AngieApplication::cache()->removeByModel('users');
		AngieApplication::cache()->removeByObject($this->object, 'users');
	} // clear

}