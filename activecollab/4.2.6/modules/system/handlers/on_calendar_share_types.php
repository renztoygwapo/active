<?php

/**
 * on_calendar_share_types event handler implementation
 *
 * @package activeCollab.modules.system
 * @subpackage handlers
 */

/**
 * Handle on_calendar_share_types event
 *
 * @param $options NamedList
 */
function system_handle_on_calendar_share_types(&$options) {
	$options->addAfter(Calendar::SHARE_WITH_TEAM_AND_SUBCONTRACTORS, lang('Our Team and Subcontractors'), Calendar::SHARE_WITH_EVERYONE);
	$options->addAfter(Calendar::SHARE_WITH_MANAGERS_ONLY, lang('Managers Only'), Calendar::SHARE_WITH_TEAM_AND_SUBCONTRACTORS);
} // system_handle_on_calendar_share_types