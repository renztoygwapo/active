<?php

  /**
   * select_company helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select company box
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_mail_to_project_action($params, &$smarty) {
    $name = array_var($params,'name',null,true);
    $value = array_var($params, 'value', null, true);
    
    $actions = array(
      MailToProjectInterceptor::ACTION_TASK => lang('Task'),
      MailToProjectInterceptor::ACTION_DISCUSSION => lang('Discussion')
    );

    foreach($actions as $key => $v) {
      $options[] = option_tag($v, $key, array(
        'class' => 'object_option',
        'selected' => $value == $key,
      ));
    } //foreach

    return HTML::select($name, $options, $params);
  } // smarty_function_select_company