<?php

  /**
   * Project roles manager
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  final class ProjectRoles extends BaseProjectRoles {
    
    /**
     * Return ID name map of project roles
     * 
     * @return array
     */
    static function getIdNameMap() {
      $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'project_roles ORDER BY name');
      
      if($rows) {
        $result = array();
        
        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach
        
        return $result;
      } else {
        return null;
      } // if
    } // getIdNameMap
    
    /**
     * Return ID of default project role
     * 
     * @return integer
     */
    static function getDefaultId() {
      return DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'project_roles WHERE is_default = ?', true);
    } // getDefaultId
    
    /**
     * Return default project role
     * 
     * @return ProjectRole
     */
    static function getDefault() {
      return ProjectRoles::find(array(
        'order' => 'is_default DESC', 
        'one' => true, 
      ));
    } // getDefault
    
    /**
     * Set default project role
     * 
     * Role can be NULL, in case you want Custom to be selected by default
     *
     * @param ProjectRole $role
     * @return ProjectRole
     */
    static function setDefault(ProjectRole $role) {
      DB::transact(function() use ($role) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'project_roles SET is_default = ?', false);
        DB::execute('UPDATE ' . TABLE_PREFIX . 'project_roles SET is_default = ? WHERE id = ?', true, $role->getId());
      }, 'Setting project role as default');
    } // setDefault
    
    /**
     * Cached array of project permissions
     *
     * @var array
     */
    static private $permissions = false;
    
    /**
     * Return project permissions
     * 
     * @return array
     */
    static function getPermissions() {
      if(self::$permissions === false) {
        EventsManager::trigger('on_project_permissions', array(&self::$permissions));
      } // if
      
      return self::$permissions;
    } // function_name
    
    /**
  	 * Return project roles slice based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return ProjectRoles::find(array(
  			  'conditions' => array("id NOT IN (?)", $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return ProjectRoles::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
    
  }