<?php

  /**
   * print_assignment_filter_row helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render single row for assignment filter print page
   * 
   * Params:
   * 
   * - assignment - array
   * - filter - AssignmentFilter
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_print_assignment_filter_row($params, &$smarty) {
    $filter = array_required_var($params, 'filter', true, 'AssignmentFilter');
    $user = array_required_var($params, 'user', true, 'User');
    $assignment = array_required_var($params, 'assignment');
    $subtask = (boolean) array_var($params, 'subtask', false, true);
    
    $result = '<tr>';
    
    // Priority
    $result .= '<td class="priority">';
    switch($assignment['priority']) {
      case PRIORITY_LOWEST:
        $result .= lang('Lowest');
        break;
      case PRIORITY_LOW:
        $result .= lang('Low');
        break;
      case PRIORITY_HIGH:
        $result .= lang('High');
        break;
      case PRIORITY_HIGHEST:
        $result .= lang('Highest');
        break;
      default:
        $result .= lang('Normal');
    } // switch
    
    $result .= '</td>';
    
    // Label
    $result .= '<td class="label">' . clean(Labels::getLabelName($assignment['label_id'])) . '</td>';
    
    // Name
    if($subtask) {
      $result .= '<td class="name">&nbsp;&nbsp;&nbsp;&nbsp;(' . lang('Subtask') . ') ' . clean($assignment['body']) . '</td>';
    } else {
      if($assignment['type'] == 'Task' && isset($assignment['task_id'])) {
        $result .= '<td class="name">(' . lang($assignment['type']) . ') #' . $assignment['task_id'] . ': ' . clean($assignment['name']) . '</td>';
      } else {
        $result .= '<td class="name">(' . lang($assignment['type']) . ') ' . clean($assignment['name']) . '</td>';
      } // if
    } // if
    
    $additional_columns = array();
    
    if($filter->getAdditionalColumn1() && $filter->getAdditionalColumn1() != 'none') {
      $additional_columns['additional_column_1'] = $filter->getAdditionalColumn1();
    } // if
    
    if($filter->getAdditionalColumn2() && $filter->getAdditionalColumn2() != 'none') {
      $additional_columns['additional_column_2'] = $filter->getAdditionalColumn2();
    } // if
    
    if(count($additional_columns)) {
      foreach($additional_columns as $k => $v) {
        $result .= '<td class="' . $k . '">';
        
        switch($v) {
          case 'assignee':
            $result .= isset($assignment['assignee']) && $assignment['assignee'] ? clean($assignment['assignee']) : lang('Unassigned');
            break;
          case 'project':
          case 'category':
          case 'milestone':
          case 'created_by':
            $result .= isset($assignment[$v]) && $assignment[$v] ? clean($assignment[$v]) : '--';
            break;
          case 'created_on':
            $result .= $assignment['created_on'] instanceof DateTimeValue ? $assignment['created_on']->formatDateForUser($user, 0) : lang('Unknown');
            break;
          case 'age':
            if($assignment['age'] == 1) {
              $result .= lang('One Day');
            } else {
              $result .= lang(':num Days', array(
                'num' => $assignment['age'],
              ));
            } // if
            break;
          case 'due_on':
            $result .= $assignment['due_on'] instanceof DateValue ? $assignment['due_on']->formatForUser($user, 0) : lang('Not Set');
            break;
          case 'completed_on':
            $result .= $assignment['completed_on'] instanceof DateTimeValue ? $assignment['completed_on']->formatDateForUser($user, 0) : lang('Open');
            break;
          case 'estimated_time':
            $result .= isset($assignment[$v]) && $assignment[$v] ? (float_format($assignment['estimated_time']) . 'h ' . lang('of') . ' ' . JobTypes::findById($assignment['estimated_job_type_id'])->getName()) : lang('Empty');
            break;
          case 'tracked_time':
            $result .= isset($assignment[$v]) && $assignment[$v] ? float_format($assignment[$v]) . 'h' : lang('Empty');
            break;
        } // switch
        
        $result .= '</td>';
      } // foreach
    } // if
    
    return "$result</tr>";
  } // smarty_function_print_assignment_filter_row