<?php

  /**
   * Invoicing module on_object_deleted event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * on_object_deleted handler implemenation
   *
   * @param Object $object
   * @return null
   */
  function invoicing_handle_on_object_deleted($object) {
    if($object instanceof TimeRecord || $object instanceof Expense) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE item_id = ?', $object->getId());
    } // if
  } // invoicing_handle_on_object_deleted

?>