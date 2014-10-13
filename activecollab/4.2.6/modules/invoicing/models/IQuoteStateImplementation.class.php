<?php

/**
 * Quote state implementation
 *
 * @package activeCollab.modules.invoicing
 * @subpackage models
 */
class IQuoteStateImplementation extends IInvoiceObjectStateImplementation {

  /**
   * Construct Quote state helper
   *
   * @param Quote $object
   */
  function __construct(Quote $object) {
    if($object instanceof Quote) {
      parent::__construct($object);
    } else {
      throw new InvalidInstanceError('object', $object, 'Quote');
    } // if
  } // __construct

  /**
   * Returns true if $user can mark this object as archived
   *
   * @param User $user
   * @return boolean
   */
  function canArchive(User $user) {
    if (!($this->object->isWon() || $this->object->isLost())) {
      return false;
    }

    if ($this->object->getState() != STATE_VISIBLE) {
      return false;
    } // if

    return $user->getSystemPermission('can_manage_quotes');
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

    return $user->getSystemPermission('can_manage_quotes');
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