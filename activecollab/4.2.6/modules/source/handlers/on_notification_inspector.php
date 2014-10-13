<?php

/**
 * on_notification_inspector event handler implementation
 *
 * @package activeCollab.modules.source
 * @subpackage handlers
 */

/**
 * Handle on_notification_inspector event
 *
 * @param ProjectSourceRepository $context
 * @param IUser $recipient
 * @param NamedList $properties
 * @param mixed $action
 * @param mixed $action_by
 */
function source_handle_on_notification_inspector(&$context, &$recipient, &$properties, &$action, &$action_by) {
  if($context instanceof ProjectSourceRepository) {
    $properties->add('branch_name', array(
      'label' => lang('Branch', null, null, $recipient->getLanguage()),
      'value' => array($context->active_branch),
    ));
  } // if
} // documents_handle_on_notification_inspector