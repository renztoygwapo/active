<?php


  /**
   * TaskSegments class
   *
   * @package ActiveCollab.modules.tasks
   * @subpackage models
   */
  class TaskSegments extends BaseTaskSegments {

    /**
     * Returns true if $user can define a new expense category
     *
     * @param IUser $user
     * @return boolean
     */
    static function canAdd(IUser $user) {
      return $user instanceof User && $user->isProjectManager();
    } // canAdd

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Return types slice based on given criteria
     *
     * @param integer $num
     * @param array $exclude
     * @param integer $timestamp
     * @return DBResult
     */
    static function getSlice($num = 10, $exclude = null, $timestamp = null) {
      if($exclude) {
        return TaskSegments::find(array(
          'conditions' => array("id NOT IN (?)", $exclude),
          'order' => 'name',
          'limit' => $num,
        ));
      } else {
        return TaskSegments::find(array(
          'order' => 'name',
          'limit' => $num,
        ));
      } // if
    } // getSlice

    /**
     * Cached ID name map
     *
     * @var array
     */
    static private $id_name_map = false;

    /**
     * Return map of defined task segments indexed by ID
     *
     * @return array
     */
    static function getIdNameMap() {
      if(self::$id_name_map === false) {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'task_segments ORDER BY name');

        if($rows) {
          self::$id_name_map = array();

          foreach($rows as $row) {
            self::$id_name_map[(integer) $row['id']] = trim($row['name']);
          } // foreach
        } else {
          self::$id_name_map = null;
        } // if
      } // if

      return self::$id_name_map;
    } // getIdNameMap
  
  }

