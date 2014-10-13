<?php

/**
 * Invoice state implementation
 *
 * @package activeCollab.modules.invoicing
 * @subpackage models
 */
class IInvoiceStateImplementation extends IInvoiceObjectStateImplementation {

  /**
   * Construct Invoice state helper
   *
   * @param Invoice $object
   */
  function __construct(Invoice $object) {
    if($object instanceof Invoice) {
      parent::__construct($object);
    } else {
      throw new InvalidInstanceError('object', $object, 'Invoice');
    } // if
  } // __construct

  /** Mark object as deleted
  *
  * @throws NotImplementedError
  */
  function delete() {
    $this->object->setTimeRecordsStatus(BILLABLE_STATUS_BILLABLE);
    $this->object->releaseTimeRecords();
    $this->object->setExpensesStatus(BILLABLE_STATUS_BILLABLE);
    $this->object->releaseExpenses();
    parent::delete();
  } //delete

  /**
   * Returns true if $user can mark this object as archived
   *
   * @param User $user
   * @return boolean
   */
  function canArchive(User $user) {
    if (!($this->object->isPaid() || $this->object->isCanceled())) {
      return false;
    } // if

    if ($this->object->getState() != STATE_VISIBLE) {
      return false;
    } // if

    return $user->getSystemPermission('can_manage_finances');
  } // canArchive

  /**
   * Can unarchive
   *
   * @param User $user
   * @return boolean
   */
  function canUnarchive(User $user) {
    if ($this->object->getState() != STATE_ARCHIVED) {
      return false;
    } // if

    return $user->getSystemPermission('can_manage_finances');
  } // canUnarchive

  /**
   * Can trash
   *
   * @param User $user
   * @return bool
   */
  function canTrash(User $user) {
    return false;
  } // canTrash

  /**
   * Can untrash
   *
   * @param User $user
   * @return bool|void
   */
  function canUntrash(User $user) {
    return false;
  } // canUntrash
}