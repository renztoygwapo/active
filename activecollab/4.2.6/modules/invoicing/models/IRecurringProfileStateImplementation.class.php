<?php

/**
 * Recurring Profile implementation
 *
 * @package activeCollab.modules.invoicing
 * @subpackage models
 */
class IRecurringProfileStateImplementation extends IInvoiceObjectStateImplementation {

  /**
   * Construct Recurring Profile state helper
   *
   * @param RecurringProfile $object
   */
  function __construct(RecurringProfile $object) {
    if($object instanceof RecurringProfile) {
      parent::__construct($object);
    } else {
      throw new InvalidInstanceError('object', $object, 'RecurringProfile');
    } // if
  } // __construct

}