<?php

	/**
	 * Calendar state implementation
	 *
	 * @package activeCollab.frameworks.calendars
	 * @subpackage models
	 */
	class ICalendarStateImplementation extends IStateImplementation {

		/**
		 * Construct calendar state implementation instance
		 *
		 * @param Calendar $object
		 */
		function __construct(Calendar $object) {
			if($object instanceof Calendar) {
				parent::__construct($object);
			} else {
				throw new InvalidInstanceError('object', $object, 'Calendar');
			} // if
		} // __construct

		/**
		 * Move object to trash
		 *
		 * @param boolean $trash_already_trashed
		 */
		function trash($trash_already_trashed = false) {
			try {
				DB::beginWork('Moving calendar to trash @ ' . __CLASS__);

				parent::trash($trash_already_trashed);

				$calendar_events = CalendarEvents::findByCalendar($this->object, STATE_ARCHIVED);
				if (is_foreachable($calendar_events)) {
					foreach ($calendar_events as $calendar_event) {
						if ($calendar_event instanceof CalendarEvent) {
							$calendar_event->state()->trash(true);
						} // if
					} // foreach
				} // if

				DB::commit('Calendar moved to trash @ ' . __CLASS__);
			} catch(Exception $e) {
				DB::rollback('Failed to move calendar to trash @ ' . __CLASS__);

				throw $e;
			} // try
		} // trash

		/**
		 * Restore object from trash
		 */
		function untrash() {
			try {
				DB::beginWork('Restoring calendar from a trash @ ' . __CLASS__);

				parent::untrash();

				$calendar_events = CalendarEvents::findByCalendar($this->object, STATE_TRASHED);
				if (is_foreachable($calendar_events)) {
					foreach ($calendar_events as $calendar_event) {
						if ($calendar_event instanceof CalendarEvent && $calendar_event->getState() === STATE_TRASHED) {
							$calendar_event->state()->untrash();
						} // if
					} // foreach
				} // if

				DB::commit('Calendar restored from a trash @ ' . __CLASS__);
			} catch(Exception $e) {
				DB::rollback('Failed to restore calendar from trash @ ' . __CLASS__);

				throw $e;
			} // try
		} // untrash

		/**
		 * Mark object as deleted
		 */
		function delete() {
			try {
				DB::beginWork('Deleting calendar @ ' . __CLASS__);

				parent::delete();

				$calendar_events = CalendarEvents::findByCalendar($this->object, STATE_TRASHED);
				if (is_foreachable($calendar_events)) {
					foreach ($calendar_events as $calendar_event) {
						if ($calendar_event instanceof CalendarEvent) {
							$calendar_event->state()->delete();
						} // if
					} // foreach
				} // if

				DB::commit('Calendar deleted @ ' . __CLASS__);
			} catch(Exception $e) {
				DB::rollback('Failed to delete calendar @ ' . __CLASS__);

				throw $e;
			} // try
		} // delete

		// ---------------------------------------------------
		//  Permissions
		// ---------------------------------------------------

		/**
		 * Returns true if $user can mark this object as archived
		 *
		 * @param User $user
		 * @return boolean
		 */
		function canArchive(User $user) {
			// we cannot archive obejct which is not visible
			if ($this->object->getState() < STATE_VISIBLE) {
				return false;
			} // if

			return $this->object->isCreator($user);
		} // canArchive


		/**
		 * Returns true if $user can mark this object as not archived
		 *
		 * @param User $user
		 * @return boolean
		 */
		function canUnarchive(User $user) {
			if ($this->object->getState() != STATE_ARCHIVED) {
				return false;
			} // if
			return $this->object->isCreator($user) || $user->isAdministrator();
		} // canArchive

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