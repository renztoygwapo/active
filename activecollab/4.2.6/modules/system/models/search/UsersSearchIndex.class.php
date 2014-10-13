<?php

  /**
   * Application level search index implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class UsersSearchIndex extends FwUsersSearchIndex {
  
    /**
     * Cached filter definitions for this search index
     *
     * @var array
     */
    private $filters = false;
    
    /**
     * Return filters that can be used to limit results from this search index
     * 
     * @return array
     */
    function getFilters() {
      if($this->filters === false) {
        $visible_company_ids = Authentication::getLoggedUser() instanceof User ? Authentication::getLoggedUser()->visibleCompanyIds() : null;
        
        $this->filters = array(
          'group_id' => new EnumerableSearchFilter($this, 'group_id', lang('Company'), Companies::getIdNameMap($visible_company_ids)),  
        );
      } // if
      
      return $this->filters;
    } // getFilters
    
    /**
     * Return context filter for a given user
     * 
     * @param IUser $user
     * @return string
     */
    function getUserFilter(IUser $user) {
      if($user->isPeopleManager() || $user->isFinancialManager()) {
        return null;
      } else {
        $user_ids = $user->visibleUserIds();
        
        if($user_ids) {
          return new SearchCriterion('item_id', SearchCriterion::IS, $user_ids);
        } else {
          return false;
        } // if
      } // if
    } // getUserFilter
    
  }