<?php

	/**
	 * Subtask calendar event context implementation
	 *
	 * @package angie.frameworks.subtasks
	 * @subpackage models
	 */
	class ISubtaskCalendarEventContextImplementation extends ICalendarEventContextImplementation {

		/**
		 * Construct calendar event context helper instance
		 *
		 * @param Subtask $object
		 * @throws InvalidInstanceError
		 */
		function __construct(Subtask $object) {
			if($object instanceof Subtask) {
				parent::__construct($object);
			} else {
				throw new InvalidInstanceError('object', $object, 'Subtask');
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
			$parent = DataObjectPool::get('Task', $this->object->getParentId());
			$result = array(
				'id'              => $this->object->getId(),
				'type'            => 'ProjectObjectSubtask',
				'parent_id'       => $parent->getProjectId(),
				'parent_type'     => 'Project',
				'org_parent_id'   => $parent->getId(),
				'org_parent_type' => $parent->getType(),
				'name'            => $this->object->getName(),
				'ends_on'         => $this->object->getDueOn(),
				'starts_on'       => $this->object->getDueOn(),
				'permissions'     => array(
					'can_edit'        => $this->object->canEdit($user),
					'can_trash'       => false,
					'can_reschedule'  => $this->object->schedule()->canReschedule($user)
				),
				'urls'            => array(
					'view'            => $this->object->getViewUrl(),
					'edit'            => $this->object->getEditUrl(),
					'reschedule'      => $this->object->schedule()->getRescheduleUrl()
				)
			);

			return $result;
		} // describe

	}