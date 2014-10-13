<?php

  /**
   * on_notification_inspector event handler implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_notification_inspector event
   * 
   * @param ProjectObject $context
   * @param IUser $recipient
   * @param NamedList $properties
   * @param mixed $action
   * @param mixed $action_by
   */
  function system_handle_on_notification_inspector(&$context, &$recipient, &$properties, &$action, &$action_by) {
    if ($recipient instanceof User && $context instanceof ProjectObject) {
      if($context->getMilestone() instanceof Milestone && $context->getMilestone()->getState() > STATE_ARCHIVED) {
        $properties->add('project_and_milestone', array(
          'label' => lang('Project', null, null, $recipient->getLanguage()), 
          'value' => array(
            array($context->getProject()->getViewUrl(), $context->getProject()->getName()), 
            array($context->getMilestone()->getViewUrl(), $context->getMilestone()->getName()), 
          )
        ));
      } else {
        $properties->add('project_and_milestone', array(
          'label' => lang('Project', null, null, $recipient->getLanguage()), 
          'value' => array(
            array($context->getProject()->getViewUrl(), $context->getProject()->getName()), 
          ), 
        ));
      } // if
    } // if
  } // system_handle_on_notification_inspector