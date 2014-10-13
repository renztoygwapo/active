<?php

  /**
   * Invoicing module on_object_inspector events handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Populate object inspector
   *
   * @param IInspectorImplementation $inspector
   * @param IInspector $object
   * @param IUser $user
   * @param string $interface
   */
	function invoicing_handle_on_object_inspector(IInspectorImplementation &$inspector, IInspector &$object, IUser &$user, $interface) {

    // Invoice
    if ($object instanceof Invoice) {
      
      if(!$user->isAdministrator() && !$user->isFinancialManager()) {
        $inspector->removeProperty('created_on');
      }//if
       
      $inspector->addProperty('issued_by', lang('Issued'),  new ActionOnByInspectorProperty($object, 'issued', false));

      if($user->isAdministrator() || $user->isFinancialManager()) {
        $inspector->addProperty('paid_by', lang('Paid'), new ActionOnByInspectorProperty($object, 'paid'));
      }//if
      
      if($user->isAdministrator() || $user->isFinancialManager()) {
        $inspector->addProperty('canceled_by', lang('Canceled'), new ActionOnByInspectorProperty($object, 'closed'));
      }//if
      
      if($object->getBasedOn()) {
        if($user->isAdministrator() || $user->isFinancialManager()) {
      	  $inspector->addProperty('based_on', lang('Based On'), new SimplePermalinkInspectorProperty($object, 'based_on.permalink', 'based_on.name'));
        } else {
          $inspector->addProperty('based_on', lang('Based On'), new SimpleFieldInspectorProperty($object, 'based_on.name'));
        }//if
      }//if

      $inspector->addProperty('purchase_order_number', lang('PO Number'), new SimpleFieldInspectorProperty($object, 'purchase_order_number'));
      
    } // if
    
    /**
     * Quote
     */
    if ($object instanceof Quote) {
      if ($object->getBasedOn() && ProjectRequests::canManage($user)) {
        $inspector->addProperty('based_on', lang('Based On'), new SimplePermalinkInspectorProperty($object, 'based_on.permalink', 'based_on.name'));
      } // if

      $sufix = null;
      if ($object->getStatus() !== QUOTE_STATUS_DRAFT && $object->getDate($object->getStatus()) instanceof DateTimeValue) {
        $sufix = " " . lang('(:date)', array('date' => $object->getDate($object->getStatus())->formatDateForUser($user)));
      } // if

      $inspector->addProperty('status', lang('Status'), new SimpleFieldInspectorProperty($object, 'verbose_status', array('sufix' => $sufix)));

      if (Quotes::canManage($user) || !($object->isPublicPageExpired())) {
        $inspector->addProperty('public_url', lang('Public URL'), new SimplePermalinkInspectorProperty($object, 'public_url', null, array(
          'target' => '_blank',
          'quick_view' => false,
        )));
      } // if
    } // if
    
    // Recurring Profile
    if ($object instanceof RecurringProfile) {
    	$inspector->addProperty('starts_on', $object->isStarted() ? lang('Started On') : lang('Starts On'), new SimpleFieldInspectorProperty($object, 'start_on.formatted_gmt'));
    	$inspector->addProperty('frequency', lang('Frequency'), new SimpleFieldInspectorProperty($object, 'frequency'));
    	$inspector->addProperty('occurrence', lang('Occurrence'), new SimpleFieldInspectorProperty($object, 'occurrence_left'));
    	
    	if(!$object->isArchived()) {
    	  $inspector->addProperty('next_triggers', lang('Next Trigger On'), new SimpleFieldInspectorProperty($object, 'next_trigger_on.formatted_gmt'));
    	}//if
    	
    	$inspector->addProperty('auto_issue', lang('Auto issue'), new SimpleBooleanInspectorProperty($object, 'auto_issue', lang('Yes'), lang('No')));
    	$inspector->addProperty('invoice_due_after', lang('Invoice due after'), new SimpleFieldInspectorProperty($object, 'invoice_due_after'));
//    	$inspector->addProperty('allow_payments', lang('Allow Payments'), new SimpleFieldInspectorProperty($object, 'allow_payments_text'));
    	$inspector->addProperty('notified_on', lang('Notified On'), new SimpleFieldInspectorProperty($object, 'notified_on.formatted_date'));    	
    	$inspector->addProperty('purchase_order_number', lang('PO Number'), new SimpleFieldInspectorProperty($object, 'purchase_order_number'));
    } // if

  } // invoicing_handle_on_object_inspector