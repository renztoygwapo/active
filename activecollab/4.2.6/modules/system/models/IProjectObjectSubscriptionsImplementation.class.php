<?php

  /**
   * Project object subscriptions implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectObjectSubscriptionsImplementation extends ISubscriptionsImplementation {
    
    /**
     * Construct project object subscriptions implementation
     *
     * @param ISubscriptions $object
     */
    function __construct(ISubscriptions $object) {
      if($object instanceof ProjectObject) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectObject');
      } // if
    } // __construct
    
    /**
     * Clone subscriptions from this project object to another object
     *
     * @param ISubscriptions $to
     */
//    function cloneTo(ISubscriptions $to) {
//      $project = $to->getProject(); // We need it to check if user has access to a given project
//      
//    	$rows = DB::execute('SELECT user_id FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ? user_id > ?', get_class($this->object), $this->object->getId(), 0);
//    	if(is_foreachable($rows)) {
//    	  foreach($rows as $row) {
//      	  $user = Users::findById($row['user_id']);
//      	  if($user instanceof User && $project->users()->isMember($user)) {
//      	    $to->subscriptions()->subscribe($user);
//      	  } // if
//    	  } // if
//    	} // if
//    	return true;
//    } // cloneTo

    /**
     * Return array of available users
     *
     * @param User $user
     * @return array
     */
    function getAvailableUsers($user) {
      $users = $this->object->getProject()->users()->get($user);

      // take out users without private permission if this object is private
      if ($this->object instanceof IVisibility && $this->object->getVisibility() == VISIBILITY_PRIVATE) {
        $users = $this->object->visibility()->filterUsersWithoutPrivatePermission($users);
      } //if

      return $users;
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
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can subscribe to this object
     *
     * @param User $user
     * @return boolean
     */
    function canSubscribe(User $user) {
      if($this->object->getState() < STATE_VISIBLE) {
        return false;
      } // if
      
      return $this->object->getProject()->users()->isMember($user) || $user->isProjectManager();
    } // canSubscribe
    
  }