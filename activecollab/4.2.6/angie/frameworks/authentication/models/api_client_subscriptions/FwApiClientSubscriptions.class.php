<?php

  /**
   * Framework level API client subscriptions
   * 
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwApiClientSubscriptions extends BaseApiClientSubscriptions {
    
    /**
     * Subscription error codes
     */
    const ERROR_OPERATION_FAILED = 0;
    const ERROR_CLIENT_NOT_SET = 1;
    const ERROR_USER_DOES_NOT_EXIST = 2;
    const ERROR_INVALID_PASSWORD = 3;
    const ERROR_NOT_ALLOWED = 4;
    
    /**
     * Returns true if $user can create an API subscription
     * 
     * @param User $user
     * @return boolean
     */
    static function canSubscribe(User $user) {
      return $user->isApiUser();
    } // canSubscribe
    
    /**
     * Issue a key to a given user for given application
     * 
     * @param string $email
     * @param string $password
     * @param string $client_name
     * @param string $client_vendor
     * @param bool $read_only
     * @return string
     * @throws ApiClientSubscriptionError
     */
    static function subscribe($email, $password, $client_name, $client_vendor, $read_only = false) {
      if(empty($client_name) || empty($client_vendor)) {
        throw new ApiClientSubscriptionError(self::ERROR_CLIENT_NOT_SET);
      } // if
      
      $user = Users::findByEmail($email, true);
      if($user instanceof User && $user->getState() >= STATE_VISIBLE) {
        if($user->isCurrentPassword($password)) {
          if(ApiClientSubscriptions::canSubscribe($user)) {
            try {
              $subscriptions_table = TABLE_PREFIX . 'api_client_subscriptions';
            
              $token = DB::executeFirstCell("SELECT token FROM $subscriptions_table WHERE user_id = ? AND client_name = ?", $user->getId(), $client_name);
              if($token) {
                return $user->getId() . '-' . $token;
              } else {
                do {
                  $token = make_string(40);
                } while(DB::executeFirstCell("SELECT COUNT(id) FROM $subscriptions_table WHERE token = ?", $token) > 0);
                
                DB::execute("INSERT INTO $subscriptions_table (user_id, token, client_name, client_vendor, created_on, is_enabled, is_read_only) VALUES (?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)", $user->getId(), $token, $client_name, $client_vendor, true, $read_only);
                
                return $user->getId() . '-' . $token;
              } // if
            } catch(Exception $e) {
              throw new ApiClientSubscriptionError(self::ERROR_OPERATION_FAILED);
            } // try
          } else {
            throw new ApiClientSubscriptionError(self::ERROR_NOT_ALLOWED);
          } // if
        } else {
	        $user->securityLog()->log('failed', null, true);
          throw new ApiClientSubscriptionError(self::ERROR_INVALID_PASSWORD);
        } // if
      } else {
	      // @todo ovde ide anonymous login
        throw new ApiClientSubscriptionError(self::ERROR_USER_DOES_NOT_EXIST);
      } // if
    } // subscribe
  
    /**
     * Return client subscription by token
     * 
     * @param string $token
     * @return ApiClientSubscription
     */
    static function findByToken($token) {
      return ApiClientSubscriptions::find(array(
        'conditions' => array('token = ?', $token), 
        'one' => true, 
      ));
    } // findByToken
    
    /**
  	 * Return slice of API subscriptions based on given criteria
  	 * 
  	 * @param User $user
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSliceByUser(User $user, $num = 10, $exclude = null, $timestamp = null) {
  		$max_date = $timestamp ? new DateTimeValue($timestamp) : new DateTimeValue();
  		
  		if($exclude) {
  			return ApiClientSubscriptions::find(array(
  			  'conditions' => array('id NOT IN (?) AND user_id = ? AND created_on <= ?', $exclude, $user->getId(), $max_date), 
  			  'order' => 'created_on DESC', 
  			  'limit' => $num,  
  			));
  		} else {
  			return ApiClientSubscriptions::find(array( 
  				'conditions' => array('user_id = ? AND created_on <= ?', $user->getId(), $max_date),
  			  'order' => 'created_on DESC', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSliceByUser
    
    /**
     * Return number of API subscriptions by user
     * 
     * @param User $user
     * @return integer
     */
    static function countByUser(User $user) {
      return ApiClientSubscriptions::count(array('user_id = ?', $user->getId()));
    } // countByUser
    
    /**
     * Generate token
     * 
     * @return string
     */
    static function generateToken() {
      do {
        $token = make_string(40);
      } while(DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'api_client_subscriptions WHERE token = ?', $token) > 0);
      
      return $token;
    } // generateToken
    
  }