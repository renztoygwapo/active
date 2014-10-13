<?php

  /**
   * select_job_type helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */

  /**
   * Render select job type box
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_job_type($params, &$smarty) {
    if(AngieApplication::isModuleLoaded('tracking')) {
      $user = array_required_var($params, 'user', true, 'IUser');

      $name = array_required_var($params, 'name', true);
      $value = array_var($params, 'value', null, true);
      
      if(empty($value)) {
        $value = JobTypes::getDefaultJobTypeId();
      } // if
      
      if(isset($params['class'])) {
        $params['class'] .= ' select_job_type';
      } else {
        $params['class'] = 'select_job_type';
      } // if
      
      if(array_var($params, 'short', false, true)) {
        $params['class'] .= ' short';
      } // if

      if($user->isProjectManager() || !ConfigOptions::hasValueFor('job_type_id', $user)) {
        $job_types = JobTypes::getIdNameMap($value, JOB_TYPE_ACTIVE, array_var($params, 'exclude_ids', null, true));
      } else {
        $job_type_id = ConfigOptions::getValueFor('job_type_id', $user);
        $job_type_name = JobTypes::getNameById($job_type_id);

        $job_types = array($job_type_id => $job_type_name);
      } // if

      return HTML::selectFromPossibilities($name, $job_types, $value, $params);
    } // if

    return '';
  } // smarty_function_select_job_type