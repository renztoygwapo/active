<?php

/**
 * Handle on_history_field_renderers event
 *
 * @package activeCollab.modules.discussions
 * @subpackage handlers
 */

/**
 * Get history changes as log text
 *
 * @param $object
 * @param $renderers
 */
function discussions_handle_on_history_field_renderers(&$object, &$renderers) {
	if ($object instanceof Discussion) {
		$renderers['boolean_field_1'] = function($old_value, $new_value) {
			if($new_value) {
				return lang('Pinned');
			} else {
				return lang('Unpinned');
			} // if
		};
	} // if
}