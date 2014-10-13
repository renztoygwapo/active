<?php

  /**
   * on_notification_inspector event handler implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_notification_inspector event
   * 
   * @param NotebookPage $context
   * @param IUser $recipient
   * @param NamedList $properties
   * @param mixed $action
   * @param mixed $action_by
   */
  function notebooks_handle_on_notification_inspector(&$context, &$recipient, &$properties, &$action, &$action_by) {
    if($context instanceof NotebookPage) {
      $properties->add('notebook', array(
        'label' => lang('Notebook', null, null, $recipient->getLanguage()),
        'value' => array(
          array($context->getNotebook()->getViewUrl(), $context->getNotebook()->getName()),
        ),
      ));
    } // if
  } // notebooks_handle_on_notification_inspector