<?php

  /**
   * Framework level modification log management implementation
   *
   * @package angie.frameworks.history
   * @subpackage models
   */
  abstract class FwModificationLogs extends BaseModificationLogs {
    
    /**
     * Return log entires by parent
     *
     * @param IHistory $parent
     * @return DBResult
     */
    static public function findByParent(IHistory $parent) {
      return ModificationLogs::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), 
        'order' => 'created_on DESC', 
      ));
    } // findByParent
    
    /**
     * Remove by parent
     * 
     * @param IHistory $parent
     */
    static public function deleteByParent(IHistory $parent) {
      $logs_table = TABLE_PREFIX . 'modification_logs';
      $values_table = TABLE_PREFIX . 'modification_log_values';
      
      $log_ids = DB::executeFirstColumn("SELECT id FROM $logs_table WHERE parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId());
      if($log_ids) {
        try {
          DB::beginWork('Removing modification log entries @ ' . __CLASS__);
          
          DB::execute("DELETE FROM $logs_table WHERE id IN (?)", $log_ids);
          DB::execute("DELETE FROM $values_table WHERE modification_id IN (?)", $log_ids);
          
          DB::commit('Modification log entries removed @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to remove modification log entries @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByParent
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     */
    static function deleteByParents($parents) {
      $logs_table = TABLE_PREFIX . 'modification_logs';
      $values_table = TABLE_PREFIX . 'modification_log_values';
      
      if(is_foreachable($parents)) {
        $log_ids = array();
        
        foreach($parents as $parent_type => $parent_ids) {
          $parent_log_ids = DB::executeFirstColumn("SELECT id FROM $logs_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
          
          if($parent_log_ids) {
            $log_ids = array_merge($log_ids, $parent_log_ids);
          } // if
        } // foreach
        
        if(count($log_ids) > 0) {
          try {
            DB::beginWork('Removing modification log entries @ ' . __CLASS__);
            
            DB::execute("DELETE FROM $logs_table WHERE id IN (?)", $log_ids);
            DB::execute("DELETE FROM $values_table WHERE modification_id IN (?)", $log_ids);
            
            DB::commit('Modification log entries removed @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Failed to remove modification log entries @ ' . __CLASS__);
            throw $e;
          } // try
        } // if
      } // if
    } // deleteByParents
    
  }