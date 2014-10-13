<?php

  /**
   * Project object subtask assignees implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectObjectSubtaskAssigneesImplementation extends ISubtaskAssigneesImplementation {

    /**
     * Return array of available users
     *
     * @param User $user
     * @return array
     */
    function getAvailableUsers(User $user) {
      if($this->object->getProject()->isLeader($user) || $user->isPeopleManager() || $user->isProjectManager() || ConfigOptions::getValueFor('clients_can_delegate_to_employees', $this->object->getProject())) {
        return $this->object->getProject()->users()->get($user);
      } else {
        return $this->object->getProject()->users()->getByCompany($user->getCompany(), $user);
      } // if
    } // getAvailableUsers

    /**
     * Return available users for select box
     *
     * @param User $user
     * @param mixed $exclude_ids
     * @return array
     */
    function getAvailableUsersForSelect(User $user, $exclude_ids = null) {
      if($this->object->getProject()->isLeader($user) || $user->isPeopleManager() || $user->isProjectManager() || ConfigOptions::getValueFor('clients_can_delegate_to_employees', $this->object->getProject())) {
        $users = $this->object->getProject()->users()->getForSelect($user, $exclude_ids);
      } else {
        $users = $this->object->getProject()->users()->getByCompanyForSelect($user->getCompany(), $user, $exclude_ids);
      } // if

      // if this subtasks parent object has visibility set on private exclude users who cannot see private
      if ($this->object->getParent() instanceof IVisibility && $this->object->getParent()->getVisibility() == VISIBILITY_PRIVATE) {
        $users = $this->object->getParent()->visibility()->filterUsersWithoutPrivatePermissionForSelect($users);
      } // if

      return $users;
    } // getAvailableUsersForSelect

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return $this->object->getProject() instanceof Project ? '[' . $this->object->getProject()->getName() . '] ' : '';
    } // getNotificationSubjectPrefix

  }