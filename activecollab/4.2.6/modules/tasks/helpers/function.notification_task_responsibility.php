<?php

  /**
   * notification_task_responsibility helper implementation
   *
   * @package activecollab.modules.tasks
   * @subpackage helpers
   */

  /**
   * Render new task responisble person on notification
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_task_responsibility($params, &$smarty) {
    $context = array_required_var($params, 'context', false, 'ApplicationObject');
    $recipient = array_required_var($params, 'recipient', false, 'IUser');

    $language = $recipient->getLanguage();

    $result = '';

    if($context->assignees()->getAssignee() instanceof IUser) {

      // Recipient is responsible
      if($context->assignees()->isResponsible($recipient)) {
        $result = lang('<u>You are responsible</u> for this :type!', array(
          'type' => $context->getVerboseType(true, $language)
        ), true, $language);

      // Recipient is assigned
      } elseif($context->assignees()->isAssignee($recipient)) {
        $result = lang('<u>You are assigned</u> to this :type and :responsible_name is responsible.', array(
          'type' => $context->getVerboseType(true, $language),
          'responsible_name' => $context->assignees()->getAssignee()->getDisplayName(true)
        ), true, $language);

      // Someone else is assignee
      } elseif($context->assignees()->getAssignee() instanceof User) {
        $result = lang(':responsible_name is responsible for this :type.', array(
          'type' => $context->getVerboseType(true, $language),
          'responsible_name' => $context->assignees()->getAssignee()->getDisplayName(true)
        ), true, $language);
      } // if
      
      if($context->getDueOn()) {
        $result .= ' ' . lang('It is due on <u>:due_on</u>', array(
          'due_on' => $context->getDueOn()->formatForUser($recipient, 0)
        ), true, $language);
      } // if
    } // if

    return $result;
  } // smarty_function_notification_task_responsibility