<?php

  /**
   * SourceRepositories class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceRepositories extends BaseSourceRepositories {
  
    /**
     * Find all repositories that match specific update type
     *
     * @param int $update_type
     * @return array
     */
    static function findByUpdateType($update_type) {
      return BaseSourceRepositories::find(array(
        'conditions' => array('update_type = ?', $update_type), 
      ));
    } // find repositories by update type
    
    /**
     * Return ID name map of available repositories
     * 
     * @return array
     */
    static function getIdNameMap() {
      $result = array();
      
      $rows = db::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'source_repositories ORDER BY name');
      if($rows) {
        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach
      } // if
      
      return $result;
    } // getIdNameMap
    
    /**
     * Return ID name map of repositories that given user can see
     * 
     * @param IUser $user
     * @return array
     */
    static function getIdNameMapByUser(IUser $user) {
      $result = array();
      
      if($user->isProjectManager()) {
        return SourceRepositories::getIdNameMap();
      } else {
        $project_ids = Projects::findIdsByUser($user, true);
        
        if($project_ids) {
          $source_repositories_table = TABLE_PREFIX . 'source_repositories';
          $project_objects_table = TABLE_PREFIX . 'project_objects';
          
          $rows = DB::execute("SELECT $source_repositories_table.id, $source_repositories_table.name FROM $source_repositories_table, $project_objects_table WHERE $source_repositories_table.id = $project_objects_table.integer_field_1 AND $project_objects_table.type = ? AND $project_objects_table.project_id IN (?)", 'ProjectSourceRepository', $project_ids);
          if($rows) {
            foreach($rows as $row) {
              $result[(integer) $row['id']] = $row['name'];
            } // foreach
          } // if
        } // if
      } // if
      
      return $result;
    } // getIdNameMapByUser
    
    /**
  	 * Return slice of repositories
  	 * 
  	 * @param integer $num
  	 * @return DBResult
  	 */
  	static function getSlice($num = 100, $exclude = null, $timestamp = null) {
      if ($exclude) {
        return SourceRepositories::find(array(
          'conditions' => array('id NOT IN (?) AND created_on < ?', $exclude, date(DATETIME_MYSQL, $timestamp)),
          'order' => 'type, name',
          'limit' => $num,
        ));
      } else {
  	  return SourceRepositories::find(array(
        'order' => 'type, name',
  			'limit' => $num,  
  	  ));
      } //if
  	} // getSlice
    
  }