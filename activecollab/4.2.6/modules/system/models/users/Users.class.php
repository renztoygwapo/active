<?php

  /**
   * Users manager class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Users extends FwUsers {
    
    /**
     * Returns true if $user can create a new user account
     *
     * @param IUser $user
     * @param Company $context
     * @return boolean
     */
    static function canAdd(IUser $user, $context = null) {
      if($user instanceof User && $context instanceof Company) {
        return ($context->getState() > STATE_ARCHIVED) && $user->isPeopleManager();
      } else {
        return false;
      } // if
    } // canAdd

    // ---------------------------------------------------
    //  Roles, Permissions, Instances
    // ---------------------------------------------------

    /**
     * Return available user classes
     *
     * @return array
     */
    static function getAvailableUserClasses() {
      return array('Administrator', 'Manager', 'Member', 'Subcontractor', 'Client');
    } // getAvailableUserClasses

    /**
     * Return array of available user instances
     *
     * @return User[]
     */
    static function getAvailableUserInstances() {
      return array(new Administrator(), new Manager(), new Member(), new Subcontractor(), new Client());
    } // getAvailableUserInstances

    /**
     * Return default user class
     *
     * @return string
     */
    static function getDefaultUserClass() {
      return 'Client';
    } // getDefaultUserClass

    /**
     * Return list of user roles that can see private objects
     *
     * @return array
     */
    static function userClassesThatCanSeePrivate() {
      $result = parent::userClassesThatCanSeePrivate();

      $result[] = 'Subcontractor';
      $result[] = 'Manager';

      return $result;
    } // userClassesThatCanSeePrivate

    /**
     * Find users by type and permissions
     *
     * @param string $permission
     * @param integer $company_id
     * @return array
     */
    static function findClientsByPermissions($permission, $company_id) {
      $all_clients = Users::findByType('Client', array(
        'company_id = ?', $company_id
      ));

      if($all_clients) {
        $clients = array();

        foreach($all_clients as $client) {
          if($client->getSystemPermission($permission)) {
            $clients[] = $client;
          } //if
        } //foreach

        return $clients;
      } else {
        return null;
      } // if
    } // findUserByTypeAndPermissions

    /**
     * Describe role based on user instance
     *
     * Purpose of this function is to provide activeCollab 3 compatible API response
     *
     * @param User $user
     * @return array
     */
    static function describeUserRoleForApi(User $user) {
      $user_class = get_class($user);

      $result = array(
        'id' => 0,
        'class' => $user_class,
        'is_default' => $user_class == Users::getDefaultUserClass(),
        'is_administrator' => $user->isAdministrator(),
      );

      $result['is_project_manager'] = $user->isProjectManager();
      $result['is_people_manager'] = $user->isPeopleManager();

      $result['role_permissions'] = array(
        'has_system_access' => $user->getState() >= STATE_VISIBLE,
        'has_admin_access' => $user->isAdministrator(),
        'can_use_api' => $user->isApiUser(),
        'can_use_feeds' => $user->isApiUser(),
        'can_see_private_objects' => $user->canSeePrivate(),
        'can_manage_trash' => $user->canManageTrash(),
        'can_manage_projects' => $user->isProjectManager(),
        'can_manage_project_requests' => ProjectRequests::canManage($user),
        'can_add_project' => Projects::canAdd($user),
        'can_see_project_budgets' => $user->canSeeProjectBudgets(),
        'can_manage_people' => $user->isPeopleManager(),
        'can_manage_company_details' => false,
        'can_see_contact_details' => $user->canSeeContactDetails(),
        'can_see_company_notes' => Companies::canSeeNotes($user),
        'can_have_homescreen' => $user->isMember(),
        'can_manage_assignment_filters' => DataFilters::canManage('AssignmentFilter', $user),
      );

      if(AngieApplication::isModuleLoaded('documents')) {
        $result['role_permissions']['can_use_documents'] = Documents::canUse($user);
        $result['role_permissions']['can_add_documents'] = Documents::canManage($user);
        $result['role_permissions']['can_manage_documents'] = Documents::canManage($user);
      } // if

      if(AngieApplication::isModuleLoaded('invoicing')) {
        $result['role_permissions']['can_manage_finances'] = $user->isFinancialManager();
        $result['role_permissions']['can_manage_quotes'] = Quotes::canManage($user);
      } // if

      if(AngieApplication::isModuleLoaded('status')) {
        $result['role_permissions']['can_use_status_updates'] = StatusUpdates::canUse($user);
      } // if

      return $result;
    } // describeUserRoleForApi

    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------

    /**
     * Create administrator account
     *
     * @param string $email
     * @param string $password
     * @param array $additional
     * @return Administrator
     */
    static function addAdministrator($email, $password, $additional = null) {
      if(empty($additional)) {
        $additional = array();
      } // if

      $additional['company_id'] = Companies::findOwnerCompany()->getId();
      $additional['auto_assign'] = false;

      return parent::addAdministrator($email, $password, $additional);
    } // addAdministrator

    /**
     * Return ID Details map
     *
     * @param array $ids
     * @param array $fields
     * @param int $min_state
     * @param bool $permalink
     * @param bool|integer $avatar
     * @return array
     */
    static function getIdDetailsMap($ids = null, $fields, $min_state = STATE_ARCHIVED, $permalink = false, $avatar = false) {
      $fields = (array) $fields;

      if($permalink && !in_array('company_id', $fields)) {
        $fields[] = 'company_id';
      } // if

      return parent::getIdDetailsMap($ids, $fields, $min_state, $permalink, $avatar);
    } // getIdDetailsMap

    /**
     * Cached permalink pattern
     *
     * @var bool
     */
    static private $permalink_pattern = false;

    /**
     * Return permalink from user row
     *
     * @param array $row
     * @return string
     */
    static protected function getPermalinkFromUserRow($row) {
      if(self::$permalink_pattern === false) {
        self::$permalink_pattern = Router::assemble('people_company_user', array('company_id' => '--COMPANY-ID--', 'user_id' => '--USER-ID--'));
      } // if

      return str_replace(array('--COMPANY-ID--', '--USER-ID--'), array($row['company_id'], $row['id']), self::$permalink_pattern);
    } // permalinkFromUserRow

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Return users for object lists
     *
     * @param User $user
     * @param int $min_state
     * @return array
     */
    static function findForObjectsList(User $user, $min_state = STATE_ARCHIVED) {
      $users = Users::find(array(
        'conditions' => array('id IN (?) AND company_id IN (?) AND state >= ?', $user->visibleUserIds(), $user->visibleCompanyIds(), $min_state),
        'order' => 'CONCAT(first_name, last_name, email)'
      ));

      $users_map = array();
      if ($users) {
        foreach ($users as $v) {
          $users_map[] = array(
            'id' => $v->getId(),
            'is_archived' => $v->getState() == STATE_ARCHIVED ? 1 : 0,
            'name' => $v->getName(),
            'avatar' => array(
              'small' => $v->avatar()->getUrl(16),
              'photo' => $v->avatar()->getUrl(IUserAvatarImplementation::SIZE_PHOTO),
            ),
            'display_name' => $v->getDisplayName(),
            'email' => $v->getEmail(),
            'company_id' => $v->getCompanyId(),
            'permalink' => $v->getViewUrl(),
          );
        } // foreach
      } // if
      
      return $users_map;
    } //findForObjectsList
    
    /**
     * Return users by company id
     * 
     * If $ids is set, result will be limited to these users only
     *
     * @param integer $company_id
     * @param int $min_state
     * @return User[]
     */
    static function findByCompanyId($company_id, $min_state = STATE_VISIBLE) {
     
      $conditions = array('company_id = ? AND state >= ?', $company_id, $min_state);
      
      return Users::find(array(
        'conditions' => $conditions,
        'order' => 'CONCAT(first_name, last_name, email)',
      ));
    } // findByCompany
    

    /**
      * return managed_by_id(users) recursively
      * @param array $user_ids
      * @return $result[]
    **/
    static function getUsersManagedById($user_ids) {
      $arrUserManagedBy = array();

      if(is_foreachable($user_ids)) {
        foreach ($user_ids as $uid) {
          $user = Users::findById($uid);
          $userMngById = $user->getManagedById();
          
          if(!empty($userMngById)) {
            $arrUserManagedBy[] = $userMngById;
            $flag = true;
            
            while($flag) {
              $user = Users::findById($userMngById);
              $tmpUserMngById = $user->getManagedById();
              
              if(!empty($tmpUserMngById)) {
                $arrUserManagedBy[] = $tmpUserMngById;
                $userMngById = $tmpUserMngById;
              } else {
                $flag = false;
              }
            } //while
          } // if
        } //foreach
      }
      return array_unique($arrUserManagedBy);
    }//getUsersManagedById
    

    /**
      * return people id, email by user type
      * @param string $type
      * @param array $userType
      * @return $result[]
    **/
    static function getUsersByType($userType) {
     
      $rows = DB::execute('SELECT id, email, first_name, last_name FROM ' . TABLE_PREFIX . 'users WHERE type IN (?)', $userType);

      if(is_foreachable($rows)) {
        $result = array();
        foreach($rows as $row) {
          $result[] = $row;
        } // foreach
        return $result;
      } // if
      return null;
    } // getUsersByType

    /**
     * Return users by company ID-s
     *
     * @param array $company_ids
     * @param int $min_state
     * @return User[]
     */
    static function findByCompanyIds($company_ids, $min_state = STATE_VISIBLE) {
      $conditions = DB::prepare('company_id IN (?) AND state >= ?', $company_ids, $min_state);

      return Users::find(array(
        'conditions' => $conditions
      ));
    } // findByCompanyIds
    
    /**
     * Return users by company
     * 
     * If $ids is set, result will be limited to these users only
     *
     * @param Company $company
     * @param array $ids
     * @param integer $min_state
     * @return User[]
     */
    static function findByCompany(Company $company, $ids = array(), $min_state = STATE_VISIBLE) {
      if (is_foreachable($ids)) {
        $conditions = array('company_id = ? AND id IN (?) AND state >= ?', $company->getId(), $ids, $min_state);
      } else {
        $conditions = array('company_id = ? AND state >= ?', $company->getId(), $min_state);
      } // if
      
      return Users::find(array(
        'conditions' => $conditions,
        'order' => 'CONCAT(first_name, last_name, email)',
      ));
    } // findByCompany
    
    /**
     * Return archived users by company
     *
     * @param Company $company
     * @param integer $state
     * @return User[]
     */
    static function findArchivedByCompany(Company $company, $state = STATE_ARCHIVED) {
      return Users::find(array(
        'conditions' => array('company_id = ? AND state = ?', $company->getId(), $state),
        'order' => 'updated_on DESC'
      ));
    } // findArchivedByCompany
    
    /**
     * Return user ID-s by company
     * 
     * If $ids is set, result will be limited to these ID-s only
     *
     * @param Company $company
     * @param array $ids
     * @return array
     */
    static function findUserIdsByCompany(Company $company, $ids = null) {
      if($ids) {
        $rows = DB::execute('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE company_id = ? AND id IN (?) ORDER BY CONCAT(first_name, last_name, email)', $company->getId(), $ids);
      } else {
        $rows = DB::execute('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE company_id = ? ORDER BY CONCAT(first_name, last_name, email)', $company->getId());
      } // if

    	if(is_foreachable($rows)) {
    	  $result = array();
    	  foreach($rows as $row) {
    	    $result[] = (integer) $row['id'];
    	  } // foreach
    	  return $result;
    	} // if
    	return null;
    } // findUserIdsByCompany
    
    /**
     * Use array of users and organize them by category
     *
     * @param User[] $users
     * @return array
     */
    static function groupByCompany($users) {
      $result = array();
      
      if(is_foreachable($users)) {
        $id_name_map = Companies::getIdNameMap(array_unique(objects_array_extract($users, 'getCompanyId')));
        
        $unknown_company_users = array();
        foreach($users as $user) {
          $company_name = $id_name_map && isset($id_name_map[$user->getCompanyId()]) ? $id_name_map[$user->getCompanyId()] : null;
          
          if($company_name) {
            if(!isset($result[$company_name])) {
              $result[$company_name] = array();
            } // if
            
            $result[$company_name][] = $user;
          } else {
            $unknown_company_users[] = $user;
          } // if
        } // foreach
        
        ksort($result);
        
        if(count($unknown_company_users)) {
          $result[lang('Individuals')] = $unknown_company_users;
        } // if
      } // if
      
      return $result;
    } // groupByCompany
    
    /**
     * Import auto-assign people into project
     *
     * @param Project $into
     * @throws Exception
     */
    static function importAutoAssignIntoProject(Project $into) {
      try {
        DB::beginWork('Automatically adding users to the project @ ' . __CLASS__);
        
        $users = Users::find(array(
          'conditions' => array('auto_assign = ? AND state = ?', true, STATE_VISIBLE),
          'order' => 'CONCAT(first_name, last_name, email)',
        ));
       
        if($users) {
          foreach($users as $user) {
            $into->users()->add($user, $user->getAutoAssignRole(), $user->getAutoAssignPermissions());
          } // foreach
        } // if
        
        DB::commit('Users automatically added to the new project @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to automatically add users to the new project @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // importAutoAssignIntoProject
    
    /**
     * Return ID-s of user accounts $user can see
     *
     * @param IUser $user
     * @param Company $company
     * @param integer $min_state
     * @return mixed
     */
    static function findVisibleUserIds(IUser $user, $company = null, $min_state = STATE_VISIBLE) {
      if($user instanceof User) {
        return AngieApplication::cache()->getByObject($user, array('visible_user_ids', ($company instanceof Company ? $company->getId() : 'all'), $min_state), function() use ($user, $company, $min_state) {
          $users_table = TABLE_PREFIX . 'users';
          $project_users_table = TABLE_PREFIX . 'project_users';

          if($user->isPeopleManager() || $user->isFinancialManager()) {
            if($company instanceof Company) {
              $visible_ids = DB::executeFirstColumn("SELECT id FROM $users_table WHERE company_id = ? AND state >= ?", $company->getId(), $min_state);
            } else {
              $visible_ids = DB::executeFirstColumn("SELECT id FROM $users_table WHERE state >= ?", $min_state);
            } // if

          } else {
            if($company instanceof Company) {
              if($user->getCompanyId() == $company->getId()) {
                $visible_ids = DB::executeFirstColumn("SELECT id FROM $users_table WHERE company_id = ? AND id <> ? AND state >= ?", $company->getId(), $user->getId(), $min_state);
              } else {
                $project_ids = DB::executeFirstColumn("SELECT DISTINCT project_id FROM $project_users_table WHERE user_id = ?", $user->getId());

                if($project_ids) {
                  $visible_ids = DB::executeFirstColumn("SELECT $users_table.id FROM $users_table JOIN $project_users_table ON $users_table.id = $project_users_table.user_id WHERE $users_table.company_id = ? AND $users_table.state >= ?", $company->getId(), $min_state);
                } else {
                  $visible_ids = null; // User is not involved in any project, so he can see only members of his own company
                } // if
              } // if
            } else {
              $projects_table = TABLE_PREFIX . 'projects';

              $project_ids = DB::executeFirstColumn("SELECT DISTINCT $projects_table.id FROM $projects_table JOIN $project_users_table ON $projects_table.id = $project_users_table.project_id WHERE $projects_table.state >= ? AND $project_users_table.user_id = ?", STATE_ARCHIVED, $user->getId());

              if($project_ids) {
                $user_ids = DB::executeFirstColumn("(SELECT id FROM $users_table WHERE company_id = ? AND $users_table.state >= ?) UNION (SELECT user_id AS 'id' FROM $project_users_table JOIN $users_table ON $project_users_table.user_id = $users_table.id WHERE $project_users_table.project_id IN (?) AND $users_table.state >= ?)", $user->getCompanyId(), $min_state, $project_ids, $min_state);
                $visible_ids = count($user_ids) ? array_unique($user_ids) : null;
              } else {
                $visible_ids = DB::executeFirstColumn("SELECT id FROM $users_table WHERE company_id = ? AND state >= ?", $user->getCompanyId(), $min_state);
              } // if
            } // if
          } // if

          return $visible_ids;
        });
      } else {
        return null;
      } // if
    } // findVisibleUserIds
    
    /**
     * Return ID-s of companies $user can see
     *
     * @param User $user
     * @return array
     */
    static function findVisibleCompanyIds($user) {
      
      // Admins, People & Financial managers can see everyone
      if($user->isAdministrator() || $user->isPeopleManager() || $user->isFinancialManager()) {
        $rows = DB::execute('SELECT id FROM ' . TABLE_PREFIX . 'companies ORDER BY name');
        
        $result = array();
        if($rows) {
          foreach($rows as $row) {
            $result[] = (integer) $row['id'];
          } // foreach
        } // if
        
        return $result;
      } // if
      
      $visible_user_ids = $user->visibleUserIds();
      
      if(is_foreachable($visible_user_ids)) {
        $users_table = TABLE_PREFIX . 'users';
        $companies_table = TABLE_PREFIX . 'companies';

        $result = array();
        
        $rows = DB::execute("SELECT DISTINCT(company_id) FROM $users_table JOIN $companies_table ON $users_table.company_id = $companies_table.id WHERE $users_table.id IN (?) ORDER BY $companies_table.is_owner DESC, $companies_table.name", $visible_user_ids);
        if($rows) {
          foreach($rows as $row) {
            $result[] = (integer) $row['company_id'];
          } // foreach
        } // if
        
        if(!in_array($user->getCompanyId(), $result)) {
          $result[] = $user->getCompanyId();
        } // if
        
        $projects_table = TABLE_PREFIX . 'projects';
        $project_users_table = TABLE_PREFIX . 'project_users';
        
        $rows = DB::execute("SELECT DISTINCT $projects_table.company_id AS 'company_id' FROM $projects_table JOIN $project_users_table ON $projects_table.id = $project_users_table.project_id WHERE $project_users_table.user_id = ? AND $projects_table.state >= ? AND $projects_table.company_id > 0 AND $projects_table.company_id NOT IN (?)", $user->getId(), STATE_TRASHED, $result);
        if($rows) {
          foreach($rows as $row) {
            $result[] = (integer) $row['company_id'];
          } // foreach
        } // if
        
        return $result;
      } else {
        return array($user->getCompanyId());
      } // if
    } // findVisibleCompanyIds
    
    /**
     * Return user data prepared for select box
     *
     * @param User $user
     * @param array $exclude_ids
     * @param integer $min_state
     * @return array
     */
    static function getForSelect(User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      if($exclude_ids && !is_array($exclude_ids)) {
        $exclude_ids = array($exclude_ids);
      } // if

      // if we have exclusion array, remove those users from array returned by $user->visibleUserIds()
      if (is_foreachable($exclude_ids)) {
        $visible_user_ids = array_diff($user->visibleUserIds(null, $min_state), $exclude_ids);
      } else {
        $visible_user_ids = $user->visibleUserIds(null, $min_state);
      } // if
      
      if(is_foreachable($visible_user_ids)) {
        return Users::getForSelectByConditions(array('id IN (?) AND state >= ?', $visible_user_ids, $min_state));
      } else {
        return null;
      } // if
    } // getForSelect

    /**
     * Return users grouped for select box by conditions
     * 
     * $additional_table is used in case we need to have a join
     *
     * @param mixed $conditions
     * @param string $additional_table
     * @return array
     */
    static function getForSelectByConditions($conditions, $additional_table = null) {
      $user_table = TABLE_PREFIX . 'users';
      
      $tables = $additional_table ? "$user_table, $additional_table" : $user_table;
      $conditions = DB::prepareConditions($conditions);
      
      if($conditions) {
        $users = DB::execute("SELECT $user_table.id, $user_table.first_name, $user_table.last_name, $user_table.email, $user_table.company_id FROM $tables WHERE $conditions ORDER BY CONCAT($user_table.first_name, $user_table.last_name, $user_table.email)");
      } else {
        $users = DB::execute("SELECT $user_table.id, $user_table.first_name, $user_table.last_name, $user_table.email, $user_table.company_id FROM $tables ORDER BY CONCAT($user_table.first_name, $user_table.last_name, $user_table.email)");
      } // if

      if($users) {
        $users->setCasting(array(
          'id' => DBResult::CAST_INT,
          'company_id' => DBResult::CAST_INT,
        ));

        return Users::sortUsersForSelect($users);
      } else {
        return null;
      } // if
    } // getForSelectByConditions

    /**
     * Sort users for select
     *
     * @param User[] $users
     * @return array
     */
    static function sortUsersForSelect($users) {
      if(is_foreachable($users)) {
        $company_ids = array();

        foreach($users as $user) {
          $company_id = $user instanceof User ? $user->getCompanyId() : $user['company_id'];

          if(!in_array($company_id, $company_ids)) {
            $company_ids[] = $company_id;
          } // if
        } // foreach

        $companies_map = Companies::getIdNameMap($company_ids);

        // Get owner company ID
        $owner_company = Companies::findOwnerCompany();
        $owner_company_id = $owner_company instanceof Company ? $owner_company->getId() : null;

        // Prepare result elements
        $owner_company_members = array();
        $other_companies = array();
        $individuals = array();

        foreach($users as $user) {
          $user_id = $user instanceof User ? $user->getId() : $user['id'];
          $display_name = $user instanceof User ? $user->getDisplayName() : Users::getUserDisplayName($user);
          $company_id = $user instanceof User ? $user->getCompanyId() : $user['company_id'];

          $company_name = $companies_map && isset($companies_map[$company_id]) ? $companies_map[$company_id] : null;

          if($company_name) {
            if($company_id == $owner_company_id) {
              $owner_company_members[$user_id] = $display_name;
            } else {
              if(isset($other_companies[$company_name])) {
                $other_companies[$company_name][$user_id] = $display_name;
              } else {
                $other_companies[$company_name] = array(
                  $user_id => $display_name,
                );
              } // if
            } // if
          } else {
            $individuals[$user_id] = $display_name;
          } // if
        } // foreach

        ksort($other_companies);

        // Join elements together (owner company first, than other companies and finally individuals)
        $result = array();

        if(count($owner_company_members)) {
          $result[$owner_company->getName()] = $owner_company_members;
        } // if

        if(count($other_companies)) {
          $result = array_merge($result, $other_companies);
        } // if

        if(count($individuals)) {
          $result[lang('Individuals')] = $individuals;
        } // if

        return $result;
      } else {
        return null;
      } // if
    } // sortUsersForSelect
    
    /**
     * Delete users by company
     *
     * @param Company $company
     * @return boolean
     */
    static function deleteByCompany($company) {
      return Users::delete(array('company_id = ?', $company->getId()));
    } // deleteByCompany
    
    /**
     * Fetch user details from database for provided user id-s
     *
     * @param array $user_ids
     * @return array
     */
    static function findUsersDetails($user_ids) {
      if($user_ids) {
        $users_table = TABLE_PREFIX . 'users';
      
        $users = DB::execute("SELECT $users_table.id, $users_table.first_name, $users_table.email, $users_table.last_name, $users_table.company_id FROM $users_table WHERE $users_table.id IN (?)", $user_ids);
        
        if(is_foreachable($users)) {
          $companies = Companies::getIdNameMap();
        
          // Create a result array and make sure that owner company is the first element in it
          $result = array(
            first($companies) => array()
          );
          
          foreach ($users as $user) {
            if($user['first_name'] && $user['last_name']) {
              $user['display_name'] = $user['first_name'] . ' ' . $user['last_name'];
            } elseif($user['first_name']) {
              $user['display_name'] = $user['first_name'];
            } elseif($user['last_name']) {
              $user['display_name'] = $user['last_name'];
            } else {
              $user['display_name'] = $user['email'];
            } // if
            
            $company_name = $companies && isset($companies[$user['company_id']]) ? $companies[$user['company_id']] : null;
            if($company_name) {
              if(isset($result[$company_name])) {
                $result[$company_name][] = $user;
              } else {
                $result[$company_name] = array($user);
              } // if
            } // if
          } // if
          
          ksort($result);
          
          return $result; 
        } else {
          return false;
        } // if
      } else {
        return false;
      } // if
    } // findUsersDetails

    /**
     * Find users for printing by grouping and filtering criteria
     *
     * @param array $user_ids
     * @param string $group_by
     * @param array $filter_by
     * @return User[]
     */
    static function findForPrint($user_ids, $group_by = null, $filter_by = null) {
      // initial condition
      $conditions = array();
      $conditions[] = DB::prepare('id IN (?)', $user_ids);
     
      if (!in_array($group_by, array('company_id'))) {
      	$group_by = null;
      } // if
                
      // filter by visibility status
      $filter_is_archived = array_var($filter_by, 'is_archived', null);
      if ($filter_is_archived === '0') {
        $conditions[] = DB::prepare('(state = ?)', STATE_VISIBLE);
      } else if ($filter_is_archived === '1') {
      	$conditions[] = DB::prepare('(state = ?)', STATE_ARCHIVED);
      } // if
      
      // do find users
      $users = Users::find(array(
      	'conditions' => implode(' AND ', $conditions),
      	'order' => $group_by ? $group_by : 'id DESC' 
      ));
    	
    	return $users;
    } // findForPrint
    
    // ---------------------------------------------------
	  //  vCard
	  // ---------------------------------------------------
    
    /**
     * Prepare user for import
     *
     * @param array $prepared_contacts
     * @param array $vcard
     * @param User $logged_user
     * @param boolean $company_defined
     * @return array
     */
    static function prepareUser(&$prepared_contacts, $vcard, $logged_user, $company_defined = true) {
    	if(!array_key_exists('EMAIL', $vcard)) { // user email is required
    		return true;
    	} // if

    	if(!array_key_exists('ORG', $vcard) && $company_defined) { // user must belong to company
  			return true;
  		} // if

    	// user data initialization
  		$title = $office_address = $phone_work = $phone_mobile = $im_type = $im_value = $updated_on = $company_name = '';
      
  		$user = parent::prepareUserFromVCard($vcard);

  		$components = array('TITLE', 'ADR', 'TEL', 'X-AIM', 'X-ICQ', 'X-MSN', 'X-YAHOO', 'X-JABBER', 'X-SKYPE', 'X-GOOGLE-TALK');
  		foreach($components as $component) {
  			if(array_key_exists($component, $vcard) && is_foreachable($vcard[$component])) {
  				switch($component) {
  					case "TITLE":
  						if(is_foreachable($vcard[$component][0]['value'])) {
  							$value = trim($vcard[$component][0]['value'][0][0]);

    						if($value != '') {
  								$title = $value;
    						} // if
  						} // if
  						break;
            case "ADR":
              if(is_foreachable($vcard[$component])) {
                $value = '';
                foreach($vcard[$component] as $address) {
                  if(is_foreachable($address['param']) && strtoupper($address['param']['TYPE'][0]) == 'WORK' && is_foreachable($address['value'])) {
                    foreach($address['value'] as $part_of_address) {
                      if(trim($part_of_address[0] != '')) {
                        $value .= trim($part_of_address[0]) . "\n";
                      } // if
                    } // foreach

                    if($value != '') {
                      $office_address = $value;
                    } // if
                  } // if
                } // foreach
              } // if
              break;
  					case "TEL":
  						$telephone_types = array('WORK' => 'phone_work', 'CELL' => 'phone_mobile');
  						
  						foreach($telephone_types as $vcard_type => $ac_type) {
  							$value = '';
  							foreach($vcard[$component] as $telephone) {
  								if(is_foreachable($telephone['param']) && strtoupper($telephone['param']['TYPE'][0]) == $vcard_type) {
  									$value .= trim($telephone['value'][0][0]);
  									
  									if($value != '') {
  										$vcard_type == 'WORK' ? $phone_work = $value : $phone_mobile = $value;
  									} // if
  								} // if
  							} // foreach
  						} // foreach
  						break;
  					case "X-AIM":
  						User::prepareIM('AIM', $vcard[$component], $im_type, $im_value);
  						break;
  					case "X-ICQ":
  						User::prepareIM('ICQ', $vcard[$component], $im_type, $im_value);
  						break;
  					case "X-MSN":
  						User::prepareIM('MSN', $vcard[$component], $im_type, $im_value);
  						break;
  					case "X-YAHOO":
  						User::prepareIM('Yahoo!', $vcard[$component], $im_type, $im_value);
  						break;
  					case "X-JABBER":
  						User::prepareIM('Jabber', $vcard[$component], $im_type, $im_value);
  						break;
  					case "X-SKYPE":
  						User::prepareIM('Skype', $vcard[$component], $im_type, $im_value);
  						break;
  					case "X-GOOGLE-TALK":
  						User::prepareIM('Google', $vcard[$component], $im_type, $im_value);
  						break;
  				} // switch
  			} // if
  		} // foreach
  		
  		$company_name = trim($vcard['ORG'][0]['value'][0][0]);
  		
  		$config_option_values = array(
  			'title' 				=> $title,
  			'phone_work' 		=> $phone_work,
  			'phone_mobile' 	=> $phone_mobile,
  			'im_type' 			=> $im_type,
  			'im_value' 			=> $im_value
  		);

  		// check whether user already exists
			$users_table = TABLE_PREFIX . 'users';
    	$rows = DB::execute("SELECT email FROM $users_table WHERE state >= ?", STATE_TRASHED);

    	$ac_users = array();
    	if(is_foreachable($rows)) {
	    	foreach($rows as $row) {
	    		$ac_users[] = strtolower($row['email']);
	    	} // foreach
    	} // if

    	in_array(strtolower($user['email']), $ac_users) ? $is_new = false : $is_new = true;

    	// if it's an existing user populate related info from the DB
    	$user_instance = Users::findByEmail($user['email'], true);
    	if($user_instance instanceof User && !$is_new) {
    		$company_instance = $user_instance->getCompany();

    		if(!$logged_user->isPeopleManager()) { // check whether user has enough permissions to update user
	    		return true;
	    	} // if

    		if(is_foreachable($config_option_values)) {
    			foreach($config_option_values as $k => &$v) {
    				if($v == '') {
    					$v = ConfigOptions::getValueFor($k, $user_instance);
    				}// if
    			} // foreach
    			
          unset($v);
    		} // if
    	} // if

    	// if it's a new user check whether user has enough permissions to create user
    	if(!($user_instance instanceof User) && $is_new) {
    		$company_instance = Companies::findByName($company_name);
    		if($company_instance instanceof Company && !$logged_user->isPeopleManager()) {
	    		return true;
	    	} // if

	    	if(!($company_instance instanceof Company) && !$logged_user->isPeopleManager()) {
	    		return true;
	    	} // if
    	} // if

  		$user_additional_info = array(
  			'title' 				  => $config_option_values['title'],
  			'phone_work' 		  => $config_option_values['phone_work'],
  			'phone_mobile' 	  => $config_option_values['phone_mobile'],
  			'im_type' 			  => $config_option_values['im_type'],
  			'im_value' 			  => $config_option_values['im_value'],
  			'is_new'				  => $is_new,
  			'company_name'	  => $is_new ? $company_name : $company_instance->getName(),
        'company_defined' => $company_defined
  		);
  		
  		$user = array_merge($user, $user_additional_info);

  		// check whether company exists in aC or in prepared array
			$companies_table = TABLE_PREFIX . 'companies';
  		$rows = DB::execute("SELECT name FROM $companies_table WHERE state >= ?", STATE_TRASHED);

    	$ac_companies = array();
    	if(is_foreachable($rows)) {
	    	foreach($rows as $row) {
	    		$ac_companies[] = strtolower_utf(trim($row['name']));
	    	} // foreach
    	} // if
    	
    	$prepared_companies = array();
  		if(is_foreachable($prepared_contacts)) {
  			foreach($prepared_contacts as $prepared_contact) {
  				if($prepared_contact['object_type'] == 'Company') {
  					$prepared_companies[] = strtolower_utf($prepared_contact['name']);
  				} // if
  			} // foreach
  		} // if

      if($company_defined) {
        if(!in_array(strtolower_utf($company_name), $ac_companies) && !in_array(strtolower_utf($company_name), $prepared_companies) && $logged_user->isPeopleManager()) { // only administrators and people managers can create new company
          $prepared_contacts[] = array(
            'object_type'     => 'Company',
            'name'				    => $company_name,
            'office_address' 	=> $office_address,
            'is_new'			    => true
          );
        } // if

        // add new user to its company array (if there is)
        if(is_foreachable($prepared_contacts)) {
          foreach($prepared_contacts as $key => $prepared_contact) {
            if($prepared_contact['object_type'] == 'Company' && strtolower_utf($prepared_contact['name']) == strtolower_utf($user['company_name'])) {
              return $prepared_contacts[$key]['users'][] = $user;
            } // if
          } // foreach
        } // if
      } // if

  		return $prepared_contacts[] = $user;
    } // prepareUser
    
    /**
     * Import user from vCard
     * 
     * @param array $user_data
     * @param array $imported_users
     * @param Company $company
     * @return mixed
     * @throws InvalidInstanceError
     * @throws Exception
     */
    static function fromVCard($user_data, &$imported_users, $company = null) {
      try {
    		DB::beginWork('Import user @ ' . __CLASS__);
    		
    		$user = parent::fromVCard($user_data, $imported_users);

    		if(!($user instanceof User)) {
          throw new InvalidInstanceError('user', $user, 'User');
    		} // if
    		
    		// set these values only if it is a new user
    		if($user->isNew()) {
    			if($company instanceof Company) {
	    			$company_id = $company->getId();
	    		} else {
            $company_id = array_var($user_data, 'company_id');
            if(is_null($company_id)) {
              $company_id = Companies::findByName(array_var($user_data, 'company_name'))->getId();
            } // if
	    		} // if
	    		$user->setCompanyId($company_id);
    		} // if

    		$user->save();

    		// set config option values
    		$options = array('title', 'phone_work', 'phone_mobile', 'im_type', 'im_value');
    		foreach($options as $option) {
    			if($value = array_var($user_data, $option)) {
    				ConfigOptions::setValueFor($option, $user, $value);
    			} // if
    		} // foreach
    		
    		DB::commit('User imported @ ' . __CLASS__);
    		return true;
    	} catch(Exception $e) {
    		DB::rollback('Failed to import user @ ' . __CLASS__);
    		throw $e;
    	} // try
    } // fromVCard
    
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
    	return array(
    		'user' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE state = ? ORDER BY updated_on DESC', STATE_TRASHED)
    	);
    } // getTrashedMap
    
    /**
     * Find trashed users
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$trashed_company_ids = array_var($map, 'company');
    	    	
    	if (is_foreachable($trashed_company_ids)) {
   			$trashed_users = DB::execute('SELECT id, first_name, last_name, email, company_id FROM ' . TABLE_PREFIX . 'users WHERE state = ? AND company_id NOT IN (?) ORDER BY updated_on DESC', STATE_TRASHED, $trashed_company_ids);
    	} else {
    		$trashed_users = DB::execute('SELECT id, first_name, last_name, email, company_id FROM ' . TABLE_PREFIX . 'users WHERE state = ? ORDER BY updated_on DESC', STATE_TRASHED);
    	}  // if   	
    	
    	if (!is_foreachable($trashed_users)) {
    		return null;
    	} // if
    	    	
    	$view_url = Router::assemble('people_company_user', array('company_id' => '--COMPANY-ID--', 'user_id' => '--USER-ID--'));
    	    	
    	$items = array();
    	foreach ($trashed_users as $trashed_user) {
    		$items[] = array(
    			'id' => $trashed_user['id'],
    			'name' => Users::getUserDisplayName(array(
    				'first_name' => $trashed_user['first_name'],
    				'last_name' => $trashed_user['last_name'],
    				'email' => $trashed_user['email']
    			)),
    			'type' => 'User',
    			'permalink' => str_replace(array('--COMPANY-ID--', '--USER-ID--'), array($trashed_user['company_id'], $trashed_user['id']), $view_url),
    		);
    	} // foreach
    	
    	return $items;    	
    } // findTrashed
    
    /**
     * Delete trashed users
     * 
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$users = Users::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($users)) {
    		foreach ($users as $trashed_user) {
    			$trashed_user->state()->delete();
    		} // foreach
    	} // if
    	
    	return true;
    } // deleteTrashed
    
  }
