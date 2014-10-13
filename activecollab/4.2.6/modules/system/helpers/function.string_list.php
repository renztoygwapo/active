<?php

  /**
   * string_list widget definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render string list widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_string_list($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $link_title = array_var($params, 'link_title', lang('Add New'));
    
  	$id = array_var($params, 'id');
    if(empty($id)) {
      $id = HTML::uniqueId('string_list');
    } // if
    
    $value = array_var($params, 'value');
    
    $smarty->assign(array(
      '_string_list_name' => $name,
      '_string_list_id' => $id,
      '_string_list_value' => $value,
      '_string_list_link_title' => $link_title
    ));

    AngieApplication::useWidget('string_list', ENVIRONMENT_FRAMEWORK);
    
    return $smarty->fetch(get_view_path('_string_list', null, SYSTEM_MODULE));
  } // smarty_function_string_list