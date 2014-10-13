<?php

  /**
   * Framework level subscription manager implementation
   *
   * @package angie.frameworks.subscriptions
   * @subpackage models
   */
  abstract class FwSubscriptions extends BaseSubscriptions {
    
    /**
     * Delete subscriptions by parent
     *
     * @param ISubscriptions $parent
     * @return boolean
     */
    static function deleteByParent(ISubscriptions $parent) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());

      AngieApplication::cache()->removeByObject($parent);
      AngieApplication::cache()->removeByModel('users');
    } // deleteByParent
    
    /**
     * Delete subscriptions by user
     *
     * @param User $user
     */
    static function deleteByUser(User $user) {
      if($user instanceof User) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE user_id = ?', $user->getId());
      } elseif($user instanceof AnonymousUser) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE user_id = ? AND user_email = ?', 0, $user->getEmail());
      } // if

      AngieApplication::cache()->removeByModel('users');
    } // deleteByUser
    
    /**
     * Delete subscription record based on id and code
     *
     * @param integer $id
     * @param string $code
     */
    static function deleteByIdAndCode($id, $code) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE id = ? AND code = ?', $id, $code);
      AngieApplication::cache()->removeByModel('users');
    } // deleteByIdAndCode
    
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
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id IN (?)', $parent_type, $parent_ids);
        } // foreach
      } // if
    } // deleteByParents
    
    /**
     * Delete by parent types
     */
    static function deleteByParentTypes($types) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type IN (?)', $types);
    } // deleteByParentTypes
    
  }