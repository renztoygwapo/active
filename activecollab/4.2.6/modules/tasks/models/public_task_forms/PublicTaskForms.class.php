<?php

  /**
   * PublicTaskForms class
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class PublicTaskForms extends BasePublicTaskForms {
    
    /**
     * Returns true if $user can create a new task form
     * 
     * @param IUser $user
     * @return boolean
     */
    static function canAdd(IUser $user) {
      return $user->isAdministrator();
    } // canAdd
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
  
    /**
  	 * Return log slice based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
     * @param string $timestamp
  	 * @return PublicTaskForm[]
  	 */
    static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return PublicTaskForms::find(array(
  			  'conditions' => array("id NOT IN (?)", $exclude), 
  			  'order' => 'slug', 
  			  'limit' => $num,  
  			));
  		} else {
  			return PublicTaskForms::find(array(
  			  'order' => 'slug', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
  	
  	/**
  	 * Find by slug
  	 * 
  	 * @param string $slug
     * @return PublicTaskForm[]
  	 */
  	static function findBySlug($slug) {
  		return PublicTaskForms::find(array(
  			'conditions' => array('slug = ? ', $slug),
  			'one' => true
  		));
  	} // findBySlug
  	
  	/**
  	 * Return enabled public task forms
     *
     * @return PublicTaskForm[]
  	 */
  	static function findEnabled() {
  	  return PublicTaskForms::find(array(
  	    'conditions' => array('is_enabled = ?', true), 
  	    'order' => 'name', 
  	  ));
  	} // findEnabled
  
  }