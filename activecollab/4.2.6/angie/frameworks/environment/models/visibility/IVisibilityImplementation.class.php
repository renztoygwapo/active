<?php

  /**
   * Visibility helper
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class IVisibilityImplementation {
    
    /**
     * Parent object instance
     *
     * @var IVisibility
     */
    private $object;
    
    /**
     * Construct visibility implementation helper
     *
     * @param IVisibility $object
     */
    function __construct(IVisibility $object) {
      $this->object = $object;
    } // __construct

    /**
     * Returns true if parent object has set visibility to private
     *
     * @return boolean
     */
    function isPrivate() {
      return $this->object->getVisibility() === VISIBILITY_PRIVATE;
    } // isPrivate

    /**
     * Return verbose information on who can see this object
     * 
     * @return string
     */
    function getStatement($full = false) {
      if($this->isPrivate()) {
        if($full) {
          $class_names = Users::userClassesThatCanSeePrivate();

          if($class_names) {
            return lang('This :type is private. It is visible only to the following user types: :user_types', array(
              'type' => $this->object->getVerboseType(),
              'user_types' => implode(', ', $class_names),
            ));
          } else {
            return lang('Private');
          } // if
        } else {
          return lang('Private');
        } // if
      } else {
        return lang('Visible to everyone');
      } // if
    } // getStatement
    
    /**
     * Describe state of the parent object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['visibility'] = $this->object->getVisibility();
      $result['permissions']['can_change_visibility'] = $this->canChange($user);
    } // describe

    /**
     * Describe state of the parent object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['visibility'] = $this->object->getVisibility();

      if($detailed) {
        $result['permissions']['can_change_visibility'] = $this->canChange($user);
      } // if
    } // describeForApi
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can change visibility of parent object
     *
     * @param IUser $user
     * @return boolean
     */
    function canChange(IUser $user) {
      return $this->object->canEdit($user);
    } // canChange

    // ---------------------------------------------------
    //  Miscellaneous
    // ---------------------------------------------------

    /**
     * Check if parent object has subscribed users who cannot see private objects
     *
     * @return boolean
     */
    function hasSubscribersWithoutPrivatePermission() {
      if ($this->object instanceof ISubscriptions) {
        $subscribers = $this->object->subscriptions()->get();
        if (is_foreachable($subscribers)) {
          foreach ($subscribers as $subscriber) {
            /**
             * @param User $subscriber
             */
            if (!($subscriber instanceof User) || !$subscriber->canSeePrivate()) {
              // we found subscriber who cannot see private so we can break the loop and return true
              return true;
            } //if
          } // foreach
        } //if
      } //if
      return false;
    } // hasSubscribedUsersWithoutPrivatePermission

    /**
     * Check if parent object has assignees users who cannot see private objects
     *
     * @return boolean
     */
    function hasAssigneesWithoutPrivatePermission() {
      if ($this->object instanceof IAssignees) {
        // check if user is responsible

        if (($user = $this->object->assignees()->getAssignee()) instanceof User) {
          if (!$user->canSeePrivate()) {
            // we found assignee who cannot see private so we can break the loop and return true
            return true;
          } //if
        } //if

        // check if user in assignee table
        $assignees = $this->object->assignees()->getAllAssignees();
        if (is_foreachable($assignees)) {
          foreach ($assignees as $assignee) {
            /**
             * @param User $assignee
             */
            if (!$assignee->canSeePrivate()) {
              // we found assignee who cannot see private so we can break the loop and return true
              return true;
            } //if
          } // foreach
        } //if
      } //if
      return false;
    } // hasAssigneesWithoutPrivatePermission

    /**
     * Unsubscribes all users without private object permission from this object if it has private visibility
     */
    function updateUsersWithoutPrivatePermissions() {
      if ($this->object->getVisibility() == VISIBILITY_PRIVATE) {

        // update subscribers
        if ($this->object instanceof ISubscriptions) {
          $subscribers = $this->object->subscriptions()->get();
          if (is_foreachable($subscribers)) {
            foreach ($subscribers as $subscriber) {
              /**
               * @param User $subscriber
               */
              if ($subscriber instanceof AnonymousUser || !$subscriber->canSeePrivate()) {
                $this->object->subscriptions()->unsubscribe($subscriber);
              } //if
            } // foreach
          } //if
        } //if

        // Update subtasks assignees
        if ($this->object instanceof ISubtasks) {
          $subtasks = Subtasks::findByParent($this->object);

          if($subtasks) {
            foreach($subtasks as $subtask) {
              if (($user = Users::findById($subtask->getAssigneeId())) instanceof User && !$user->canSeePrivate()) {
                $subtask->setAssigneeId(0);
                $subtask->save();
              } // if
            } // foreach
          } // if
        } //if
      } //if
    } // updateUsersWithoutPrivatePermissions

    /**
     * Takes array of users and returns the filtered array without the users without can_see_private_objects permission
     *
     * @param User[]|DBresult $users
     *
     * @return User[]|null $users
     */
    function filterUsersWithoutPrivatePermission($users) {
      if (is_foreachable($users)) {

        // Since DBResult is read-only we have to change it to array
        if ($users instanceof DBResult) {
          $users = $users->toArray();
        } //if

        foreach ($users as $key => $user) {
          if (!$user->canSeePrivate()) {
            unset($users[$key]);
          } //if
        } //foreach
        return array_values($users);
      } else {
        return null;
      } // if
    } // filterUsersWithoutPrivatePermission

    /**
     * Takes array of users grouped for select and returns the filtered array without the users without can_see_private_objects permission
     *
     * @param $grouped_users Array()|null
     *
     * @return Array()|null
     */
    function filterUsersWithoutPrivatePermissionForSelect($grouped_users) {
      if(is_foreachable($grouped_users)) {
        foreach($grouped_users as $group_name => $users) {
          foreach($users as $user_id => $user_display) {
            if (!(Users::findById($user_id)->canSeePrivate())) {
              unset($users[$user_id]);
            } // if
          } // foreach
          if (count($users) > 0) {
            $grouped_users[$group_name] = $users;
          } else {
            unset($grouped_users[$group_name]);
          } // if
        } // foreach
      } // if

      return $grouped_users;
    } // filterUsersWithoutPrivatePermission

  } //IVisibilityImplementation