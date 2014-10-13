<?php

  /**
   * Framework level homescreen tabs manager implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  abstract class FwHomescreenTabs extends BaseHomescreenTabs {

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Return tabs by user
     *
     * @param User $user
     * @return HomescreenTab[]
     */
    static function findByUser(User $user) {
      return HomescreenTabs::find(array(
        'conditions' => array('user_id = ?', $user->getId()),
      ));
    } // findByUser

    /**
     * Return ID name map for a given user
     *
     * @param User $user
     * @return array
     */
    static function getIdNameMap(User $user) {
      $result = array();

      $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'homescreen_tabs WHERE user_id = ? ORDER BY position', $user->getId());
      if($rows) {
        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach
      } // if

      return count($result) ? $result : null;
    } // getIdNameMap

    /**
     * Return true if tab with the given ID exists, and it is for a given user
     *
     * @param integer $tab_id
     * @param User|integer $user
     * @return bool
     */
    static function tabExists($tab_id, $user) {
      return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'homescreen_tabs WHERE id = ? AND user_id = ?', $tab_id, ($user instanceof User ? $user->getId() : (integer) $user));
    } // tabExists

    // ---------------------------------------------------
    //  OLD API
    // ---------------------------------------------------
    
    /**
     * Return next home screen tab position by home screen
     * 
     * @param User $parent
     * @return integer
     */
    static function getNextPosition(User $parent) {
      return ((integer) DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'homescreen_tabs WHERE user_id = ?', $parent->getId())) + 1;
    } // getNextPosition

    /**
     * Remove all home screen tab types when module is uninstalled
     *
     * @param AngieModule $module
     * @return DbResult
     */
    static function deleteByModule(AngieModule $module) {
      $tab_types = array();

      $d = dir($module->getPath() . '/models/homescreen_tabs');
      if($d) {
        while(($entry = $d->read()) !== false) {
          $class_name = str_ends_with($entry, '.class.php') ? str_replace('.class.php', '', $entry) : null;

          if($class_name) {
            $tab_types[] = $class_name;
          } // if
        } // if

        $d->close();
      } // if

      if (count($tab_types)) {
        return DB::execute("DELETE FROM " . TABLE_PREFIX . "homescreen_tabs WHERE type IN (?)", $tab_types);
      } // if
    } // deleteByModule
    
  }