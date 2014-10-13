<?php

	/**
	 * Project calendar context implementation
	 *
	 * @package activeCollab.modules.system
	 * @subpackage models
	 */
	class IProjectCalendarContextImplementation extends ICalendarContextImplementation {

		/**
		 * Construct calendar context helper instance
		 *
		 * @param Project $object
		 * @throws InvalidInstanceError
		 */
		function __construct(Project $object) {
			if($object instanceof Project) {
				parent::__construct($object);
			} else {
				throw new InvalidInstanceError('object', $object, 'Project');
			} // if
		} // __construct

		/**
		 * @return mixed
		 */
		function getColor() {
			$config = Calendars::getLoggedUserConfigByTypeId('Project', $this->object->getId());
			return array_var($config, 'color', Calendar::DEFAULT_COLOR);
		} // getColor

		/**
		 * @param $value
		 */
		function setColor($value) {
			$config = array(
				'color' => $value
			);
			Calendars::setConfigForLoggedUserByTypeId('Project', $this->object->getId(), $config);
		} // setColor

		/**
		 * @return mixed
		 */
		function isVisible() {
			$config = Calendars::getLoggedUserConfigByTypeId('Project', $this->object->getId());
			return array_var($config, 'visible', 1);
		} // isVisible

		/**
		 * @param bool $value
		 */
		function setVisible($value = true) {
			$config = array(
				'visible' => $value
			);
			Calendars::setConfigForLoggedUserByTypeId('Project', $this->object->getId(), $config);
		} // setVisible

		/**
		 * Return array or property => value pairs that describes this object
		 *
		 * $user is an instance of user who requested description - it's used to get
		 * only the data this user can see
		 *
		 * @param IUser $user
		 * @param boolean $detailed
		 * @param boolean $for_interface
		 * @param integer $min_state
		 * @return array
		 */
		function describe(IUser $user, $detailed = false, $for_interface = false, $min_state = STATE_VISIBLE) {
			$result = array(
				'id'          => $this->object->getId(),
				'type'        => 'Project',
				'name'        => $this->object->getName(),
				'color'       => $this->getColor(),
				'visible'     => $this->isVisible(),
				'permissions' => array(
					'can_edit'          => true,
					'can_remove'        => false,
				),
				'urls'        => array(
					'edit'              => Router::assemble('calendar_change_color_by_type', array('type' => 'project', 'type_id' => $this->object->getId())),
					'change_visibility' => Router::assemble('calendar_change_visibility_by_type', array('type' => 'project', 'type_id' => $this->object->getId())),
					'ical'              => Router::assemble('project_ical_subscribe', array('project_slug' => $this->object->getSlug()))
				)
			);

			return $result;
		} // describe
		
	}