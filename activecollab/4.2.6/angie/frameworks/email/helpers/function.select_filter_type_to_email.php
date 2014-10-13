<?php

  /**
   * Render select filter type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_filter_type_to_email($params, &$smarty) {
    $filter_types = array(
      IncomingMailFilter::IM_FILTER_IS,
      IncomingMailFilter::IM_FILTER_IS_NOT,
      IncomingMailFilter::IM_FILTER_STARTS_WITH,
      IncomingMailFilter::IM_FILTER_ENDS_WITH,
      IncomingMailFilter::IM_FILTER_HAS
    );
    
    $readonly = false;
    if(isset($params['readonly'])) {
      $readonly = $params['readonly'];
      unset($params['readonly']);
    } // if
    
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
  } // smarty_function_select_filter_type_to_email
