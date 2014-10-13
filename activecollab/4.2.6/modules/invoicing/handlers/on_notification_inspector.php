<?php

  /**
   * on_notification_inspector event handler implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_notification_inspector event
   * 
   * @param Invoice $context
   * @param IUser $recipient
   * @param NamedList $properties
   * @param mixed $action
   * @param mixed $action_by
   */
  function invoicing_handle_on_notification_inspector(&$context, &$recipient, &$properties, &$action, &$action_by) {
    
    // Invoice
    if ($context instanceof Invoice) {

      if($context->isIssued()) {
        $properties->add('issued_on', array(
          'label' => lang('Issued On', null, null, $recipient->getLanguage()), 
          'value' => array($context->getIssuedOn()->formatDateForUser($recipient, 0)), 
        ));
        $properties->add('due_on', array(
          'label' => lang('Payment Due On', null, null, $recipient->getLanguage()),
          'value' => array($context->getDueOn()->formatForUser($recipient, 0)), 
        ));
      } else if ($context->isCanceled()) {
        $properties->add('canceled_on', array(
          'label' => lang('Canceled On', null, null, $recipient->getLanguage()), 
          'value' => array($context->getClosedOn()->formatDateForUser($recipient, 0)), 
        ));
      } else if ($context->isPaid()) {
        $properties->add('paid_on', array(
          'label' => lang('Paid On', null, null, $recipient->getLanguage()), 
          'value' => array($context->getClosedOn()->formatDateForUser($recipient, 0)), 
        ));
      } // if
      
      if($context->getStatus() > INVOICE_STATUS_ISSUED) {
        $action = lang('Issued', null, null, $recipient);
        $action_by = $context->getIssuedBy();
      } // if
      
    // Quote
    } elseif ($context instanceof Quote) {
      
    } // if
    
  } // invoicing_handle_on_notification_inspector