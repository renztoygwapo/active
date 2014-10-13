<?php

  /**
   * Framework level assignment management class
   *
   * @package angie.frameworks.assignees
   * @subpackage models
   */
  class FwAssignments {

    /**
     * Returns true if $user is assigned to $object
     *
     * @param IUser $user
     * @param IAssignees $parent
     * @return boolean
     */
    static function isAssignee(IUser $user, IAssignees $parent) {
      if($user instanceof User && $user->getState() >= STATE_ARCHIVED) {
        return Assignments::isResponsible($user, $parent) || ($parent->assignees()->getSupportsMultipleAssignees() && DB::executeFirstCell('SELECT COUNT(user_id) FROM ' . TABLE_PREFIX . 'assignments WHERE user_id = ? AND parent_type = ? AND parent_id = ?', $user->getId(), get_class($parent), $parent->getId()));
      } else {
        return false;
      } //if
    } // isAssignee

    /**
     * Returns true if $user is assigned to $parent and set as main assignee
     *
     * @param IUser $user
     * @param IAssignees $parent
     * @return boolean
     */
    static function isResponsible(IUser $user, IAssignees $parent) {
      if($user instanceof User && $user->getState() >= STATE_ARCHIVED) {
        return $parent->getAssigneeId() && $parent->getAssigneeId() == $user->getId();
      } else {
        return false;
      } //if
    } // isResponsible
    
    /**
     * Return number of users assigned to a given object
     *
     * @param ProjectObject $object
     * @return integer
     */
    static function countAssigneesByObject($object) {
      return (integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id = ?', get_class($object), $object->getId());
    } // countAssigneesByObject
    
    /**
     * Delete assignments by project object
     *
     * @param IAssignees $parent
     * @return boolean
     */
    static function deleteByParent(IAssignees $parent) {
      return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());
    } // deleteByParent
    
    /**
     * Delete assignments by User
     *
     * @param User $user
     * @return boolean
     */
    static function deleteByUser(User $user) {
      return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE user_id = ?', $user->getId());
    } // deleteByUser
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     */
    static function deleteByParents($parents) {
      if(is_foreachable($parents)) {
        foreach($parents as $parent_type => $parent_ids) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id IN (?)', $parent_type, $parent_ids);
        } // foreach
      } // if
    } // deleteByParents

    /**
     * Delete by parent types
     *
     * @param $types
     * @return DbResult
     */
    static function deleteByParentTypes($types) {
      return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type IN (?)', $types);
    } // deleteByParentTypes
    
  }