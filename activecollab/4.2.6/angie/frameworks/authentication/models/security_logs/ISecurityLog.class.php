<?php

/**
 * Security log interface
 *
 * @package angie.frameworks.authentication
 * @subpackage models
 */
interface ISecurityLog {

	/**
	 * Return security log helper instance
	 *
	 * @return ISecurityLogImplementation
	 */
	function securityLog();

}