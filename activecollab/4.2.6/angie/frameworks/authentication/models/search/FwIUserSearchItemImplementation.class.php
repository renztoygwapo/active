<?php

  /**
   * User search item implementation
   * 
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwIUserSearchItemImplementation extends ISearchItemImplementation {
  
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
        'users' => array('first_name', 'last_name', 'email', 'group_id'),
      );
    } // getIndices
  
//    /**
//     * Return item context for given index
//     * 
//     * @param SearchIndex $index
//     * @return string
//     */
//    function getContext(SearchIndex $index) {
//      if($index instanceof UsersSearchIndex) {
//        return 'people/' . $this->object->getGroupId() . '/users';
//      } else {
//        throw new InvalidInstanceError('index', $index, 'UsersSearchIndex');
//      } // if
//    } // getContext
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     * @throws InvalidInstanceError
     */
    function getAdditional(SearchIndex $index) {
      if($index instanceof UsersSearchIndex) {
        return array(
          'group_id' => $this->object->getGroupId(), 
          'group' => $this->object->getGroupName(),  
          'name' => Users::getUserDisplayName(array(
            'first_name' => $this->object->getFirstName(),
            'last_name' => $this->object->getLastName(),
            'email' => $this->object->getEmail(),
          )),
          'email' => $this->object->getEmail(), 
        );
      } else {
        throw new InvalidInstanceError('index', $index, 'UsersSearchIndex');
      } // if
    } // getAdditional
    
  }