<?php

/**
 * Project object specific activity logs
 *
 * @package activeCollab.modules.system
 * @subpackage models
 */
class ICalendarEventActivityLogsImplementation extends IActivityLogsImplementation {

	/**
	 * Return target for given action
	 *
	 * @param string $action
	 * @return Project
	 */
	function getTarget($action = null) {
		return $this->object->getParent();
	} // getTarget

}