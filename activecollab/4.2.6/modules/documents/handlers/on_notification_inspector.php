<?php

  /**
   * on_notification_inspector event handler implementation
   * 
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_notification_inspector event
   * 
   * @param Document $context
   * @param IUser $recipient
   * @param NamedList $properties
   * @param mixed $action
   * @param mixed $action_by
   */
  function documents_handle_on_notification_inspector(&$context, &$recipient, &$properties, &$action, &$action_by) {
    if($context instanceof Document) {
      if($context->getType() == 'file') {
        $properties->add('file_size', array(
          'label' => lang('Size', null, null, $recipient->getLanguage()), 
          'value' => array(format_file_size($context->getSize())), 
        ));
      } // if
      
      if($context->category()->get() instanceof Category) {
        $properties->add('category', array(
          'label' => lang('Category', null, null, $recipient->getLanguage()), 
          'value' => array(
            array($context->category()->get()->getViewUrl(), $context->category()->get()->getName()), 
          ), 
        ));
      } // if
    } // if
  } // documents_handle_on_notification_inspector