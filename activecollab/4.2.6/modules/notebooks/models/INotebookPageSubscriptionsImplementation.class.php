<?php

  /**
   * Notebook page subscriptions implementation
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookPageSubscriptionsImplementation extends ISubscriptionsImplementation {
    
    /**
     * Construct notebook page subscriptions implementation
     *
     * @param ISubscriptions $object
     */
    function __construct(ISubscriptions $object) {
      if($object instanceof NotebookPage) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'NotebookPage');
      } // if
    } // __construct
    
    /**
     * Return array of available users
     *
     * @param User $user
     * @return array
     */
    function getAvailableUsers($user) {
      return $this->object->getProject()->users()->get($user);
    } // getAvailableUsers
    
    /**
     * Return available users for select
     *
     * @param User $user
     * @param mixed $exclude_ids
     * @return array
     */
    function getAvailableUsersForSelect(User $user, $exclude_ids = null) {
      return $this->object->getProject()->users()->getForSelect($user, $exclude_ids);
    } // getAvailableUsersForSelect
    
  }