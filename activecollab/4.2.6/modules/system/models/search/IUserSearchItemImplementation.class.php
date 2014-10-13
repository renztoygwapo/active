<?php

  /**
   * Application level user search item implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IUserSearchItemImplementation extends FwIUserSearchItemImplementation {
  
    /**
     * Return list of indices that index parent object
     * 
     * Result is an array where key is the index name, while value is list of 
     * fields that's watched for changes
     * 
     * @return array
     */
    function getIndices() {
      return array(
        'users' => array('first_name', 'last_name', 'email', 'company_id'),
        'names' => array('first_name', 'last_name', 'email'),
      );
    } // getIndices
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     */
    function getAdditional(SearchIndex $index) {
      if($index instanceof NamesSearchIndex) {
        return array(
          'name' => Users::getUserDisplayName(array(
            'first_name' => $this->object->getFirstName(),
            'last_name' => $this->object->getLastName(),
            'email' => $this->object->getEmail(),
          )),
          'short_name' => $this->object->getEmail(),
          'visibility' => VISIBILITY_NORMAL, 
        );
      } else {
        return parent::getAdditional($index);
      } // if
    } // getAdditional

    /**
     * Describe parent object to be used in search result
     *
     * @param IUser $user
     * @return array
     */
    function describeForSearch(IUser $user) {
      $result = parent::describeForSearch($user);

      $result['short_name'] = $this->object->getEmail();

      return $result;
    } // describeForSearch
    
  }