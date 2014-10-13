<?php

/**
 * project due helper
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Print project due on string (due in, due today or late) for a given object
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_project_due($params, &$smarty) {
  $project_id = array_var($params, 'project_id');
  $due_on = array_var($params, 'due_on');

  $due_date = $due_on ? new DateValue(strtotime($due_on)) : null;
  $project = Projects::findById($project_id);

  if($project instanceof ApplicationObject) {
    if($project instanceof IComplete && $project->complete()->isCompleted()) {
      return lang('Completed');
    } // if
  } else {
    throw new InvalidInstanceError('object', $project, 'ApplicationObject');
  } // if

  $offset = get_user_gmt_offset();

  if($due_date instanceof DateValue) {
    AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

    $date = smarty_modifier_date($due_date, 0); // just printing date, offset is 0!

    if($due_date->isToday($offset)) {
      return '<span class="today">' . lang('Due Today') . '</span>';
    } elseif($due_date->isYesterday($offset)) {
      return '<span class="late" title="' . clean($date) . '">' . lang('1 Day Late') . '</span>';
    } elseif($due_date->isTomorrow($offset)) {
      return '<span class="upcoming" title="' . clean($date) . '">' . lang('Due Tomorrow') . '</span>';
    } else {
      $now = new DateTimeValue();
      $now->advance($offset);
      $now = $now->beginningOfDay();

      $due_date->beginningOfDay();

      if($due_date->getTimestamp() > $now->getTimestamp()) {
        return '<span class="upcoming" title="' . clean($date) . '">' . lang('Due in :days Days', array('days' => floor(($due_date->getTimestamp() - $now->getTimestamp()) / 86400))) . '</span>';
      } else {
        return '<span class="late" title="' . clean($date) . '">' . lang(':days Days Late', array('days' => floor(($now->getTimestamp() - $due_date->getTimestamp()) / 86400))) . '</span>';
      } // if
    } // if
  } else {
    return lang('No Due Date');
  } // if
} // smarty_function_due

?>