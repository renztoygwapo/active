<?php

  /**
   * Object's subscriptions helper implementation
   *
   * @package angie.frameworks.subscriptions
   * @subpackage models
   */
  class ISubscriptionsImplementation {
    
    /**
     * Parent object instance
     *
     * @var ISubscriptions
     */
    protected $object;
    
    /**
     * Construct object's subscriptions helper
     *
     * @param ISubscriptions $object
     */
    function __construct(ISubscriptions $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Cached array of subscribers
     *
     * @var array
     */
    private $subscribers = false;
    
    /**
     * Return array of subscribed users
     *
     * @return array
     */
    function get() {
      if($this->subscribers === false) {
        $subscriptions_table = TABLE_PREFIX . 'subscriptions';

        $subscribers = Users::findFlattenFromUserListingTable($subscriptions_table, 'user', DB::prepare("$subscriptions_table.parent_type = ? AND $subscriptions_table.parent_id = ?", get_class($this->object), $this->object->getId()), STATE_TRASHED);

        if($subscribers) {
          $this->subscribers = array();

          // Clean up trashed users. If we don't do it like this, trashed users would be added as anonymous users
          foreach($subscribers as $subscriber) {
            if($subscriber instanceof IState && $subscriber->getState() <= STATE_ARCHIVED) {
              continue;
            } // if

            $this->subscribers[] = $subscriber;
          } // foreach
        } else {
          $this->subscribers = null;
        } // if
      } // if
      
      return $this->subscribers;
    } // getSubscribers

    /**
     * Return subscription code for the given user
     *
     * @param IUser $user
     * @return string
     */
    function getSubscriptionCodeFor(IUser $user) {
      $parent_class = get_class($this->object);
      $parent_id = $this->object->getId();

      return AngieApplication::cache()->getByObject($this->object, array('subscription_codes', $user->getEmail()), function() use ($parent_class, $parent_id, $user) {
        $row = DB::executeFirstRow("SELECT id, code FROM " . TABLE_PREFIX . "subscriptions WHERE parent_type = ? AND parent_id = ? AND user_email = ?", $parent_class, $parent_id, $user->getEmail());

        if($row) {
          return 'SUBS-' . $row['id'] . '-' . $row['code'];
        } else {
          return null;
        } // if
      });
    } // getSubscriptionCodeFor

    /**
     * Subscription codes cache
     *
     * @var array
     */
    private $subscription_codes_by_email = false;

    /**
     * Get subscription codes mapped by email
     *
     * @return array
     */
    function getSubscriptionCodesByEmail() {
      if ($this->subscription_codes_by_email === false) {
        $this->subscription_codes_by_email = array();
        $raw_subscriptions = DB::execute("SELECT id, user_email, code FROM " . TABLE_PREFIX . "subscriptions WHERE parent_type = ? AND parent_id = ?", get_class($this->object), $this->object->getId());
        if (is_foreachable($raw_subscriptions)) {
          foreach ($raw_subscriptions as $raw_subscription) {
            if ($raw_subscription['id'] && $raw_subscription['code']) {
              $this->subscription_codes_by_email[$raw_subscription['user_email']] = $raw_subscription['id'] . '-' . $raw_subscription['code'];
            } // f
          } // foreach
        } // if
      } // if

      return $this->subscription_codes_by_email;
    } // getSubscriptionCodesByEmail
    
    /**
     * Array of user ID-s subscribed to parent object
     *
     * @var array
     */
    protected $subscriber_ids = false;
    
    /**
     * Return ID-s of subscribers
     * 
     * @return array
     */
    function getIds() {
      if($this->subscriber_ids === false) {
        
        // Load list of subscriber ID-s
        if($this->subscribers === false) {
          $users_table = TABLE_PREFIX . 'users';
          $subscriptions_table = TABLE_PREFIX . 'subscriptions';
          
          $rows = DB::execute("SELECT $users_table.id FROM $users_table, $subscriptions_table WHERE $subscriptions_table.parent_type = ? AND $subscriptions_table.parent_id = ? AND $users_table.id = $subscriptions_table.user_id", get_class($this->object), $this->object->getId());
          
          if($rows) {
            $this->subscriber_ids = array();
            
            foreach($rows as $row) {
              $this->subscriber_ids[] = (integer) $row['id'];
            } // foreach
          } // if
          
        // Get from list of already loaded subscribers
        } else {
          if($this->subscribers) {
            foreach($this->subscribers as $subscriber) {
              $this->subscriber_ids = array();
              
              if($subscriber instanceof User) {
                $this->subscriber_ids = $subscriber->getId();
              } // if
            } // foreach
          } // if
        } // if
        
        // No ID-s loaded? Set as NULL so don't go over all of this again
        if(empty($this->subscriber_ids)) {
          $this->subscriber_ids = null;
        } // if
      } // if
      
      return $this->subscriber_ids;
    } // getIds
    
    /**
     * Cached subscriber names
     *
     * @var array
     */
    private $subscriber_names = false;
    
    /**
     * Return names of the people subscribed to the parent object
     * 
     * @return array
     */
    function getNames() {
      if($this->subscriber_names === false) {
        
        // No subscribers loaded? Query the database
        if($this->subscribers === false) {
          $object_class = get_class($this->object);
          $object_id = $this->object->getId();
          
          $users_table = TABLE_PREFIX . 'users';
          $subscriptions_table = TABLE_PREFIX . 'subscriptions';
          
          $loaded_user_ids = array();
          $this->subscriber_names = array();
          
          // First load users
          $users = DB::execute("SELECT $users_table.id, $users_table.first_name, $users_table.last_name, $users_table.email FROM $users_table, $subscriptions_table WHERE $subscriptions_table.parent_type = ? AND $subscriptions_table.parent_id = ? AND $users_table.id = $subscriptions_table.user_id", $object_class, $object_id);
          if(is_foreachable($users)) {
            foreach($users as $user) {
              $loaded_user_ids[] = $user['id'];
              
              $this->subscriber_names[] = Users::getUserDisplayName(array(
                'first_name' => $user['first_name'], 
                'last_name' => $user['last_name'], 
                'email' => $user['email']
              ), true);
            } // foreach
          } // if
          
          // Now, anonymous users
          if(is_foreachable($loaded_user_ids)) {
            $rows = DB::execute("SELECT user_name, user_email FROM $subscriptions_table WHERE parent_type = ? AND parent_id = ? AND user_id NOT IN (?)", $object_class, $object_id, $loaded_user_ids);
          } else {
            $rows = DB::execute("SELECT user_name, user_email FROM $subscriptions_table WHERE parent_type = ? AND parent_id = ?", $object_class, $object_id);
          } // if
          
          if(is_foreachable($rows)) {
            foreach($rows as $row) {
              $this->subscriber_names[] = $row['user_name'] ? $row['user_name'] : $row['user_email'];
            } // foreach
          } // if
          
          if(empty($this->subscriber_names)) {
            $this->subscriber_names = null;
          } // if
          
        // We already have loaded subscribers, no need to query the database
        } else {
          if($this->subscribers) {
            $this->subscriber_names = array();
            foreach($this->subscribers as $subscriber) {
              $this->subscriber_names[] = $subscriber->getDisplayName(true);
            } // foreach
          } else {
            $this->subscriber_names = null;
          } // if
        } // if
        
      } // if
      
      return $this->subscriber_names;
    } // getNames

    /**
     * Set array of subscribers
     *
     * @param array $users
     * @param boolean $replace
     * @throws Exception
     */
    function set($users, $replace = true) {
      try {
        DB::beginWork('Setting object subscribers @ ' . __CLASS__);
        
        $parent_id = (integer) $this->object->getId();
        $subscriptions_table = TABLE_PREFIX . 'subscriptions';
        
        $already_subscribed = array();
        if($replace) {
          Subscriptions::deleteByParent($this->object); // cleanup
        } else {
          $already_subscribed = DB::executeFirstColumn('SELECT user_email FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());;
          if (is_foreachable($already_subscribed)) {
            foreach ($already_subscribed as $key=>$data) {
              $already_subscribed[$key] = strtolower($data);
            } //foreach
          } //if
        } // if
      
        $to_subscribe = array();
        if(is_foreachable($users)) {
          $load_user_details = array();
          
          foreach($users as $user) {
            
            // We have user instance
            if($user instanceof User) {
              $user_id = $user->getId();
              $user_email = $user->getEmail();
              
              if(!isset($to_subscribe[$user_email])) {
                AngieApplication::cache()->removeByObject($user, 'subscriptions');
                $to_subscribe[$user_email] = array($user_id, $user->getDisplayName(), $user_email);
              } // if
              
            // Anonymous user
            } elseif($user instanceof AnonymousUser) {
              $user_email = $user->getEmail();
              
              if(!isset($to_subscribe[$user_email])) {
                $to_subscribe[$user_email] = array(0, $user->getName(), $user_email);
              } // if

            // email address
            } else if ($user && is_valid_email($user)) {
              if(!isset($to_subscribe[$user])) {
                $to_subscribe[$user] = array(0, $user, $user);
              } // if

            // skip non set users
            } else if (!$user) {
              continue;

            // User ID? Load it later, with a single query
            } else {
              $user_id = (integer) $user;
              if($user_id) {
                $load_user_details[] = $user_id;
              } // if
            } // if

          } // foreach
          
          if(is_foreachable($load_user_details)) {
            $rows = DB::execute('SELECT id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users WHERE id IN (?)', $load_user_details);
            if(is_foreachable($rows)) {
              foreach($rows as $row) {
                $user_email = $row['email'];
                
                if(!isset($to_subscribe[$user_email]) && !(in_array(strtolower($user_email), $already_subscribed))) {
                  $to_subscribe[$user_email] = array(
                    $row['id'], 
                    trim($row['first_name']) && trim($row['last_name']) ? trim($row['first_name']) . ' ' . trim($row['last_name']) : $user_email, 
                    $user_email
                  );
                } // if
              } // foreach
            } // if
          } // if

          // additional filtering to make sure that email address is not already subscribed
          if (!$replace && is_foreachable($already_subscribed) && is_foreachable($to_subscribe)) {
            $unfiltered = $to_subscribe;
            $to_subscribe = array();
            foreach ($unfiltered as $email_address => $subscriber_data) {
              if (!in_array($email_address, $already_subscribed)) {
                $to_subscribe[$email_address] = $subscriber_data;
              } // if
            } // foreach
          } // if
          
          // Insert subscriptions
          if(is_foreachable($to_subscribe)) {
            $parent_type = get_class($this->object);
            
            $records = array();
            foreach($to_subscribe as $record) {
              $records[] = DB::prepare('(?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?)', $record[0], $record[1], $record[2], $parent_type, $parent_id, $this->getSubscriptionCode());
            } // if
            
            DB::execute("INSERT INTO $subscriptions_table (user_id, user_name, user_email, parent_type, parent_id, subscribed_on, code) VALUES " . implode(', ', $records));
          } // if
        } // if
        
        DB::commit('Object subscribers set @ ' . __CLASS__);
        
        // Clean cached values
        $this->subscribers = false;
        $this->subscriber_names = false;

        AngieApplication::cache()->removeByModel('users');
      } catch(Exception $e) {
        DB::rollback('Failed to set object subscribers @ ' . __CLASS__);
        throw $e;
      } // try
    } // set
    
    /**
     * Clone this object's subscriptions to a different object
     *
     * @param ISubscriptions $to
     * @param bool $check_users
     */
    function cloneTo(ISubscriptions $to, $check_users = false) {
      $rows = DB::execute('SELECT user_id, user_name, user_email FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
      
      if($rows) {
        $to_insert = array();
        
        $parent_type = DB::escape(get_class($to));
        $parent_id = DB::escape($to->getId());

        $existing_user_ids = array();
        if ($check_users) {
          $existing_user_ids = $to->getProject()->users()->getIds();
        } // if

        $evaluate_permissions = is_foreachable($existing_user_ids);

        foreach($rows as $row) {
          if ($check_users && $evaluate_permissions && (integer) $row['user_id'] > 0) {
            if (!in_array($row['user_id'], $existing_user_ids)) { // not in target project
              continue;
            } else {
              $subscriber = Users::findById($row['user_id']);
              if (!$subscriber instanceof User || $subscriber->getState() < STATE_VISIBLE) { // user doesn't exist any more
                continue;
              } else {
                $model = Inflector::pluralize(get_class($to));
                if (class_exists($model) && method_exists($model, 'canAccess') && !$model::canAccess($subscriber, $to->getProject())) { // no permission
                  continue;
                } // if
              } // if
            } // if
          } // if

          $to_insert[] = DB::prepare("($parent_type, $parent_id, ?, ?, ?, UTC_TIMESTAMP(), ?)", $row['user_id'], $row['user_name'], $row['user_email'], $this->getSubscriptionCode());

          AngieApplication::cache()->removeByObject(array('users', $row['user_id']), 'subscriptions');
        } // foreach

        if(count($to_insert)) {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . 'subscriptions (parent_type, parent_id, user_id, user_name, user_email, subscribed_on, code) VALUES ' . implode(', ', $to_insert));
        } // if
      } // if
    } // cloneTo
    
    /**
     * Returns true if this object has people subscribed to it
     *
     * @return boolean
     */
    function hasSubscribers() {
      return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
    } // hasSubscribers
    
    /**
     * Subscribe $user to this object
     *
     * @param IUser $user
     */
    function subscribe(IUser $user) {
      if(!$this->isSubscribed($user, false)) {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'subscriptions (parent_type, parent_id, user_id, user_name, user_email, subscribed_on, code) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?)', get_class($this->object), $this->object->getId(), $user->getId(), $user->getDisplayName(), $user->getEmail(), $this->getSubscriptionCode());
        
        $this->subscribers = false;
        $this->subscriber_names = false;
        
        if($user instanceof User) {
          AngieApplication::cache()->removeByObject($user, 'subscriptions');
        } // if

        AngieApplication::cache()->removeByObject($this->object);
      } // if
    } // subscribe
    
    /**
     * Unsubscribe $user from this object
     *
     * @param IUser $user
     */
    function unsubscribe(IUser $user) {
      if($this->isSubscribed($user, false)) {
        if($user instanceof User) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ? AND user_id = ?', get_class($this->object), $this->object->getId(), $user->getId());
          AngieApplication::cache()->removeByObject($user, 'subscriptions');
        } elseif($user instanceof AnonymousUser) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ? AND user_id = ? AND user_email = ?', get_class($this->object), $this->object->getId(), 0, $user->getEmail());
        } // if

        AngieApplication::cache()->removeByObject($this->object);
        
        $this->subscribers = false;
        $this->subscriber_names = false;
      } // if
    } // unsubscribe

    /**
     * Unsubscribe all users
     */
    function unsubscribeAllUsers() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
      AngieApplication::cache()->removeByModel('Users');

      $this->subscribers = false;
      $this->subscriber_names = false;
    } // unsubscribeAllUsers
    
    /**
     * Check if $user is subscribed to this object
     *
     * @param IUser $user
     * @param boolean $use_cache
     * @return boolean
     * @throws InvalidParamError
     */
    function isSubscribed(IUser $user, $use_cache = true) {
      $object_class = get_class($this->object);
      $object_id = $this->object->getId();
      
      $subscriptions_table = TABLE_PREFIX . 'subscriptions';
      
      if($user instanceof User) {
        if($use_cache) {
          $cached_value = AngieApplication::cache()->getByObject($user, 'subscriptions', function() use ($user, $subscriptions_table) {
            $result = array();

            $rows = DB::execute("SELECT parent_type, parent_id FROM $subscriptions_table WHERE user_id = ? OR user_email = ?", $user->getId(), $user->getEmail());
            if($rows) {
              foreach($rows as $row) {
                if(isset($result[$row['parent_type']]) && is_array($result[$row['parent_type']])) {
                  $result[$row['parent_type']][] = (integer) $row['parent_id'];
                } else {
                  $result[$row['parent_type']] = array(
                    (integer) $row['parent_id']
                  );
                } // if
              } // foreach
            } // if

            return $result;
          });
          
          return isset($cached_value[$object_class]) && in_array($object_id, $cached_value[$object_class]);
        } else {
          return (boolean) DB::executeFirstCell("SELECT COUNT(*) FROM $subscriptions_table WHERE parent_type = ? AND parent_id = ? AND (user_id = ? OR user_email = ?)", $object_class, $object_id, $user->getId(), $user->getEmail());
        } // if
      
      } elseif($user instanceof AnonymousUser) {
        return (boolean) DB::executeFirstCell("SELECT COUNT(*) FROM $subscriptions_table WHERE parent_type = ? AND parent_id = ? AND user_email = ?", $object_class, $object_id, $user->getEmail());
      } else {
        throw new InvalidParamError('user', $user, '$user is expected to be instance of User or AnonymousUser class', true);
      } // if
    } // isSubscribed
    
    /**
     * Return array of available users
     *
     * @param User $user
     * @return array
     */
    function getAvailableUsers(User $user) {
      return Users::find();
    } // getAvailableUsers
    
    /**
     * Return available users for select box
     *
     * @param User $user
     * @param mixed $exclude_ids
     * @param integer $min_state
     * @return array
     */
    function getAvailableUsersForSelect(User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      return Users::getForSelect($user, $exclude_ids, $min_state);
    } // getAvailableUsersForSelect

    /**
     * Return subscription code
     *
     * @return string
     */
    protected function getSubscriptionCode() {
      return strtoupper(make_string(10));
    } // getSubscriptionCode
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see the list of subscribers for this object
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $this->object->canView($user);
    } // canView
    
    /**
     * Check if $user can manage subscription list for this particular object
     *
     * @param User $user
     */
    function canManage(User $user) {
      return $this->object->canEdit($user);
    } // canManage
    
    /**
     * Returns true if $user can subscribe to this object
     *
     * @param User $user
     * @return boolean
     */
    function canSubscribe(User $user) {
      if($this->object->getState() < STATE_VISIBLE) {
        return false;
      } // if
      
      return $this->canView($user);
    } // canSubscribe
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Manage subscriptions URL
     *
     * @return string
     */
    function getSubscriptionsUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_subscriptions', $this->object->getRoutingContextParams());
    } // getSubscriptionsUrl

    /**
     * Manage subscriptions URL
     *
     * @return string
     */
    function getUnsubscribeAllUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_unsubscribe_all', $this->object->getRoutingContextParams());
    } // getSubscriptionsUrl
    
    /**
     * Return subscribe to object URL
     *
     * @param IUser $user
     * @return string
     */
    function getSubscribeUrl(IUser $user) {
      $params = $this->object->getRoutingContextParams();
      if(empty($params)) {
        $params = array();
      } // if
      
      if($user instanceof User) {
        $params['user_id'] = $user->getId();
      } elseif($user instanceof AnonymousUser) {
        $params['user_id'] = 0;
        $params['user_name'] = $user->getName();
        $params['user_email'] = $user->getEmail();
      } // if
      
      return Router::assemble($this->object->getRoutingContext() . '_subscribe', $params);
    } // getSubscribeUrl
    
    /**
     * Return unsubscribe URL
     *
     * @param IUser $user
     * @return string
     */
    function getUnsubscribeUrl(IUser $user) {
      $params = $this->object->getRoutingContextParams();
      if(empty($params)) {
        $params = array();
      } // if
      
      if($user instanceof User) {
        $params['user_id'] = $user->getId();
      } elseif($user instanceof AnonymousUser) {
        $params['user_id'] = 0;
        $params['user_name'] = $user->getName();
        $params['user_email'] = $user->getEmail();
      } // if
      
      return Router::assemble($this->object->getRoutingContext() . '_unsubscribe', $params);
    } // getUnsubscribeUrl
    
    // ---------------------------------------------------
    //  Event names
    // ---------------------------------------------------

    /**
     * Describe subscription of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['user_is_subscribed'] = $this->isSubscribed($user);
      
      if ($detailed) {
        $result['subscribers'] = null;

        if($this->get()) {
          $result['subscribers'] = array();

          foreach($this->get() as $subscriber) {
            $result['subscribers'][] = array(
              'id' => $subscriber->getId(),
              'display_name' => $subscriber->getDisplayName(),
              'short_display_name' => $subscriber->getDisplayName(true),
              'permalink' => $subscriber->getViewUrl(),
            );
          } // foreach
        } // if
      } // if

      $result['urls']['subscriptions'] = $this->getSubscriptionsUrl();
      $result['urls']['subscribe'] = $this->getSubscribeUrl($user);
      $result['urls']['unsubscribe'] = $this->getUnsubscribeUrl($user);      
    } // describe

    /**
     * Describe subscription of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['user_is_subscribed'] = $this->isSubscribed($user);

      if ($detailed) {
        $result['subscribers'] = null;

        if($this->get()) {
          $result['subscribers'] = array();

          foreach($this->get() as $subscriber) {
            $result['subscribers'][] = array(
              'id' => $subscriber->getId(),
              'display_name' => $subscriber->getDisplayName(),
              'short_display_name' => $subscriber->getDisplayName(true),
              'permalink' => $subscriber->getViewUrl(),
            );
          } // foreach
        } // if

        $result['urls']['subscriptions'] = $this->getSubscriptionsUrl();
        $result['urls']['subscribe'] = $this->getSubscribeUrl($user);
        $result['urls']['unsubscribe'] = $this->getUnsubscribeUrl($user);
      } // if
    } // describeForApi

  }