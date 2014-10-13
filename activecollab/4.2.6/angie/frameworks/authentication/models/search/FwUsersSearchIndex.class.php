<?php

  /**
   * Users search index
   * 
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwUsersSearchIndex extends SearchIndex {
    
    /**
     * Return short name of this index
     * 
     * @return string
     */
    function getShortName() {
      return 'users';
    } // getShortName
  
    /**
     * Return index name
     * 
     * @return string
     */
    function getName() {
      return lang('Users');
    } // getName
    
    /**
     * Return index fields
     * 
     * @return array
     */
    function getFields() {
      return array(
        'name' => self::FIELD_STRING, 
        'email' => self::FIELD_STRING, 
        'group_id' => self::FIELD_NUMERIC, 
        'group' => self::FIELD_STRING, 
      );
    } // getFields
    
    // ---------------------------------------------------
    //  Rebuild
    // ---------------------------------------------------
    
    /**
     * Return steps to rebuild this search index
     */
    function getRebuildSteps() {
      $steps = parent::getRebuildSteps();
      
      $steps[] = array(
        'text' => lang('Build Index'), 
       	'url' => $this->getBuildUrl(),
      );
      
      return $steps;
    } // getRebuildSteps
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Return build index URL
     * 
     * @return string
     */
    function getBuildUrl() {
      return Router::assemble('users_search_index_admin_build');
    } // getBuildUrl
    
  }