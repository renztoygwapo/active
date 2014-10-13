<?php

	/**
	 * Task calendar event context implementation
	 *
	 * @package activeCollab.modules.tasks
	 * @subpackage models
	 */
	class ITaskCalendarEventContextImplementation extends ICalendarEventContextImplementation {

		/**
		 * Construct calendar event context helper instance
		 *
		 * @param Task $object
		 * @throws InvalidInstanceError
		 */
		function __construct(Task $object) {
			if($object instanceof Task) {
				parent::__construct($object);
			} else {
				throw new InvalidInstanceError('object', $object, 'Task');
			} // if
		} // __construct

		/**
		 * Describe object as calendar event
		 *
		 * @param IUser $user
		 * @param bool $detailed
		 * @param bool $for_interface
		 * @param int $min_state
		 * @return mixed
		 */
		function describe(IUser $user, $detailed = false, $for_interface = false, $min_state = STATE_VISIBLE) {
			$result = array(
				'id'            => $this->object->getId(),
				'type'          => 'Task',
				'parent_id'     => $this->object->getProjectId(),
				'parent_type'   => 'Project',
				'name'          => $this->object->getName(),
				'ends_on'       => $this->object->getDueOn(),
				'starts_on'     => $this->object->getDueOn(),
				'permissions'   => array(
					'can_edit'        => $this->object->canEdit($user),
					'can_trash'       => false,
					'can_reschedule'  => $this->object->schedule()->canReschedule($user)
				),
				'urls'          => array(
					'view'          => $this->object->getViewUrl(),
					'edit'          => $this->object->getEditUrl(),
					'reschedule'    => $this->object->schedule()->getRescheduleUrl()
				)
			);

			return $result;
		} // describe

	}