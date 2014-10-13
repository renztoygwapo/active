<?php


  /**
   * Render select Personality Type
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_personality_type($params, &$smarty) {
  	$name = array_var($params, 'name', null, true);
    $types = array('Driver', 'Expressive', 'Amiable', 'Antithetical');
    $render_type = array_var($params, 'render_type', null, true);
    $value = array_var($params, 'value', null, true);
    $id = array_var($params, 'id', null, true);

    if(empty($render_type)) {
      if(isset($params['value'])) {
        $value = $params['value'];
        unset($params['value']);
      } // if
      
      $options = array();
      if(array_var($params, 'optional', false, true)) {
        $options[] = option_tag(lang('-- Select Personality Type --'), '');
      } // if

      foreach($types as $type) {
        $options[] = option_tag($type, $type, array(
                'selected' => $value == $type, 
                ));
      } // foreach
      
      return HTML::select($name, $options, $params);

    } else if(!empty($render_type) && $render_type == 'hidden'){
      return "<input type='hidden' id='".$id."'value='".$value."'/>";
    } //if
    
  } // smarty_function_select_personality_type

?>