<?php

  /**
   * Render select filter type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_filter_type_sender($params, &$smarty) {
    $filter_types = array(
      IncomingMailFilter::IM_FILTER_ANY, 
      IncomingMailFilter::IM_FILTER_ONLY_REGISTERED, 
      IncomingMailFilter::IM_FILTER_ONLY_NOT_REGISTERED   
    );
    
    $readonly = false;
    if(isset($params['readonly'])) {
      $readonly = $params['readonly'];
      unset($params['readonly']);
    } // if
    
    if(!$readonly) {
      $filter_types[] =  IncomingMailFilter::IM_FILTER_IS;
      $filter_types[] =  IncomingMailFilter::IM_FILTER_IS_NOT;
      $filter_types[] =  IncomingMailFilter::IM_FILTER_STARTS_WITH;
      $filter_types[] =  IncomingMailFilter::IM_FILTER_ENDS_WITH;
      $filter_types[] =  IncomingMailFilter::IM_FILTER_HAS;
    }//if
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($filter_types as $filter_type) {
      $option_attributes = $filter_type == $value ? array('selected' => true) : null;
      $options[] = option_tag($filter_type, $filter_type, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_filter_type_sender
