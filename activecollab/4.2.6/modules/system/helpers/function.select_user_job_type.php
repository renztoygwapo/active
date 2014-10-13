<?php

  /**
   * select_user_job_type helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select user job type select box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_user_job_type($params, &$smarty) {
    $name = array_var($params, 'name', 'job_type_id', true);
    $value = array_var($params, 'value', null, true);
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_user_job_type');
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= ' select_user_job_type';
    } else {
      $params['class'] = 'select_user_job_type';
    } // if

    $job_types = JobTypes::getIdNameMap();
    
    $options = array();
    if(is_foreachable($job_types)) {
      $group_options = array();

      foreach($job_types as $job_type_id => $job_type_name) {
        $group_options[] = HTML::optionForSelect($job_type_name, $job_type_id, $job_type_id == $value);
      } // foreach

      $options[] = HTML::optionGroup(lang('Only this Job Type'), $group_options);
    } // if
    
    return HTML::optionalSelect($name, $options, $params, lang('All Job Types'));
  } // smarty_function_select_user_job_type