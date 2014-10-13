<?php

  /**
   * Authentication level users manager
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwUsers extends BaseUsers {
    
    /**
     * Returns true if $user can create a new user account
     *
     * @param IUser $user
     * @param mixed $context
     * @return boolean
     */
    static function canAdd(IUser $user, $context = null) {
      return $user instanceof User && $user->isAdministrator();
    } // canAdd

    // ---------------------------------------------------
    //  Roles, Permissions, Instances
    // ---------------------------------------------------

    /**
     * Return available user classes
     *
     * @return array
     */
    static function getAvailableUserClasses() {
      return array('Administrator', 'Member');
    } // getAvailableUserClasses

    /**
     * Return array of available user instances
     *
     * @return User[]
     */
    static function getAvailableUserInstances() {
      return array(new Administrator(), new Member());
    } // getAvailableUserInstances

    /**
     * Return user instance
     *
     * Use $of_class to specify user class, if needed. When omitted, default user instance will be created
     *
     * @param string $of_class
     * @param boolean $validate
     * @return User
     * @throws InvalidParamError
     */
    static function getUserInstance($of_class = null, $validate = false) {
      if(empty($of_class)) {
        $of_class = static::getDefaultUserClass();
      } // if

      if($validate && !Users::isAvailableUserClass($of_class)) {
        throw new InvalidParamError('of_class', $of_class, "'$of_class' is not a valid user class");
      } // if

      return new $of_class;
    } // getUserInstance

    /**
     * Returns true if $class is available user class
     *
     * @param string $class
     * @return bool
     */
    static function isAvailableUserClass($class) {
      return in_array($class, Users::getAvailableUserClasses());
    } // isAvailableUserClass

    /**
     * Return default user class
     *
     * @return string
     */
    static function getDefaultUserClass() {
      return 'Member';
    } // getDefaultUserClass

    /**
     * Update user type, and return reloaded user instance
     *
     * @param User $user
     * @param string $new_class
     * @param User $by
     * @return User
     * @throws InvalidParamError
     * @throws LastAdministratorRoleChangeError
     * @throws InvalidInstanceError
     */
    static function changeUserType(User $user, $new_class, User $by) {
      if(Users::isAvailableUserClass($new_class)) {
        if($new_class == 'Administrator' && !$by->isAdministrator()) {
          throw new InvalidInstanceError('by', $by, 'Administrator');
        } // if

        if(get_class($user) != $new_class) {
          if($user instanceof Administrator && Users::isLastAdministrator($user)) {
            throw new LastAdministratorRoleChangeError($user);
          } // if

          AngieApplication::cache()->removeByObject($user);

          DB::transact(function() use($user, $new_class, $by) {
            $from_class = $user->getType();

            DB::execute('UPDATE ' . TABLE_PREFIX . 'users SET type = ? WHERE id = ?', $new_class, $user->getId());

            $user = DataObjectPool::get('User', $user->getId(), null, true);;

            EventsManager::trigger('on_user_type_changed', array(&$user, $from_class, $new_class, $by));
          }, 'Chating user role');

          return DataObjectPool::get('User', $user->getId(), null, true);
        } // if

        return $user;
      } else {
        throw new InvalidParamError('new_class', $new_class, "'$new_class' is not a valid user class");
      } // if
    } // changeUserType

    /**
     * Return number of user
     *
     * @param integer $min_state
     * @return array
     */
    static function countByRoles($min_state = STATE_ARCHIVED) {
      $result = array();

      $rows = DB::execute("SELECT COUNT(id) AS 'row_count', type FROM " . TABLE_PREFIX . 'users WHERE state >= ? GROUP BY type', $min_state);

      if($rows) {
        foreach($rows as $row) {
          $result[$row['type']] = (integer) $row['row_count'];
        } // foreach
      } // if

      return $result;
    } // countByRoles

    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------

    /**
     * Create administrator account
     *
     * @param string $email
     * @param string $password
     * @param array $additional
     * @return Administrator
     */
    static function addAdministrator($email, $password, $additional = null) {
      $data = array(
        'email'  => $email,
        'password'  => $password,
        'state' => STATE_VISIBLE,
      );

      if($additional && is_foreachable($additional)) {
        foreach($additional as $k => $v) {
          if(!array_key_exists($k, $data)) {
            $data[$k] = $v;
          } // if
        } // foreach
      } // if

      $new_user = Users::getUserInstance('Administrator');
      $new_user->setAttributes($data);
      $new_user->save();

      return $new_user;
    } // addAdministrator

    /**
     * Return user data prepared for select box
     *
     * @param User $user
     * @param array $exclude_ids
     * @param integer $min_state
     * @return array
     */
    static function getForSelect(User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      $exclude_ids = $exclude_ids ? (array) $exclude_ids : array();

      if(count($exclude_ids)) {
        $visible_user_ids = array_diff($user->getVisibleUserIds(null, $min_state), $exclude_ids);
      } else {
        $visible_user_ids = $user->getVisibleUserIds(null, $min_state);
      } // if

      if(count($visible_user_ids)) {
        return Users::getForSelectByConditions(array('id IN (?) AND state >= ?', $visible_user_ids, $min_state));
      } else {
        return null;
      } // if
    } // getForSelect

    /**
     * Return users grouped for select box by conditions
     *
     * $additional_table is used in case we need to have a join
     *
     * @param mixed $conditions
     * @param string $additional_table
     * @return array
     */
    static function getForSelectByConditions($conditions, $additional_table = null) {
      $user_table = TABLE_PREFIX . 'users';

      $tables = $additional_table ? "$user_table, $additional_table" : $user_table;
      $conditions = DB::prepareConditions($conditions);

      if($conditions) {
        $users = DB::execute("SELECT $user_table.id, $user_table.first_name, $user_table.last_name, $user_table.email FROM $tables WHERE $conditions ORDER BY CONCAT($user_table.first_name, $user_table.last_name, $user_table.email)");
      } else {
        $users = DB::execute("SELECT $user_table.id, $user_table.first_name, $user_table.last_name, $user_table.email FROM $tables ORDER BY CONCAT($user_table.first_name, $user_table.last_name, $user_table.email)");
      } // if

      if($users) {
        $users->setCasting(array(
          'id' => DBResult::CAST_INT,
        ));

        return Users::sortUsersForSelect($users);
      } else {
        return null;
      } // if
    } // getForSelectByConditions

    /**
     * Sort users for select
     *
     * @param User[] $users
     * @return array
     */
    static function sortUsersForSelect($users) {
      if(is_foreachable($users)) {
        $individuals = array();

        foreach($users as $user) {
          $user_id = $user instanceof User ? $user->getId() : $user['id'];
          $display_name = $user instanceof User ? $user->getDisplayName() : Users::getUserDisplayName($user);

          $individuals[$user_id] = $display_name;
        } // foreach

        // Join elements together (owner company first, than other companies and finally individuals)
        $result = array();

        if(count($individuals)) {
          $result[lang('Individuals')] = $individuals;
        } // if

        return $result;
      } else {
        return null;
      } // if
    } // sortUsersForSelect

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Cached permissions
     *
     * @var NamedList
     */
    static private $permissions = false;

    /**
     * Return list of system permission with their details
     *
     * @return NamedList
     */
    static function getPermissions() {
      if(self::$permissions === false) {
        self::$permissions = new NamedList(array(

        ));

        EventsManager::trigger('on_system_permissions', array(&self::$permissions));
      } // if

      return self::$permissions;
    } // getPermissions

    /**
     * Return defaults of a single permission
     *
     * @param string $name
     * @return array
     */
    static function getPermission($name) {
      return static::getPermissions()->get($name);
    } // getPermission

    /**
     * Return names of all system permissions
     *
     * @return array
     */
    static function getPermissionNames() {
      return static::getPermissions()->keys();
    } // getPermissionNames
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return ID-s of user accounts $user can see
     *
     * @param IUser $user
     * @return array
     */
    static function findVisibleUserIds(IUser $user) {
      if($user instanceof User) {
        return DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE id != ?', $user->getId());
      } else {
        return null;
      } // if
    } // findVisibleUserIds
    
    /**
     * Return user display name by user ID
     * 
     * @param integer $id
     * @param boolean $short
     * @return string
     */
    static function getUserDisplayNameById($id, $short = false) {
      $user = DB::executeFirstRow('SELECT first_name, last_name, email FROM ' . TABLE_PREFIX . 'users WHERE id = ?', $id);
      
      if($user) {
        return Users::getUserDisplayName($user, $short);
      } else {
        return null;
      } // if
    } // getUserDisplayNameById
    
    /**
     * Return display name of user based on given parameters
     * 
     * @param array $params
     * @param boolean $short
     * @return string
     */
    static function getUserDisplayName($params, $short = false) {
      $full_name = isset($params['full_name']) && $params['full_name'] ? $params['full_name'] : null;
      $first_name = isset($params['first_name']) && $params['first_name'] ? $params['first_name'] : null;
      $last_name = isset($params['last_name']) && $params['last_name'] ? $params['last_name'] : null;
      $email = isset($params['email']) && $params['email'] ? $params['email'] : null;
      
      if ($short) {
        if($full_name) {
          $parts = explode(' ', $full_name);
          
          if(count($params) > 1) {
            $first_name = array_shift($parts);
            $last_name = implode(' ', $parts);
          } else {
            $first_name = $full_name;
          } // if
        } // if
        
        if($first_name && $last_name) {
          return $first_name . ' ' . substr_utf($last_name, 0, 1) . '.';
        } elseif($first_name) {
          return $first_name;
        } elseif($last_name) {
          return $last_name;
        } else {
          return substr($email, 0, strpos($email, '@'));
        } // if
      } else {
        if($full_name) {
          return $full_name;
        } elseif($first_name && $last_name) {
          return $first_name . ' ' . $last_name;
        } elseif($first_name) {
          return $first_name;
        } elseif($last_name) {
          return $last_name;
        } else {
          return substr($email, 0, strpos($email, '@'));
        } // if
      } // if
    } // getUserDisplayName
    
    /**
     * Return user ID name map
     *
     * @param array $ids
     * @param boolean $short
     * @return array
     */
    static function getIdNameMap($ids = null, $short = false) {
      if($ids) {
        $rows = DB::execute("SELECT id, first_name, last_name, email FROM " . TABLE_PREFIX . "users WHERE id IN (?) ORDER BY CONCAT(first_name, last_name, email)", $ids);
      } else {
        $rows = DB::execute("SELECT id, first_name, last_name, email FROM " . TABLE_PREFIX . "users ORDER BY CONCAT(first_name, last_name, email)");
      } // if
      
      if($rows) {
        $result = array();
        
        foreach($rows as $row) {
          $result[(integer) $row['id']] = Users::getUserDisplayName($row, $short);
        } // foreach
        
        return $result;
      } else {
        return array();
      } // if
    } // getIdNameMap
    
    /**
     * Return ID Details map
     * 
     * @param array $ids
     * @param array $fields
     * @param int $min_state
     * @param bool $permalink
     * @param bool|integer $avatar
     * @return array
     */
    static function getIdDetailsMap($ids = null, $fields, $min_state = STATE_ARCHIVED, $permalink = false, $avatar = false) {
      $fields = (array) $fields;

      if(!in_array('id', $fields)) {
        $fields[] = 'id';
      } // if

      if($permalink && !in_array('type', $fields)) {
        $fields[] = 'type';
      } // if
      
      if($ids) {
        $rows = DB::execute("SELECT " . implode(', ', $fields) . " FROM " . TABLE_PREFIX . "users WHERE id IN (?) AND state >= ? ORDER BY id", $ids, $min_state);
      } else {
        $rows = DB::execute("SELECT " . implode(', ', $fields) . " FROM " . TABLE_PREFIX . "users WHERE state >= ? ORDER BY id", $min_state);
      } // if

      if($rows) {
        $result = array();
        foreach ($rows as $row) {
          $single_result = array();

          foreach ($fields as $field) {
            $single_result[$field] = $row[$field];
          } // foreach

          if($permalink) {
            $single_result['permalink'] = static::getPermalinkFromUserRow($row);
          } // if

          if($avatar) {
            $single_result['avatar'] = static::getAvatarFromUserrow($row, $avatar);
          } // if

          $result[(integer) $row['id']] = $single_result;
        } // foreach

        return $result;
      } else {
        return null;
      } // if
    } // getIdDetailsMap

    /**
     * Cached permalink pattern
     *
     * @var bool
     */
    static private $permalink_pattern = false;

    /**
     * Return permalink from user row
     *
     * @param array $row
     * @return string
     */
    static protected function getPermalinkFromUserRow($row) {
      if(self::$permalink_pattern === false) {
        self::$permalink_pattern = Router::assemble('user', array('user_id' => '--USER-ID--'));
      } // if

      return str_replace('--USER-ID--', $row['id'], self::$permalink_pattern);
    } // permalinkFromUserRow

    /**
     * Return permalink from user row
     *
     * @param array $row
     * @param integer $size
     * @return string
     */
    static protected function getAvatarFromUserRow($row, $size = IUserAvatarImplementation::SIZE_BIG) {
      $custom_avatar_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . "/avatars/{$row['id']}.{$size}x{$size}.png";

      if(is_file($custom_avatar_path)) {
        return ROOT_URL . "/avatars/{$row['id']}.{$size}x{$size}.png?timestamp=" . filemtime($custom_avatar_path);
      } else {
        return static::getDefaultAvatarUrl($row['type'], $size);
      } // if
    } // getAvatarsFromUserRow

    /**
     * Return default avatar URL
     *
     * @param string $type
     * @param integer $size
     * @return string
     */
    static protected function getDefaultAvatarUrl($type, $size = IUserAvatarImplementation::SIZE_BIG) {
      return AngieApplication::getImageUrl("user-roles/member.{$size}x{$size}.png", AUTHENTICATION_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT);
    } // getDefaultAvatarUrl
    
    /**
     * Get user by session ID
     *
     * @param string $session_id
     * @param string $session_key
     * @return User
     */
    static function findBySessionId($session_id, $session_key) {
      $users_table = TABLE_PREFIX . 'users';
      $user_sessions_table = TABLE_PREFIX . 'user_sessions';
      
      return Users::findOneBySQL("SELECT $users_table.* FROM $users_table, $user_sessions_table WHERE $users_table.id = $user_sessions_table.user_id AND $user_sessions_table.id = ? AND $user_sessions_table.session_key = ?", $session_id, $session_key);
    } // findBySessionId

    /**
     * Return users by user type
     *
     * @param array $types
     * @param mixed $additional_conditions
     * @param int $min_state
     * @return User[]
     */
    static function findByType($types, $additional_conditions = null, $min_state = STATE_VISIBLE) {
      $conditions = DB::prepare('type IN (?) AND state >= ?', $types, $min_state);

      if($additional_conditions) {
        $conditions = '(' . $conditions . ' AND (' . DB::prepareConditions($additional_conditions) . '))';
      } // if

      return Users::find(array(
        'conditions' => $conditions
      ));
    } // findByType

    /**
     * Find user ID-s by given type filter
     *
     * @param string $type
     * @param int $min_state
     * @param User|integer|array|null $exclude
     * @param Closure $filter
     * @return array
     * @throws InvalidInstanceError
     */
    static function findIdsByType($type, $min_state = STATE_VISIBLE, $exclude = null, $filter = null) {
      if($filter && !($filter instanceof Closure)) {
        throw new InvalidInstanceError('filter', $filter, 'Closure');
      } // if

      if($filter instanceof Closure) {
        $fields = array('id', 'type', 'state', 'raw_additional_properties');
      } else {
        $fields = array('id');
      } // if

      $conditions = array(DB::prepare('(state >= ?)', $min_state));

      if($type) {
        $conditions[] = DB::prepare('(type IN (?))', (array) $type);
      } // if

      if($exclude instanceof User) {
        $exclude_ids = array($exclude->getId());
      } elseif(is_array($exclude)) {
        $exclude_ids = $exclude;
      } elseif($exclude) {
        $exclude_ids = (array) $exclude;
      } else {
        $exclude_ids = null;
      } // if

      if($exclude_ids) {
        $conditions[] = DB::prepare('(id NOT IN (?))', $exclude_ids);
      } // if

      $rows = DB::execute('SELECT ' . implode(', ', $fields) . ' FROM ' . TABLE_PREFIX . 'users WHERE ' . implode(' AND ', $conditions));
      if($rows) {
        $result = array();

        foreach($rows as $row) {
          $user_id = (integer) $row['id'];

          if($filter) {
            $additional_properties = $row['raw_additional_properties'] ? unserialize($row['raw_additional_properties']) : null;

            $custom_permissions = array();
            if ($additional_properties && isset($additional_properties['custom_permissions']) && !empty($additional_properties['custom_permissions'])) {
              $custom_permissions =  $additional_properties['custom_permissions'];
            } // if

            if($filter($user_id, $row['type'], $custom_permissions, $row['state'])) {
              $result[] = $user_id;
            }
          } else {
            $result[] = $user_id;
          } // if
        } // foreach

        return $result;
      } // if

      return null;
    } // findIdsByTypes
    
    /**
     * Prepare user for import
     *
     * @param array $vcard
     * @return array
     */
    static function prepareUserFromVCard($vcard) {
      $first_name = $last_name = $email = '';
      
      if(array_key_exists('N', $vcard)) {
        $first_name = trim($vcard['N'][0]['value'][1][0]);
        $last_name = trim($vcard['N'][0]['value'][0][0]);
      } // if

      $email = trim($vcard['EMAIL'][0]['value'][0][0]);

      $updated_on = null;
      if(array_key_exists('REV', $vcard)) {
        $updated_on = DateTimeValue::makeFromString($vcard['REV'][0]['value'][0][0]);
      } // if
      
      return array(
        'object_type' => 'User',
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'updated_on' => $updated_on
      );
    } // prepareUserFromVCard
    
    /**
     * Import user from vCard
     * 
     * @param array $user_data
     * @param array $imported_users
     * @return User
     * @throws Exception
     */
    static function fromVCard($user_data, &$imported_users) {
      $is_new = isset($user_data['is_new']) && $user_data['is_new'] == 'true'; // check whether it's a new user

      $password = null;

      // create an instance of User class appropriately
      if($is_new) {
        $user = Users::getUserInstance();
      } else {
        $user = Users::findByEmail(array_var($user_data, 'old_email'));
      } // if

      $user->setFirstName(array_var($user_data, 'first_name'));
      $user->setLastName(array_var($user_data, 'last_name'));
      $user->setEmail(array_var($user_data, 'email'));

      if($is_new) {
        $password = Authentication::getPasswordPolicy()->generatePassword();

        $user->setPassword($password);
        $user->setState(STATE_VISIBLE);

        if(array_var($user_data, 'updated_on')) {
          $user->setUpdatedOn(DateTimeValue::makeFromString(array_var($user_data, 'updated_on')));
          $user->setUpdatedBy(Authentication::getLoggedUser());
        } // if
      } // if

      // Collect imported users for updating object list
      $imported_users[] = array(
        'is_new' => $is_new,
        'user' => $user,
        'password' => $password
      );

      return $user;
    } // fromVCard
    
    /**
     * Return users who were online in the past $minutes minutes
     *
     * @param User $user
     * @param integer $minutes
     * @return array
     */
    static function findWhoIsOnline($user, $minutes = 15) {
      $visible_user_ids = Users::findVisibleUserIds($user);
      if(is_foreachable($visible_user_ids)) {
        $users_table = TABLE_PREFIX . 'users';
        $reference = new DateTimeValue("-$minutes minutes");
        
        return Users::findBySQL("SELECT * FROM $users_table WHERE id IN (?) AND last_activity_on > ? ORDER BY CONCAT(first_name, last_name, email)", $visible_user_ids, $reference);
      } // if
      return null;
    } // findWhoIsOnline
    
    /**
     * Return number of users who were online in the past $minutes minutes
     *
     * @param User $user
     * @param integer $minutes
     * @return array
     */
    static function countWhoIsOnline($user, $minutes = 15) {
      $visible_user_ids = Users::findVisibleUserIds($user);
      if(is_foreachable($visible_user_ids)) {
        $users_table = TABLE_PREFIX . 'users';
        $reference = new DateTimeValue("-$minutes minutes");
        
        return (integer) DB::executeFirstCell("SELECT COUNT(id) AS 'row_count' FROM $users_table WHERE id IN (?) AND last_activity_on > ?", $visible_user_ids, $reference);
      } // if
      return 0;
    } // countWhoIsOnline

    // ---------------------------------------------------
    //
    // ---------------------------------------------------

    /**
     * Return all administrators in system
     *
     * @param User $exclude_user
     * @return array
     */
    static function findAdministrators($exclude_user = null) {
      return Users::find(array(
        'conditions' => $exclude_user instanceof User ? DB::prepare("type = 'Administrator' AND id != ? AND state = ?", $exclude_user->getId(), STATE_VISIBLE) : DB::prepare("type = 'Administrator' AND state = ?", STATE_VISIBLE),
      ));
    } // findAdministrators

    /**
     * Return all administrators ids in system
     *
     * @param User $exclude_user
     * @return array
     */
    static function findAdministratorsIds($exclude_user = null) {
      $users_table = TABLE_PREFIX . 'users';
      if($exclude_user instanceof User) {
        return DB::executeFirstColumn("SELECT id FROM $users_table WHERE type = ? AND id != ? AND state = ?", 'Administrator', $exclude_user->getId(), STATE_VISIBLE);
      } else {
        return DB::executeFirstColumn("SELECT id FROM $users_table WHERE type = ? AND state = ?", 'Administrator', STATE_VISIBLE);
      } //if
    } // findAdministratorsIds

    /**
     * Return number of administrators
     *
     * @return integer
     */
    static function countAdministrators() {
      return Users::count("type = 'Administrator' AND state = '" . STATE_VISIBLE . "'");
    } // countAdministrators

    /**
     * Returns true if $user is last administrator in the system
     *
     * @param User $user
     * @return bool
     */
    static function isLastAdministrator(User $user) {
      return $user instanceof Administrator && Users::countAdministrators() == 1;
    } // isLastAdministrator

    // ---------------------------------------------------
    //  Related user listing tables
    // ---------------------------------------------------

    /**
     * Find and properly load users from user listing tables and return them as flattened array
     *
     * These tables as user listings associated with particular objects. For example, lists of subscribers, lists of
     * people who should be reminded or notified about something etc.
     *
     * @param string $external_table
     * @param string $field_prefix
     * @param string $filter
     * @param integer $min_state
     * @return array
     */
    static function findFlattenFromUserListingTable($external_table, $field_prefix, $filter, $min_state = STATE_ARCHIVED) {
      list($users, $anonymous_users) = Users::findFromUserListingTable($external_table, $field_prefix, $filter, $min_state);

      $result = array();

      if($users instanceof DBResult && $users->count()) {
        $result = $users->toArray();

        if(is_foreachable($anonymous_users)) {
          $result = array_merge($result, $anonymous_users);
        } // if
      } else {
        if(is_foreachable($anonymous_users)) {
          $result = $anonymous_users;
        } // if
      } // if

      return count($result) ? $result : null;
    } // findFlattenFromUserListingTable

    /**
     * Find and properly load users from user listing tables and return them separated by instance type
     *
     * These tables as user listings associated with particular objects. For example, lists of subscribers, lists of
     * people who should be reminded or notified about something etc.
     *
     * @param string $external_table
     * @param string $field_prefix
     * @param string $filter
     * @param integer $min_state
     * @return array
     */
    static function findFromUserListingTable($external_table, $field_prefix, $filter, $min_state = STATE_ARCHIVED) {
      $loaded_users = Users::findOnlyUsersFromUserListingTable($external_table, $field_prefix, $filter, $min_state);

      $loaded_user_ids = array();
      if($loaded_users) {
        foreach($loaded_users as $loaded_user) {
          $loaded_user_ids[] = $loaded_user->getId();
        } // foreach
      } // if

      $anonymous_users = array();

      $user_name_field = "{$field_prefix}_name";
      $user_email_field = "{$field_prefix}_email";

      if(count($loaded_user_ids)) {
        $where_part = $filter ? DB::prepare("WHERE {$field_prefix}_id NOT IN (?) AND $filter", $loaded_user_ids) : '';
      } else {
        $where_part = $filter ? "WHERE $filter" : '';
      } // if

      $rows = DB::execute("SELECT DISTINCT $user_name_field, $user_email_field FROM $external_table $where_part ORDER BY $user_name_field, $user_email_field");
      if($rows) {
        foreach($rows as $row) {
          if($row[$user_email_field]) {
            $anonymous_users[$row[$user_email_field]] = new AnonymousUser($row[$user_name_field], $row[$user_email_field]);
          } // if
        } // foreach
      } // if

      return array($loaded_users, $anonymous_users);
    } // findFromUserListingTable

    /**
     * Return only users from users listing table
     *
     * User listing tables as user listings associated with particular objects. For example, lists of subscribers, lists
     * of people who should be reminded or notified about something etc.
     *
     * @param string $external_table
     * @param string $field_prefix
     * @param string $filter
     * @param integer $min_state
     * @return User[]
     */
    static function findOnlyUsersFromUserListingTable($external_table, $field_prefix, $filter, $min_state = STATE_ARCHIVED) {
      $users_table = TABLE_PREFIX . 'users';
      $user_id_field = "{$field_prefix}_id";

      $where_part = $filter ? " AND ({$filter})" : "";

      return Users::findBySQL("SELECT DISTINCT $users_table.* FROM $users_table JOIN $external_table ON $users_table.id = {$external_table}.{$user_id_field} WHERE $users_table.state >= ? $where_part ORDER BY CONCAT($users_table.first_name, $users_table.last_name, $users_table.email)", $min_state);
    } // findOnlyUsersFromUserListingTable

    // ---------------------------------------------------
    //  Utility Methods
    // ---------------------------------------------------
    
    /**
     * Return list of users who can see private object
     * 
     * @param array $ids
     * @return array
     */
    static function whoCanSeePrivate($ids = null) {
      if (is_foreachable($ids) && count($ids) > 0) {
        $user_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE type IN (?) AND id IN (?)', static::userClassesThatCanSeePrivate(), $ids);
      } else {
        $user_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE type IN (?)', static::userClassesThatCanSeePrivate());
      } // if

      if($user_ids instanceof DBResult) {
        $user_ids->setCasting(array(
          'id' => DBResult::CAST_INT,
        ));

        return $user_ids->toArray();
      } else {
        return $user_ids;
      } // if
    } // whoCanSeePrivate

    /**
     * Return list of user roles that can see private objects
     *
     * @return array
     */
    static function userClassesThatCanSeePrivate() {
      return array('Administrator', 'Member');
    } // userClassesThatCanSeePrivate

    /**
     * Update account when auto expiry is updated
     *
     * @param integer $value
     */
    static function autoExpiryUpdated($value) {
      if($value > 0) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'users SET password_expires_on = ? WHERE password_expires_on IS NULL', DateTimeValue::makeFromString("+$value months"));
      } else {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'users SET password_expires_on = NULL');
      } // if

      AngieApplication::cache()->removeByModel('users');
    } // autoExpirySet

    /**
     * Mass-mark user passwords as expired
     *
     * @param mixed $exclude
     */
    static function expirePasswords($exclude = null) {
      $yesterday = date("Y-m-d", time() - 24 * 60 * 60); // password expired yesterday

      if($exclude) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'users SET password_expires_on = ? WHERE id NOT IN (?)', $yesterday, $exclude);
      } else {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'users SET password_expires_on = ?', $yesterday);
      } // if

      AngieApplication::cache()->removeByModel('users');
    } // expirePasswords

    // ---------------------------------------------------
    //  Email Management
    // ---------------------------------------------------

    /**
     * Return user by email address
     *
     * @param string $email
     * @param boolean $extended
     * @return User
     * @throws InvalidParamError
     */
    static function findByEmail($email, $extended = false) {
      if($email && is_valid_email($email)) {
        if($extended) {
          $user = Users::find(array(
            'conditions' => array('email = ? AND state >= ?', $email, STATE_TRASHED),
            'one' => true,
          ));

          if($user instanceof User) {
            return $user;
          } // if

          $users_table = TABLE_PREFIX . 'users';
          $addresses_table = TABLE_PREFIX . 'user_addresses';

          return Users::findOneBySql("SELECT $users_table.* FROM $users_table, $addresses_table WHERE $users_table.id = $addresses_table.user_id AND $users_table.state >= ? AND $addresses_table.email = ?", STATE_TRASHED, $email);
        } else {
          return Users::find(array(
            'conditions' => array('email = ? AND state >= ?', $email, STATE_TRASHED),
            'one' => true,
          ));
        } // if
      } else {
        throw new InvalidParamError('email', $email, 'Invalid email address');
      } // if
    } // findByEmail

    /**
     * Returns true if $address is used by any trashed, archived or visible user
     *
     * @param string $address
     * @param mixed $exclude_user
     * @return boolean
     */
    static function isEmailAddressInUse($address, $exclude_user = null) {
      $exclude_user_id = $exclude_user instanceof User ? $exclude_user->getId() : (integer) $exclude_user;

      $users_table = TABLE_PREFIX . 'users';
      $addresses_table = TABLE_PREFIX . 'user_addresses';

      if($exclude_user_id) {
        $user_id = (integer) DB::executeFirstCell("SELECT id FROM $users_table WHERE id != ? AND email = ? AND state >= ?", $exclude_user_id, $address, STATE_TRASHED);
      } else {
        $user_id = (integer) DB::executeFirstCell("SELECT id FROM $users_table WHERE email = ? AND state >= ?", $address, STATE_TRASHED);
      } // if

      if(empty($user_id)) {
        if($exclude_user_id) {
          $user_id = (integer) DB::executeFirstCell("SELECT $users_table.id FROM $users_table, $addresses_table WHERE $users_table.id = $addresses_table.user_id AND $users_table.id != ? AND $users_table.state >= ? AND $addresses_table.email = ?", $exclude_user_id, STATE_TRASHED, $address);
        } else {
          $user_id = (integer) DB::executeFirstCell("SELECT $users_table.id FROM $users_table, $addresses_table WHERE $users_table.id = $addresses_table.user_id AND $users_table.state >= ? AND $addresses_table.email = ?", STATE_TRASHED, $address);
        } // if
      } // if

      return (boolean) $user_id;
    } // isEmailAddressInUse

  }