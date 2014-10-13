<?php

  /**
   * object_estimate helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */

  /**
   * Render object estimate value and change option
   * 
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_object_estimate($params, &$smarty) {
    if (AngieApplication::isModuleLoaded('tracking')) {
      $object = array_required_var($params, 'object', true, 'ITracking');
      $user = array_required_var($params, 'user', true, 'IUser');
      
      $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('object_estimate');
      
      $estimate = $object->tracking()->getEstimate();
      
      if($estimate instanceof Estimate) {
        $estimate_value = $estimate->getValue();
        $estimate_job_type_id = $estimate->getJobType() instanceof JobType ? $estimate->getJobType()->getId() : null;
        $estimate_job_type_name = $estimate->getJobType() instanceof JobType ? $estimate->getJobType()->getName() : null;
        $estimate_comment = $estimate->getComment();
      } else {
        $estimate_value = 0;
        $estimate_job_type_id = 0;
        $estimate_job_type_name = '';
        $estimate_comment = '';
      } // if
      
      $settings = array(
        'value' => $estimate_value, 
        'job_type_id' => $estimate_job_type_id, 
        'job_type_name' => $estimate_job_type_name, 
        'comment' => $estimate_comment, 
        'short_format' => array_var($params, 'short', true), 
        'can_change' => $object->tracking()->canEstimate($user), 
        'estimates_url' => $object->tracking()->getEstimatesUrl(), 
        'set_estimate_url' => $object->tracking()->getSetEstimateUrl()
      );

      AngieApplication::useWidget('object_estimate', TRACKING_MODULE);
      return '<span id="' . $id . '"></span><script type="text/javascript">$("#' . $id . '").objectEstimate(' . JSON::encode($settings) . ');</script>';
    } else {
      return '';
    } // if
  } // smarty_function_object_estimate