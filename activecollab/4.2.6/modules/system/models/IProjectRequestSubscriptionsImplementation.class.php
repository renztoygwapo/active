<?php

  /**
   * Project request subscriptions implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectRequestSubscriptionsImplementation extends ISubscriptionsImplementation {
    
    /**
     * Construct project object subscriptions implementation
     *
     * @param ISubscriptions $object
     */
    function __construct(ISubscriptions $object) {
      if($object instanceof ProjectRequest) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectRequest');
      } // if
    } // __construct

    /**
     * @param User $user
     * @return array|void
     */
    function getAvailableUsers(User $user) {
      return Users::find(array(
        "conditions" => array("state >= ?", STATE_VISIBLE)
      ));
    } // getAvailableUsers

    /**
     * Returns true if $user can subscribe to this object
     *
     * @param User $user
     * @return boolean
     */
    function canSubscribe(User $user) {
      return true;
    } // canSubscribe
    
  }