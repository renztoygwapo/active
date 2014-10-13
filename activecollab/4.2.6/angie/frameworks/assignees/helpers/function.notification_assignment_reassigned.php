<?php

  /**
   * notification_assignment_reassigned helper implementation
   *
   * @package angie.frameworks.assignees
   * @subpackage helpers
   */

  /**
   * Render due on and new assignee on notification when changing assignee
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_assignment_reassigned($params, &$smarty) {
    $context = array_required_var($params, 'context', false, 'ApplicationObject');
    $language = array_required_var($params, 'language', false, 'Language');
    $recipient = array_required_var($params, 'recipient', false, 'IUser');
    $reassigned_by_name = array_var($params, 'reassigned_by_name', null, false);
    $url = array_var($params, 'url', null, false);
    $link_style = array_var($params, 'link_style', null, false);

    $result = lang(':reassigned_by_name has just made you responsible for "<a href=":url" style=":link_style" target="_blank">:name</a>" :type.', array(
    	'reassigned_by_name' => $reassigned_by_name,
      'name' => $context->getName(),
      'type' => $context->getVerboseType(true, $language),
      'link_style' => $link_style,
      'url' => $url
    ), true, $language);

    if(method_exists($context,'getDueOn') && $context->getDueOn()) {
      $result .= ' ' . lang('It is due on <u>:due_on</u>', array(
        'due_on' => $context->getDueOn()->formatForUser($recipient, 0)
      ), true, $language);
    } // if
      
    return $result;
  } // smarty_function_notification_assignment_reassigned