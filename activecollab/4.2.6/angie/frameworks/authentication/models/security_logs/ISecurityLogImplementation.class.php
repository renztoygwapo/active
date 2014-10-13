<?php

/**
 * Security log helper implementation
 *
 * @package angie.frameworks.authentication
 * @subpackage models
 */
class ISecurityLogImplementation {

	/**
	 * Parent object
	 *
	 * @var ISecurityLog
	 */
	protected $object;

	/**
	 * Construct security log helper instance
	 *
	 * @param ISecurityLog $object
	 */
	function __construct(ISecurityLog $object) {
		$this->object = $object;
	} // __construct

	/**
	 * LOG SECURITY
	 *
	 * @param string $event
	 * @param null $by
	 */
	function log($event, $by = null, $is_api = false) {
		SecurityLogs::write($this->object, $event, $by, $is_api);
	} // log

}