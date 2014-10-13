<?php

  /**
   * activeCollab specific model definition
   *
   * @package activeCollab
   * @subpackage resources
   */
  class ActiveCollabModuleModel extends AngieModuleModel {
  	
  	/**
  	 * Create a new company and return company ID
  	 * 
  	 * @param string $name
  	 * @param array $additional
  	 * @return integer
  	 */
  	protected function addCompany($name, $additional = null) {
  		$properties = array(
  		  'name' => $name, 
  		  'state' => 3, // STATE_VISIBLE
  		);
  		
  		if(is_array($additional)) {
  			$properties = array_merge($properties, $additional);
  		} // if
  		
  		return $this->createObject('companies', $properties);
  	} // addCompany
  	
  	/**
  	 * Create a user and return user ID
  	 * 
  	 * @param string $email
  	 * @param integer $company_id
  	 * @param array $additional
  	 * @return integer
  	 */
  	protected function addUser($email, $company_id, $additional = null) {
  		$properties = array(
  		  'company_id' => $company_id,
  		  'state' => 3, // STATE_VISIBLE 
  		  'email' => $email, 
  		);
  		
  		if(is_array($additional)) {
  			$properties = array_merge($properties, $additional);
  		} // if
  		
  		if(isset($properties['password'])) {
  			$properties['password'] = base64_encode(pbkdf2($properties['password'], APPLICATION_UNIQUE_KEY, 1000, 40));
  		} else {
  			$properties['password'] = base64_encode(pbkdf2('test', APPLICATION_UNIQUE_KEY, 1000, 40));
  		} // if

      $properties['password_hashed_with'] = 'pbkdf2';
  		
  		$properties['created_on'] = date(DATETIME_MYSQL);
  		if(!isset($properties['created_by_id'])) {
  			$properties['created_by_id'] = 1;
  		} // if
  		
  		return $this->createObject('users', $properties);
  	} // addUser
    
  }