<?php

  /**
   * Estimates class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class Estimates extends BaseEstimates {
    
    /**
     * Find latest estimate by parent
     * 
     * @param ITracking $parent
     */
    static function findByParent(ITracking $parent) {
      return Estimates::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), 
        'order' => 'created_on DESC', 
      ));
    } // findByParent
  
    /**
     * Find latest estimate by parent
     * 
     * @param ITracking $parent
     */
    static function findLatestByParent(ITracking $parent) {
      return Estimates::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), 
        'order' => 'created_on DESC', 
        'limit' => 1, 
        'one' => true, 
      ));
    } // findLatestByParent
    
    /**
     * Return previous estimates by parent
     * 
     * @param ITracking $parent
     * @return DBResult
     */
    static function findPreviousByParent(ITracking $parent) {
      $count = self::countByParent($parent);
      
      if($count > 1) {
        return Estimates::find(array(
          'conditions' => array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), 
          'order' => 'created_on DESC', 
          'offset' => 1, 
          'limit' => $count - 1, 
        ));
      } else {
        return null;
      } // if
    } // findPreviousByParent
    
    /**
     * Count estimates by parent
     * 
     * @param ITracking $parent
     * @return integer
     */
    static function countByParent(ITracking $parent) {
      return DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'estimates WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());
    } // countByParent
    
    /**
     * Return number of estimates that use this particular job type
     * 
     * @param JobType $job_type
     * @return integer
     */
    static function countByJobType(JobType $job_type) {
      return Estimates::count(array('job_type_id = ?', $job_type->getId()));
    } // countByJobType
    
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
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'estimates WHERE parent_type = ? AND parent_id IN (?)', $parent_type, $parent_ids);
        } // foreach
      } // if
    } // deleteByParents
    
    /**
     * Remove estimates by parent type
     * 
     * @param array $types
     */
    static function deleteByParentTypes($types) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'estimates WHERE parent_type IN (?)', $types);
    } // deleteByParentTypes
  
  }