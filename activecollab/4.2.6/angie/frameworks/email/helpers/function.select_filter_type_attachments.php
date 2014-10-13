<?php

  /**
   * Render select filter type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_filter_type_attachments($params, &$smarty) {
    $filter_types = array(
      IncomingMailFilter::IM_FILTER_ANY, 
      IncomingMailFilter::IM_FILTER_HAS_ATTACHMENTS, 
      IncomingMailFilter::IM_FILTER_HAS_NOT_ATTACHMENTS,
    );
    
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

?>