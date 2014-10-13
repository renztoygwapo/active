<?php

  /**
   * JobTypes class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class JobTypes extends BaseJobTypes {
    
    /**
  	 * Return types slice based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return JobTypes::find(array(
  			  'conditions' => array("id NOT IN (?)", $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return JobTypes::find(array(
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
     * Return ID => name map
     * 
     * @param mixed $force_ids
     * @param integer $min_activity
     * @param mixed $exclude_ids
     * @return array
     */
    static function getIdNameMap($force_ids = null, $min_activity = JOB_TYPE_ACTIVE, $exclude_ids = null) {
      if(self::$id_name_map === false) {
        if($exclude_ids && !is_array($exclude_ids)) {
          $exclude_ids = array($exclude_ids);
        } // if

        if($force_ids === null) {
          $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'job_types WHERE is_active >= ? ORDER BY name', $min_activity);
        } else {
          if(!is_array($force_ids)) {
            $force_ids = array($force_ids);
          } // if

          $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'job_types WHERE is_active >= ? OR id IN (?) GROUP BY id ORDER BY name', $min_activity, $force_ids);
        } // if

        if($rows) {
          self::$id_name_map = array();
          
          foreach($rows as $row) {
            if($exclude_ids && in_array((integer) $row['id'], $exclude_ids)) {
              continue;
            } // if

            self::$id_name_map[(integer) $row['id']] = trim($row['name']);
          } // foreach
        } else {
          self::$id_name_map = null;
        } // if
      } // if
      
      return self::$id_name_map;
    } // getIdNameMap
    
    /**
     * Return array of hourly rates for given project
     * 
     * @param Project $project
     * @return array
     */
    static function getIdRateMapFor(Project $project) {
      $result = array();
      
      $rows = DB::execute('SELECT id, default_hourly_rate FROM ' . TABLE_PREFIX . 'job_types ORDER BY name');
      if($rows) {
        foreach($rows as $row) {
          $result[(integer) $row['id']] = (float) $row['default_hourly_rate'];
        } // foreach
        
        $rows = DB::execute('SELECT job_type_id, hourly_rate FROM ' . TABLE_PREFIX . 'project_hourly_rates WHERE project_id = ?', $project->getId());
        if($rows) {
          foreach($rows as $row) {
            $result[(integer) $row['job_type_id']] = (float) $row['hourly_rate'];
          } // foreach
        } // if
      } // if
      
      return $result;
    } // getIdRateMapFor

    /**
     * Returns true if job type is in use within the project
     *
     * @param Project $project
     * @param integer $job_type_id
     * @return boolean
     */
    static function getInUseByProject(Project $project, $job_type_id) {
      $estimate_parent_ids = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'estimates WHERE job_type_id = ?', $job_type_id);
      $used_in_estimates = (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'project_objects WHERE id IN (?) AND project_id = ?', $estimate_parent_ids, $project->getId());

      if(!$used_in_estimates) {
        $time_record_parent_ids = DB::execute('SELECT parent_type, parent_id FROM ' . TABLE_PREFIX . 'time_records WHERE job_type_id = ?', $job_type_id);
        if(!($time_record_parent_ids instanceof DBResult)) {
          return false;
        } // if

        foreach($time_record_parent_ids->toArray() as $parent) {
          if($parent['parent_type'] == 'Project' && $parent['parent_id'] == $project->getId()) {
            return true;
          } // if

          $time_record_project_object_ids[] = $parent['parent_id'];
        } // foreach

        return (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'project_objects WHERE id IN (?) AND project_id = ?', $time_record_project_object_ids, $project->getId());
      } else {
        return true;
      } // if
    } // getInUseByProject

    /**
     * Find all job types and prepare them for objects list
     *
     * @param Project $project
     * @return array
     */
    static function findForObjectsList(Project $project) {
      $result = array();

      $job_types = DB::execute('SELECT id, name, default_hourly_rate, is_default, is_active FROM ' . TABLE_PREFIX . 'job_types ORDER BY name');

      if($job_types instanceof DBResult) {
        $job_types->setCasting(array(
          'id' => DBResult::CAST_INT,
          'default_hourly_rate' => DBResult::CAST_INT,
          'is_default' => DBResult::CAST_INT,
          'is_active' => DBResult::CAST_INT
        ));

        $edit_url = Router::assemble('project_hourly_rate', array('project_slug' => $project->getSlug(), 'job_type_id' => '--JOBTYPEID--'));

        foreach($job_types as $job_type) {
          $custom_hourly_rate = DB::executeFirstCell('SELECT hourly_rate FROM ' . TABLE_PREFIX . 'project_hourly_rates WHERE project_id = ? AND job_type_id = ?', $project->getId(), $job_type['id']);

          // Show archived job type if it's not being used in project at all and if doesn't have custom hourly rate
          if(!$job_type['is_active'] && !JobTypes::getInUseByProject($project, $job_type['id']) && is_null($custom_hourly_rate)) {
            continue;
          } // if

          $result[] = array(
            'id'                  => $job_type['id'],
            'name'                => $job_type['name'],
            'default_hourly_rate' => $job_type['default_hourly_rate'],
            'custom_hourly_rate'  => is_null($custom_hourly_rate) ? null : (int) $custom_hourly_rate,
            'is_default'          => $job_type['is_default'],
            'is_active'           => $job_type['is_active'],
            'urls'                => array(
              'edit'                => str_replace('--JOBTYPEID--', $job_type['id'], $edit_url)
            )
          );
        } // foreach
      } // if

      return $result;
    } // findForObjectsList

    /**
     * Return job types available to user
     *
     * @param IUser $user
     * @return JobType[]
     */
    static function findAvailableTo(IUser $user) {
      $results = array();

      if($user instanceof User) {
        if($user->isProjectManager() || !ConfigOptions::hasValueFor('job_type_id', $user)) {
          $results = JobTypes::findByActivity();
        } else {
          $job_type_id = ConfigOptions::getValueFor('job_type_id', $user);
          $job_type = JobTypes::findById($job_type_id);

          $results[] = $job_type;
        } // if
      } // if

      return $results;
    } // findAvailableTo

    /**
     * Return job types by activity
     *
     * @param integer $min_activity
     * @return JobType[]
     */
    static function findByActivity($min_activity = JOB_TYPE_ACTIVE) {
      return JobTypes::find(array(
        'conditions' => array('is_active >= ?', $min_activity),
        'order' => 'name'
      ));
    } // findByActivity
    
    /**
     * Return job type name by job type ID
     * 
     * @param integer $job_type_id
     * @return string
     */
    static function getNameById($job_type_id) {
      return array_var(self::getIdNameMap(), $job_type_id);
    } // getNameById
    
    /**
     * Default job type ID
     *
     * @var integer
     */
    static private $default_job_type_id = false;
    
    /**
     * Return ID of the default job type
     * 
     * @return integer
     */
    static function getDefaultJobTypeId() {
      if(self::$default_job_type_id === false) {
        self::$default_job_type_id = (integer) DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'job_types ORDER BY is_default DESC LIMIT 0, 1');
      } // if
      
      return self::$default_job_type_id;
    } // getDefaultJobTypeId
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can define a new job type
     * 
     * @param IUser $user
     * @return boolean
     */
    static function canAdd(IUser $user) {
      return $user->isAdministrator();
    } // canAdd

    /**
     * Returns true if $user can manage job types from administration
     *
     * @param IUser $user
     * @return boolean
     */
    static function canManage(IUser $user) {
      return $user->isAdministrator();
    } // canManage
    
    /**
     * Returns true if $user can manage hourly rates for $project
     * 
     * @param IUser $user
     * @param Project $project
     * @return boolean
     */
    static function canManageProjectHourlyRates(IUser $user, Project $project) {
      return $user instanceof User && $project->canEdit($user) && $user->canSeeProjectBudgets();
    } // canManageProjectHourlyRates
  
  }