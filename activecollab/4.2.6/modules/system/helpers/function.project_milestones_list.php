<?php

/**
 * project_milestones_list helper
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Render project milestones list
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_project_milestones_list($params, &$smarty) {
	$project = array_required_var($params, 'project',true,'Project');
	$user = array_required_var($params, 'user', true, 'User');

	$print = '<div class="milestone_list"><ul>';

	$milestones = $project instanceof Project ? Milestones::findAllByProject($project) : null;

	$first_milestone_starts_on = Milestones::getFirstMilestoneStartsOn($project);

	if (is_foreachable($milestones)) {
		foreach($milestones as $milestone) {
			if ($milestone instanceof Milestone) {
				$days_between = '';
				$date_text = lang('not scheduled');
				if ($milestone->getStartOn() instanceof DateValue) {
					$days_between = $milestone->getStartOn()->daysBetween($first_milestone_starts_on);
					$date_text = $project->getCreatedOn()->advance($days_between * 86400, false)->format('Y/m/d');
				} // if
				$print .= '<li><span class="milestone_name">' . $milestone->getName() . '</span><span class="milestone_date" data-days-between="' . $days_between . '">' . $date_text . '</span></li>';
			} // if
		} // foreach
	} else {
		$print .= '<li><p class="empty_page">' . lang('No Milestones defined') . '</p></li>';
	} // if

	return $print . '</ul></div>';
} // smarty_function_project_milestones_list