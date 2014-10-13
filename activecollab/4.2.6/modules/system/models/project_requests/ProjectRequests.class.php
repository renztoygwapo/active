<?php

  /**
   * ProjectRequests class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectRequests extends BaseProjectRequests {

    /**
     * Default ordering of project requests
     *
     * @var string
     */
    static private $order_project_requests_by = 'last_comment_on DESC';

    /**
     * Returns true if $user can use project requests
     *
     * @param IUser $user
     * @return bool
     */
    static function canUse(IUser $user) {
      return $user instanceof Client ? $user->getSystemPermission('can_request_projects') : self::canManage($user);
    } // canUse
    
    /**
     * Returns true if $user can create a new project requests
     * 
     * @param IUser $user
     * @return boolean
     */
    static function canAdd(IUser $user) {
      return $user instanceof Client ? $user->getSystemPermission('can_request_projects') : self::canManage($user);
    } // canAdd
    
    /**
     * Returns true if $user can manage project requests
     * 
     * @param IUser $user
     * @return boolean
     */
    static function canManage(IUser $user) {
      if($user instanceof User) {
        return $user->isAdministrator() || ($user->isManager() && $user->getSystemPermission('can_manage_project_requests'));
      } else {
        return false;
      } // if
    } // canManage

    /**
     * Returns true if $user can request project for $company
     *
     * @param Company $company
     * @param User $user
     * @return bool
     */
    static function canRequestProjectsFor(Company $company, User $user) {
      if($company instanceof Company && !$company->isOwner()) {
        return $user instanceof Client && $user->canRequestProjects() && $user->getCompanyId() == $company->getId();
      } else {
        return false;
      } // if
    } // canRequestProjectsFor
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
  
    /**
     * Find project request by $public_id
     *
     * @param integer $public_id
     * @return Project Request
     */
    static function findByPublicId($public_id) {
    	return ProjectRequests::find(array(
    		'conditions' => array('public_id = ?', $public_id),
    		'one' => true
    	));
    } // findByPublicId
    
    /**
     * Paginate project requests by $status
     *
     * @param integer $status
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateByStatus($status = 0, $page = 1, $per_page = 30) {
    	return ProjectRequests::paginate(array(
    		'conditions' => array('status = ?', $status),
    		'order' => self::$order_project_requests_by
    	), $page, $per_page);
    } // paginateByStatus
    
    /**
     * Return active project requests
     *
     * @param User $user
     * @return ProjectRequest[]
     */
    static function findActive(User $user) {
      if($user instanceof Client) {
        $conditions = array('created_by_company_id = ? AND status IN (?) AND closed_on IS NULL', array($user->getCompanyId(), ProjectRequest::STATUS_NEW, ProjectRequest::STATUS_REPLIED));
      } else {
        $conditions = array('status IN (?) AND closed_on IS NULL', array(ProjectRequest::STATUS_NEW, ProjectRequest::STATUS_REPLIED));
      } // if

      return ProjectRequests::find(array(
        'conditions' => $conditions,
        'order' => self::$order_project_requests_by,
      ));
    } // findActive
    
    /**
     * Return closed page requests
     *
     * @param User $user
     * @return ProjectRequest[]
     */
    static function findClosed(User $user) {
      if($user instanceof Client) {
        $conditions = array('created_by_company_id = ? AND status = ? AND closed_on IS NOT NULL', ProjectRequest::STATUS_CLOSED);
      } else {
        $conditions = array('status = ? AND closed_on IS NOT NULL', ProjectRequest::STATUS_CLOSED);
      } // if

      return ProjectRequests::find(array(
        'conditions' => $conditions,
        'order' => 'closed_on'
      ));
    } // findClosed
    
    /**
     * Return project requests prepared to be displayed in project requests list
     * 
     * @param User $user
     * @return array
     */
    static function findForObjectsList(User $user) {
      $project_url = Router::assemble('project_request', array('project_request_id' => '--PROJECT-REQUEST-ID--'));

      if($user instanceof Client) {
        $project_requests = DB::execute('SELECT id, name, status, taken_by_id, taken_by_name, taken_by_email FROM ' . TABLE_PREFIX . 'project_requests WHERE created_by_company_id = ? ORDER BY ' . self::$order_project_requests_by, $user->getCompanyId());
      } else {
        $project_requests = DB::execute('SELECT id, name, status, taken_by_id, taken_by_name, taken_by_email FROM ' . TABLE_PREFIX . 'project_requests ORDER BY ' . self::$order_project_requests_by);
      } // if

      if($project_requests instanceof DBResult) {
        $project_requests = $project_requests->toArray();
        
        $user_ids = array();
        foreach($project_requests as $project_request) {
          if($project_request['taken_by_id'] && !in_array($project_request['taken_by_id'], $user_ids)) {
            $user_ids[] = (integer) $project_request['taken_by_id'];
          } // if
        } // foreach
        
        $user_names = Users::getIdNameMap($user_ids);
        
        foreach($project_requests as $k => $project_request) {
          $taken_by_id = $project_request['taken_by_id'] === null ? null : (integer) $project_request['taken_by_id'];
          
          if($taken_by_id && $user_names && isset($user_names[$taken_by_id])) {
            $project_requests[$k]['taken_by'] = Users::getUserDisplayName(array(
              'full_name' => $user_names[$taken_by_id], 
              'email' => $project_request['taken_by_email'], 
            ), true);
          } else {
            $project_requests[$k]['taken_by'] = Users::getUserDisplayName(array(
              'full_name' => $project_request['taken_by_email'], 
              'email' => $project_request['taken_by_email'], 
            ), true);
          } // if
          
          $project_requests[$k]['taken_by_id'] = $taken_by_id;
          unset($project_requests[$k]['taken_by_name']);
          unset($project_requests[$k]['taken_by_email']);
          
          $project_requests[$k]['id'] = (integer) $project_requests[$k]['id'];
          $project_requests[$k]['status'] = (integer) $project_requests[$k]['status']; 
          $project_requests[$k]['is_closed'] = $project_request['status'] == ProjectRequest::STATUS_CLOSED ? 1 : 0;
          $project_requests[$k]['permalink'] = str_replace('--PROJECT-REQUEST-ID--', $project_request['id'], $project_url);
        } // foreach
        
        return $project_requests;
      } else {
        return array();
      } // if
    } // findforObjectsList
    
    /**
     * Find project requests for printing by grouping and filtering criteria
     *
     * @param User $user
     * @param string $group_by
     * @param array $filter_by
     * @return ProjectRequest[]
     */
    static function findForPrint(User $user, $group_by = null, $filter_by = null) {
      if (!in_array($group_by, array('milestone_id', 'category_id'))) {
      	$group_by = null; // initial condition
      } // if

      $conditions = array();

      if($user instanceof Client) {
        $conditions[] = DB::prepare('created_by_company_id = ?', $user->getCompany());
      } // if
                
      // Filter by completion status
      $filter_is_completed = array_var($filter_by, 'is_closed', null);
      if ($filter_is_completed === '0') {
				$conditions[] = DB::prepare('(status != ?)', ProjectRequest::STATUS_CLOSED);
      } else if ($filter_is_completed === '1') {
      	$conditions[] = DB::prepare('(status = ?)', ProjectRequest::STATUS_CLOSED);
      } // if
      
      // Find project requests
      $project_requests = ProjectRequests::find(array(
      	'conditions' => implode(' AND ', $conditions)
      ));
    	
    	return $project_requests;
    } // findForPrint
    
    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------
    
    /**
     * Cached array of custom request fields
     *
     * @var array
     */
    static private $custom_fields = false;
    
    /**
     * Return array of custom fields
     * 
     * @return array
     */
    static function getCustomFields() {
      if(self::$custom_fields === false) {
        self::$custom_fields = ConfigOptions::getValue('project_requests_custom_fields');
        
        if(!is_array(self::$custom_fields)) {
          self::$custom_fields = array();
        } // if
      } // if
      
      return self::$custom_fields;
    } // getCustomFields
    
    /**
     * Set array of custom fields
     * 
     * @param array $value
     * @return array
     * @todo
     */
    static function setCustomFields($value) {
      
    } // setCustomFields
    
    /**
     * Returns true if CAPTCHA is enabled
     * 
     * @return boolean
     */
    static function isCaptchaEnabled() {
      return (extension_loaded('gd') || extension_loaded('gd2')) && function_exists('imagettftext') && ConfigOptions::getValue('project_requests_captcha_enabled');
    } // isCaptchaEnabled

    /**
     * Count Project requests by company
     *
     * @param Company $company
     * @return int
     */
    public static function countByCompany(Company $company) {
      return self::count(array('created_by_company_id = ?', $company->getId()));
    } // countByCompany

    /**
     * Find project requests by company
     *
     * @param Company $company
     * @return mixed
     */
    public static function findByCompany(Company $company) {
      return self::find(array(
        'conditions'  => array('created_by_company_id = ?', $company->getId()),
        'order'       => self::$order_project_requests_by
      ));
    } // findByCompany
  
  }