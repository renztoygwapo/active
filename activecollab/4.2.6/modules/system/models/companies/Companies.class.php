<?php

  /**
   * Companies manager class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Companies extends BaseCompanies {
    
    /**
     * Can $user create a new company
     *
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return $user->isPeopleManager();
    } // canAdd

    /**
     * Returns true if $user can see company notes
     *
     * @param User $user
     * @return boolean
     */
    static function canSeeNotes(User $user) {
      return $user->isAdministrator() || $user->isManager() || $user->getSystemPermission('can_see_company_notes');
    } // canSeeNotes
    
    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------

    /**
     * Find companies for printing by grouping and filtering criteria
     *
     * @param IUser $user
     * @param null $filter_by
     * @return array
     */
    static function findForPrint(IUser $user, $filter_by = null) {
      $visible_ids = $user instanceof User ? $user->visibleCompanyIds() : null;

      if(is_foreachable($visible_ids)) {
        
        // filter by visibility status
        $filter_is_archived = array_var($filter_by, 'is_archived', null);
        if ($filter_is_archived === '0') {
          $state = STATE_VISIBLE;        	
        } else if ($filter_is_archived === '1') {
          $state = STATE_ARCHIVED;        
        } // if

        if(!$state) {
          $companies = DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "companies WHERE id IN (?) ORDER BY is_owner DESC, name", $visible_ids);
        } else {
          $companies = DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "companies WHERE state = ? AND id IN (?) ORDER BY is_owner DESC, name", $state, $visible_ids);
        }//if
        
        if(is_foreachable($companies)) {
          foreach($companies as $company) {
            $temp[$company['name']] = Users::findByCompanyId($company['id']);
          }//foreach
        }//if
        
        return $temp;
        
      } // if

      return null;
    } // findForPrint

    /**
     * Prepare companies map for objects list
     *
     * @param User $user
     * @param int $min_state
     * @return array
     */
    static function findForObjectsList(User $user, $min_state = STATE_ARCHIVED) {
      $companies_map = array();
      $company_single_url = Router::assemble('people_company', array('company_id' => '__COMPANY_ID__'));

      $companies = DB::execute('SELECT id, name, state FROM ' . TABLE_PREFIX . 'companies WHERE id IN (?) AND state >= ? ORDER BY is_owner DESC, name ASC', $user->visibleCompanyIds(), $min_state);
      if ($companies) {
        foreach ($companies as $company) {
          $companies_map[$company['id']] = array(
            'name' => $company['name'],
            'permalink' => str_replace('__COMPANY_ID__', $company['id'], $company_single_url),
            'is_archived' => $company['state'] == STATE_ARCHIVED ? 1 : 0,
            'icon' => get_company_logo_url($company['id'], '16x16'),
          );
        } // foreach
      } // if

      return $companies_map;
    } // findForObjectsList
    
    /**
     * Return contexts by user
     * 
     * If $company_ids is null, system will return contexts from all companies 
     * that given user can see
     * 
     * @param IUser $user
     * @param array $contexts
     * @param array $ignore_contexts
     * @param array $company_ids
     */
    static function getContextsByUser(IUser $user, &$contexts, &$ignore_contexts, $company_ids = null) {
      if($company_ids && !is_array($company_ids)) {
        $company_ids = array($company_ids);
      } // if
      
      if($user instanceof User) {
        if($user->isPeopleManager() || $user->isFinancialManager()) {
          if($company_ids) {
            foreach($company_ids as $company_id) {
              $contexts[] = "people:companies/$company_id";
              $contexts[] = "people:companies/$company_id/%";
            } // foreach
          } else {
            $contexts[] = 'people:companies/%';
          } // if
        } else {
          $contexts[] = "people:/companies/{$user->getCompanyId()}"; // User's company
          $contexts[] = "people:/companies/{$user->getCompanyId()}/%"; // All members of user's company

          $visible_user_ids = Users::findVisibleUserIds($user);
          if($visible_user_ids) {
            $user_counts = array();
            
            $rows = DB::execute("SELECT company_id, COUNT(id) AS 'count' FROM " . TABLE_PREFIX . "users WHERE state >= ? AND company_id != ? AND id IN (?) GROUP BY company_id", STATE_VISIBLE, $user->getCompany()->getId(), $visible_user_ids);
            if($rows) {
              foreach($rows as $row) {
                $user_counts[(integer) $row['company_id']] = (integer) $row['count'];
              } // foreach
            } // if
            
            $company_users = array();
            
            $rows = DB::execute("SELECT id, company_id FROM " . TABLE_PREFIX . "users WHERE state >= ? AND id IN (?)", STATE_VISIBLE, $visible_user_ids);
            if($rows) {
              foreach($rows as $row) {
                $company_id = (integer) $row['company_id'];
                
                if(!isset($company_users[$company_id])) {
                  $company_users[$company_id] = array();
                } // if
                
                $company_users[$company_id][] = (integer) $row['id'];
              } // foreach
            } // if
            
            foreach($company_users as $company_id => $user_ids) {
              if(isset($user_counts[$company_id]) && $user_counts[$company_id] == count($user_ids)) {
                $contexts[] = "people:companies/$company_id";
                $contexts[] = "people:companies/$company_id/%";
              } else {
                foreach($user_ids as $user_id) {
                  $contexts[] = "people:companies/$company_id/users/$user_id";
                } // foreach
              } // if
            } // foreach
          } // if
        } // if
      } // if
    } // getContextsByUser
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Find companies for quick add
     * 
     * @param User $user
     * @return Company[]
     */
    static function findForQuickAdd(User $user) {
    	return Companies::find(array(
    		'conditions' => array('id IN (?) AND state >= ?', $user->visibleCompanyIds(), STATE_VISIBLE)
    	));
    } // findForQuickAdd

  	/**
  	 * Find companies that aren't trashed or deleted
  	 *
  	 * @param integer $min_state
  	 * @return DBResult
  	 */
  	static function findActiveAndArchived($min_state = STATE_ARCHIVED) {
  		return Companies::find(array(
  			'conditions' => array('state >= ?', $min_state)
  		));
  	} // findAll

  	/**
  	 * Find company by its name
  	 *
  	 * @param string $name
     * @param int $min_state
  	 * @return Company
  	 */
  	static function findByName($name, $min_state = STATE_TRASHED) {
  		return Companies::find(array(
  			'conditions' => array('name = ? AND state >= ?', $name, $min_state),
  			'one' => true
  		));
  	} // findByName
  	
  	/**
     * Find active companies visible to $user
     *
     * @param User $user
     * @return array
     */
    static function findActive(User $user) {
      $visible_ids = $user->visibleCompanyIds();

      if($visible_ids) {
        return Companies::find(array(
          'conditions' => array('(state >= ? OR id = ?) AND id IN (?)', STATE_VISIBLE, $user->getCompanyId(), $visible_ids),
          'order' => 'is_owner DESC, name',
        ));
      } else {
        return false;
      } // if
    } // findActive
    
    /**
     * Paginate active categories
     *
     * @param User $user
     * @param integer $min_state
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    static function paginateActive(User $user, $page = 1, $per_page = 30) {
      $visible_ids = $user->visibleCompanyIds();
      if(is_foreachable($visible_ids)) {
        return Companies::paginate(array(
          'conditions' => array('(state >= ? OR id = ?) AND id IN (?)', STATE_VISIBLE, $user->getCompanyId(), $visible_ids),
          'order' => 'is_owner DESC, name',
        ), $page, $per_page);
      } else {
        return array(null, new Pager(0, 1, $per_page));
      } // if
    } // paginateActive
    
    /**
     * Find archived companies visible to $user
     *
     * @param User $user
     * @return array
     */
    static function findArchived(User $user) {
      $visible_ids = $user->visibleCompanyIds();
      if(is_foreachable($visible_ids)) {
        return Companies::find(array(
          'conditions' => array('state = ? AND id IN (?)', STATE_ARCHIVED, $visible_ids),
          'order' => 'name',
        ));
      } else {
        return false;
      } // if
    } // findArchived
    
    /**
     * Return list of archived companies
     *
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    static function paginateArchived($user, $page = 1, $per_page = 30) {
      $visible_ids = $user->visibleCompanyIds();
      if(is_foreachable($visible_ids)) {
        return Companies::paginate(array(
          'conditions' => array('state = ? AND id IN (?)', STATE_ARCHIVED, $visible_ids),
          'order' => 'is_owner DESC, name',
        ), $page, $per_page);
      } else {
        return array(null, new Pager(0, 1, $per_page));
      } // if
    } // paginateArchived
    
    /**
     * Cached instance of owner company
     *
     * @var Company
     */
    static private $owner_company = false;
  
    /**
     * Return owner company from database
     *
     * @param boolean $force_load
     * @return Company
     */
    static function findOwnerCompany($force_load = false) {
      if($force_load || self::$owner_company === false) {
        self::$owner_company = Companies::find(array(
          'conditions' => array('is_owner = ?', true),
          'one' => true,
        ));
      } // if
      return self::$owner_company;
    } // findOwnerCompany
    
    /**
     * Return ID => name map
     *
     * @param mixed $ids
     * @param integer $min_state
     * @return array
     */
    static function getIdNameMap($ids = null, $min_state = STATE_TRASHED) {
      
      // No ID-s
      if($ids === null) {
        return AngieApplication::cache()->get(array('companies_id_name_map', $min_state), function() use ($min_state) {
          $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'companies WHERE state >= ? ORDER BY is_owner DESC, name', $min_state);
          if($rows) {
            $result = array();

            foreach($rows as $row) {
              $result[(integer) $row['id']] = $row['name'];
            } // foreach

            return $result;
          } // if

          return null;
        });
        
      // We have ID-s
      } else {
        if(is_foreachable($ids)) {
          $from_cache = Companies::getIdNameMap(null, $min_state);

          if($from_cache) {
            foreach($from_cache as $k => $v) {
              if(!in_array($k, $ids)) {
                unset($from_cache[$k]);
              } // if
            } // foreach
          } // if

          return $from_cache;
        } // if
        
        return null;
      } // if
    } // getIdNameMap
    
    /**
     * Returns array of companies that are involved in project
     * 
     * @param Project $project
     * @return DBResult
     */
    function findByProject(Project $project) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $users_table = TABLE_PREFIX . 'users';
      
      $company_ids = DB::executeFirstColumn("SELECT DISTINCT $users_table.company_id FROM $users_table JOIN $project_users_table ON $users_table.id = $project_users_table.user_id WHERE $project_users_table.project_id = ?", $project->getId());
      
      return is_foreachable($company_ids) ? Companies::findByIds($company_ids) : null;
    } // findByProject
       
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
    	return array(
    		'company' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'companies WHERE state = ? ORDER BY updated_on DESC', STATE_TRASHED)
    	);
    } // getTrashedMap
    
    /**
     * Find trashed companies
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$trashed_companies = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'companies WHERE state = ? ORDER BY updated_on DESC', STATE_TRASHED);
    	if (!is_foreachable($trashed_companies)) {
    		return null;
    	} // if
    	
    	$view_url = Router::assemble('people_company', array('company_id' => '--COMPANY-ID--'));
    	    	
    	$items = array();
    	foreach ($trashed_companies as $company) {
    		$items[] = array(
    			'id'						=> $company['id'],
    			'name'					=> $company['name'],
    			'type'					=> 'Company',
    			'permalink'			=> str_replace('--COMPANY-ID--', $company['id'], $view_url),
					'can_be_parent'	=> true
    		);
    	} // foreach
    	
    	return $items;    	
    } // findTrashed
    
    /**
     * Delete trashed companies
     * 
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$companies = Companies::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($companies)) {
	    	foreach ($companies as $company) {
	    		$company->state()->delete();
	    	} // foreach
    	} // if
    	
    	return true;
    } // deleteTrashed

    /**
     * Returns true if $name is used by another company that's not deleted
     *
     * @param string $name
     * @param mixed $exclude_company
     * @return bool
     */
    static function isCompanyNameInUse($name, $exclude_company = null) {
      if($exclude_company) {
        $exclude_company_id = $exclude_company instanceof User ? $exclude_company->getId() : $exclude_company;

        return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'companies WHERE name = ? AND state > ? AND id != ?', $name, STATE_DELETED, $exclude_company_id);
      } else {
        return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'companies WHERE name = ? AND state > ?', $name, STATE_DELETED);
      } // if
    } // isCompanyNameInUse
    
    // ---------------------------------------------------
	  //  vCard
	  // ---------------------------------------------------
    
    /**
	   * Render vCard data
	   *
	   * @param array $companies
	   * @param boolean $compress_vcards
	   * @return mixed
	   */
	  function render_vcard($companies, $compress_vcards = false) {
			if($compress_vcards) {
				if(!is_dir(EXPORT_DIR_PATH) && !mkdir(EXPORT_DIR_PATH, 0777, true)) {
				  throw new Exception(lang('Failed to create vCard export directory: :dir_path', array('dir_path' => EXPORT_DIR_PATH)));
				} // if
				@chmod(EXPORT_DIR_PATH, 0777);
			} // if
	
			$vcard_content = '';
			if(is_foreachable($companies)) {
				foreach($companies as $company) {
					if($company instanceof Company) {
						$vcard_content .= $company->toVCard(false, false, $compress_vcards);
	
						if(is_foreachable($users = $company->getUsers())) {
							foreach($users as $user) {
								if($user instanceof User) {
									$vcard_content .= $user->toVCard(false, $compress_vcards, $company);
								} // if
							} // foreach
						} // if
					} // if
				} // foreach
			} // if
			
			if($compress_vcards) {
				vCardForAngie::compress_vcards();
				
				header('Content-Type: application/zip; charset=UTF-8');
		    header('Content-Disposition: attachment; filename="' . basename(EXPORT_DIR_PATH . '.zip') . '"');
		    @readfile(EXPORT_DIR_PATH . '.zip');
			} else {
				header('Content-Type: text/x-vcard; charset=UTF-8');
		    header('Content-Disposition: attachment; filename="contacts.vcf"');
		    print $vcard_content;
		    die();
			} // if
	  } // render_vcard
	  
	  /**
	   * Preapare vCard data for import
	   *
	   * @param array $prepared_contacts
	   * @param array $vcard
	   * @param User $user
	   * @return array
	   */
	  function prepare_contacts(&$prepared_contacts, $vcard, $user) {
	  	// import vCard as company if there's explicitely stated that it's a company
	  	// and it does have ORG component or if there are defined ORG and FN
	  	// components and they're equal
	  	if(((array_key_exists('X-ABSHOWAS', $vcard) || array_key_exists('X-ABShowAs', $vcard)) && (array_key_exists('ORG', $vcard))) || ((array_key_exists('ORG', $vcard) && array_key_exists('FN', $vcard)) && (trim($vcard['ORG'][0]['value'][0][0]) == trim($vcard['FN'][0]['value'][0][0])))) {
	  		Companies::prepareCompany($prepared_contacts, $vcard, $user);
	  		
	  	// import vCard as user if there are defined ORG and FN components and they
	  	// aren't equal
	  	} elseif(((array_key_exists('ORG', $vcard) && array_key_exists('FN', $vcard)) && (trim($vcard['ORG'][0]['value'][0][0]) != trim($vcard['FN'][0]['value'][0][0])))) {
	  		Users::prepareUser($prepared_contacts, $vcard, $user);

      // import vCard as user even if there are no defined ORG component
	  	} elseif(!array_key_exists('ORG', $vcard)) {
        Users::prepareUser($prepared_contacts, $vcard, $user, false);
      } // if
	
	  	return $prepared_contacts;
	  } // prepare_contacts
    
    /**
     * Prepare company for import
     *
     * @param array $prepared_contacts
     * @param array $vcard
     * @param User $logged_user
     * @return array
     */
    function prepareCompany(&$prepared_contacts, $vcard, $logged_user) {
    	$company = array();

    	if(!array_key_exists('ORG', $vcard)) { // company name is required
    		return true;
    	} // if

    	// company data initialization
    	$name = $office_address = $office_phone = $office_fax = $office_homepage = $updated_on = '';

    	$name = trim($vcard['ORG'][0]['value'][0][0]);

    	$components = array('ADR', 'TEL', 'URL');
  		foreach($components as $component) {
  			if(array_key_exists($component, $vcard) && is_foreachable($vcard[$component])) {
  				switch($component) {
  					case "ADR":
  						if(is_foreachable($vcard[$component][0]['value'])) {
  							$value = '';
  							foreach($vcard[$component][0]['value'] as $k => $v) {
                  if(is_foreachable($v)) {
                    foreach($v as $part_of_address) {
                      if(trim($part_of_address != '')) {
                        $value .= trim($part_of_address) . "\n";
                      } // if
                    } // foreach
                  } // if
  								
  								if($value != '') {
    								$office_address = $value;
	    						} // if
  							} // foreach
  						} // if
  						break;
  					case "TEL":
  						$telephone_types = array('WORK' => 'office_phone', 'FAX' => 'office_fax');
  						
  						foreach($telephone_types as $vcard_type => $ac_type) {
  							$value = '';
  							foreach($vcard[$component] as $k => $telephone) {
  								if(is_foreachable($telephone['param']) && strtoupper($telephone['param']['TYPE'][0]) == $vcard_type) {
  									$value = $telephone['value'][0][0];
  									
  									if($value != '') {
                      if($vcard_type == 'WORK') {
                        if(isset($telephone['param']['TYPE'][1]) && $telephone['param']['TYPE'][1] == 'FAX') {
                          $office_fax = $value;
                        } else {
                          $office_phone = $value;
                        } // if
                      } else {
                        $office_fax = $value;
                      } // if
  									} // if
  								} // if
  							} // foreach
  						} // foreach
  						break;
  					case "URL":
  						if(is_foreachable($vcard[$component][0]['value'])) {
  							$value = stripslashes($vcard[$component][0]['value'][0][0]);

  							if($value && strpos($value, '://') === false) {
		              $value = 'http://' . $value;
		            } // if
		            
		            if($value != '') {
  								$office_homepage = $value;
    						} // if
  						} // if
  						break;
  				} // switch
  			} // if
  		} // foreach
  		
  		if(array_key_exists('REV', $vcard)) {
  			$updated_on = DateTimeValue::makeFromString($vcard['REV'][0]['value'][0][0]);
  		} // if
  		
  		$config_option_values = array(
  			'office_address' 	=> $office_address,
  			'office_phone' 		=> $office_phone,
  			'office_fax' 			=> $office_fax,
  			'office_homepage' => $office_homepage
  		);

  		// Check whether company exists in aC
			$companies_table = TABLE_PREFIX . 'companies';
  		$rows = DB::execute("SELECT name FROM $companies_table WHERE state >= ?", STATE_TRASHED);

    	$ac_companies = array();
    	if(is_foreachable($rows)) {
	    	foreach($rows as $row) {
	    		$ac_companies[] = trim($row['name']);
	    	} // foreach
    	} // if

    	in_array($name, $ac_companies) ? $is_new = false : $is_new = true;

  		// If it's an existing company populate related info from the DB
    	$company_instance = Companies::findByName($name);
    	if($company_instance instanceof Company && !$is_new) {
	  		if(!$logged_user->isPeopleManager() && !$company_instance->canEdit($logged_user)) { // check whether user has enough permissions to update company
	    		return true;
	    	} // if

    		if(is_foreachable($config_option_values)) {
	  			foreach($config_option_values as $k => &$v) {
	  				if($v == '') {
	  					$v = ConfigOptions::getValueFor($k, $company_instance);
	  				}// if
	  			} // foreach
                unset($v);
    		} // if
    	} // if
    	
    	if(!($company_instance instanceof Company) && $is_new && !$logged_user->isPeopleManager()) { // check whether user is have enough permissions to create new company
    		return true;
    	} // if

      $company = array(
        'object_type'			=> 'Company',
        'name' 						=> $name,
        'office_address' 	=> $config_option_values['office_address'],
        'office_phone' 		=> $config_option_values['office_phone'],
        'office_fax' 			=> $config_option_values['office_fax'],
        'office_homepage' => $config_option_values['office_homepage'],
        'is_new'					=> $is_new,
        'updated_on' 			=> $updated_on
      );

      // Replace new company if already prepared for import based on other prepared users' data
      if($is_new && is_foreachable($prepared_contacts)) {
        foreach($prepared_contacts as $key => $prepared_contact) {
          if($prepared_contact['object_type'] == 'Company' && strtolower_utf($prepared_contact['name']) == strtolower_utf($name)) {
            // Switch already prepared users
            $company['users'] = $prepared_contacts[$key]['users'];

            return $prepared_contacts[$key] = $company;
          } // if
        } // foreach
      } // if

    	return $prepared_contacts[] = $company;
    } // prepareCompany
    
    /**
     * Import company
     *
     * @param array $company_data
     * @param array $imported_companies
     * @param array $imported_users
     * @param integer $count_users
     * @return boolean
     * @throws InvalidInstanceError
     * @throws Exception
     */
    function fromVCard($company_data, &$imported_companies, &$imported_users, &$count_users = 0) {
    	try {
    		DB::beginWork('Import company @ ' . __CLASS__);
    		
    		// check whether it's a new company
    		$is_new = array_var($company_data, 'is_new') == 'true';

    		// create an instance of Company class appropriately
    		if($is_new) {
    			$company = new Company();
    		} else {
    			$company = Companies::findByName(array_var($company_data, 'old_name'));
    		} // if

    		if(!($company instanceof Company)) {
          throw new InvalidInstanceError('company', $company, 'Company');
    		} // if

    		$company->setName(array_var($company_data, 'name'));
    		
    		// set these values only if it is a new user
    		if($is_new) {
    			$company->setState(STATE_VISIBLE);
    			
    			if(array_var($company_data, 'updated_on')) {
	    			$company->setUpdatedOn(DateTimeValue::makeFromString(array_var($company_data, 'updated_on')));
	    			$company->setUpdatedBy(Authentication::getLoggedUser());
	    		} // if
    		} // if
    		
    		$company->save();
    		
    		// Collect imported companies for updating object list
    		$imported_companies[] = array(
    			'is_new' => $is_new,
    			'company' => $company
    		);
    		
    		// set config option values
    		$options = array('office_address', 'office_phone', 'office_fax', 'office_homepage');
    		foreach($options as $option) {
    			if($value = array_var($company_data, $option)) {
    				ConfigOptions::setValueFor($option, $company, $value);
    			} // if
    		} // foreach

    		// import company's users (if any)
    		$users = array_var($company_data, 'users');
    		if(isset($users) && is_foreachable($users)) {
    			foreach($users as $user) {
    				if(isset($user['import']) && array_var($user, 'import') == 'ok') {
    					$user_imported = Users::fromVCard($user, $imported_users, $company);
    					
    					if($user_imported) {
	    					$count_users++;
	    				} // if
    				} // if
    			} // foreach
    		} // if

    		DB::commit('Company imported @ ' . __CLASS__);
    		return true;
    	} catch(Exception $e) {
    		DB::rollback('Failed to import company @ ' . __CLASS__);
    		throw $e;
    	} // try
    } // fromVCard
    
  }