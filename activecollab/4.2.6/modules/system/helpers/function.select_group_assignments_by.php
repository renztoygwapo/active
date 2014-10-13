<?php

  /**
   * select_group_assignments_by helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select group assignments by picker
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_group_assignments_by($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', AssignmentFilter::DONT_GROUP);
    
    $possibilities = array( 
      AssignmentFilter::GROUP_BY_ASSIGNEE => lang("Assignee"),
      AssignmentFilter::GROUP_BY_PROJECT => lang("Project"),
      AssignmentFilter::GROUP_BY_PROJECT_CLIENT => lang("Project Client"),
      AssignmentFilter::GROUP_BY_MILESTONE => lang("Milestone"),
      AssignmentFilter::GROUP_BY_CATEGORY => lang("Category"),
      AssignmentFilter::GROUP_BY_LABEL => lang("Label"),
      AssignmentFilter::GROUP_BY_CREATED_ON => lang("Creation Date"),
      AssignmentFilter::GROUP_BY_DUE_ON => lang("Due Date"),
      AssignmentFilter::GROUP_BY_COMPLETED_ON  => lang("Completion Date"),
    );

    if(isset($params['exclude']) && $params['exclude']) {
      $exclude_options = explode(',', $params['exclude']);

      if($exclude_options) {
        foreach($exclude_options as $exclude_option) {
          if(isset($possibilities[$exclude_option])) {
            unset($possibilities[$exclude_option]);
          } // if
        } // foreach
      } // if
    }
    
    $options = array();
    
    foreach($possibilities as $k => $v) {
      $options[] = HTML::optionForSelect($v, $k, $k == $value);
    } // foreach
    
    $options = array(
      HTML::optionForSelect(lang("Don't Group"), AssignmentFilter::DONT_GROUP, (empty($value) || $value == AssignmentFilter::DONT_GROUP)), 
      HTML::optionForSelect('', ''), 
      HTML::optionGroup(lang('Group By'), $options), 
    );
    
    if(array_var($params, 'optional', false, true)) {
      return HTML::optionalSelect($name, $options, $params);
    } else {
      return HTML::select($name, $options, $params);
    } // if
  } // smarty_function_select_group_assignments_by