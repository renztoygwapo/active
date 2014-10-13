<?php

/**
 * bubble_options helper
 *
 * @package angie.frameworks.calendars
 * @subpackage helpers
 */

/**
 * Render bubble options picker
 *
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_bubble_options($params, &$smarty) {
	$object = array_required_var($params, 'object', true, 'ApplicationObject');
	$user = array_required_var($params, 'user', true, 'User');

	if (!($object instanceof ApplicationObject)) {
		return false;
	} // if

	// check if there is a method for permission to see this object
	if (method_exists($object, 'canView') && !$object->canView($user)) {
		return false;
	} // if

	$options = $object->getOptions($user);
	$options->remove('edit');
	$options->remove('archive');

	if (is_foreachable($options)) {
		$data = $options->toArray();
//		$unique_id = HTML::uniqueId('bubble_options_');
		return HTML::openTag('div', array('id' => $unique_id)) . '</div><script type="text/javascript">$(".bubble").bubbleOptions(' . JSON::encode($data) . ');</script>';
	} // if
} // smarty_function_bubble_options