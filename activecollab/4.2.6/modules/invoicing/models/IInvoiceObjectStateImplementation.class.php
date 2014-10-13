<?php

/**
 * Invoice object state implementation
 *
 * @package activeCollab.modules.invoicing
 * @subpackage models
 */
class IInvoiceObjectStateImplementation extends IStateImplementation {

  /**
   * Construct invoice object state helper
   *
   * @param InvoiceObject $object
   */
  function __construct(InvoiceObject $object) {
    if($object instanceof InvoiceObject) {
      parent::__construct($object);
    } else {
      throw new InvalidInstanceError('object', $object, 'InvoiceObject');
    } // if
  } // __construct

}