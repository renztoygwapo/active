<?php
	/**
	 * Calendar event state implementation
	 *
	 * @package activeCollab.frameworks.calendars
	 * @subpackage models
	 */
	class ICalendarEventStateImplementation extends IStateImplementation {

		/**
		 * Construct calendar event state implementation instance
		 *
		 * @param CalendarEvent $object
		 * @throws InvalidInstanceError
		 */
		function __construct(CalendarEvent $object) {
			if($object instanceof CalendarEvent) {
				parent::__construct($object);
			} else {
				throw new InvalidInstanceError('object', $object, 'CalendarEvent');
			} // if
		} // __construct

		/**
		 * Trash calendar event
		 *
		 * @param boolean $trash_already_trashed
		 */
		function trash($trash_already_trashed = false) {
			return parent::trash($trash_already_trashed);
		} // trash

		/**
		 * Untrash calendar event
		 */
		function untrash() {
			return parent::untrash();
		} // untrash

		/**
		 * Delete calendar event
		 */
		function delete() {
			parent::delete();
		} // delete

		/**
		 * Returns true if $user can mark this object as trashed
		 *
		 * @param User $user
		 * @return boolean
		 */
		function canTrash(User $user) {
			if ($this->object->getState() == STATE_TRASHED) {
				return false;
			} // if

			return $this->object->canDelete($user);
		} // canTrash

		/**
		 * Returns true if $user can mark this obejct as untrashed
		 *
		 * @param User $user
		 * @return boolean
		 */
		function canUntrash(User $user) {
			if ($this->object->getState() != STATE_TRASHED) {
				return false;
			} // if

			return $this->object->canDelete($user) || $user->canManageTrash();
		} // canUntrash

		/**
		 * Returns true if $user can mark this object as deleted
		 *
		 * @param User $user
		 * @return boolean
		 */
		function canDelete(User $user) {
			return $this->object->canDelete($user) || $user->canManageTrash();
		} // canDelete

	}