<?php

  /**
   * Framework level notification manager class implementation
   *
   * @package angie.frameworks.notifications
   * @subpackage models
   */
  abstract class FwNotifications extends BaseNotifications {

    // Display notifications
    const SHOW_NOTHING = 0;
    const SHOW_BADGE = 1;
    const SHOW_BADGE_AND_MESSAGE = 2;

    // Cache keys
    const SEEN_CACHE_KEY = 'notifications_seen';
    const READ_CACHE_KEY = 'notifications_read';

    /**
     * Returns true if $user has seen $notification
     *
     * @param Notification|integer $notification
     * @param User $user
     * @param boolean $use_cache
     * @param boolean $rebuild_stale_cache
     * @return bool
     * @throws InvalidInstanceError
     */
    static function isSeen($notification, User $user, $use_cache = true, $rebuild_stale_cache = true) {
      if($user instanceof User) {
        return self::isTimestampSet($notification, $user, 'seen_on', Notifications::SEEN_CACHE_KEY, $use_cache, $rebuild_stale_cache);
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if
    } // isSeen

    /**
     * Mark selected notification as seen
     *
     * @param Notification $notification
     * @param User $user
     */
    static function markSeen(Notification $notification, User $user) {
      if(!Notifications::isSeen($notification, $user)) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'notification_recipients SET seen_on = UTC_TIMESTAMP() WHERE notification_id = ? AND recipient_id = ? AND seen_on IS NULL', $notification->getId(), $user->getId());
        AngieApplication::cache()->removeByObject($notification);

        // Update seen cache if cache values exists (if not, system will rebuild it the first time it is needed)
        $cached_value = self::getSeenReadCache($user, Notifications::SEEN_CACHE_KEY);
        if(is_array($cached_value)) {
          $cached_value[$notification->getId()] = true;

          AngieApplication::cache()->setByObject($user, Notifications::SEEN_CACHE_KEY, $cached_value);
        } // if
      } // if
    } // markSeen

    /**
     * Returns true if $user has read context in which notification was published
     *
     * @param Notification|integer $notification
     * @param User $user
     * @param boolean $use_cache
     * @param boolean $rebuild_stale_cache
     * @return bool
     * @throws InvalidInstanceError
     */
    static function isRead($notification, User $user, $use_cache = true, $rebuild_stale_cache = true) {
      if($user instanceof User) {
        return self::isTimestampSet($notification, $user, 'read_on', Notifications::READ_CACHE_KEY, $use_cache, $rebuild_stale_cache);
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if
    } // isRead

    /**
     * Mark a single notification as read
     *
     * @param Notification $notification
     * @param User $user
     * @throws Exception
     */
    static function markRead(Notification $notification, User $user) {
      if(!Notifications::isRead($notification, $user, false, false)) {
        try {
          DB::beginWork('Marking notification as read @ ' . __CLASS__);

          Notifications::markSeen($notification, $user); // Just in case
          DB::execute('UPDATE ' . TABLE_PREFIX . 'notification_recipients SET read_on = UTC_TIMESTAMP() WHERE notification_id = ? AND recipient_id = ?', $notification->getId(), $user->getId());

          DB::commit('Notification has been marked as read @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to mark notification as read @ ' . __CLASS__);
          throw $e;
        } // try

        AngieApplication::cache()->removeByObject($notification);

        // Update read cache only if cache value exists (if not, system will rebuild it the first time it is needed)
        $cached_value = AngieApplication::cache()->getByObject($user, Notifications::READ_CACHE_KEY);

        if(is_array($cached_value)) {
          $cached_value[$notification->getId()] = true;

          AngieApplication::cache()->setByObject($user, Notifications::READ_CACHE_KEY, $cached_value);
        } // if
      } // if
    } // markRead

    /**
     * Mark all unread notifications for a given object as read
     *
     * @param ApplicationObject|array $parent
     * @param User $user
     * @throws Exception
     * @throws InvalidParamError
     */
    static function markReadByParent($parent, User $user) {
      if(is_array($parent) && isset($object[0]) && $object[1]) {
        list($parent_type, $parent_id) = $parent;
      } elseif($parent instanceof ApplicationObject) {
        $parent_type = get_class($parent);
        $parent_id = $parent->getId();
      } else {
        throw new InvalidParamError('parent', $parent, '$parent is expected to be an instance of ApplicationObject class of Class-ID pair');
      } // if

      $user_id = $user->getId();

      $notifications_table = TABLE_PREFIX . 'notifications';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      $notification_ids = DB::executeFirstColumn("SELECT $notifications_table.id AS 'id' FROM $notifications_table, $recipients_table WHERE $notifications_table.id = $recipients_table.notification_id AND $notifications_table.parent_type = ? AND $notifications_table.parent_id = ? AND $recipients_table.recipient_id = ? AND ($recipients_table.seen_on IS NULL or $recipients_table.read_on IS NULL)", $parent_type, $parent_id, $user_id);

      if($notification_ids) {
        try {
          $cached_seen_values = self::getSeenReadCache($user, Notifications::SEEN_CACHE_KEY);
          $cached_read_values = self::getSeenReadCache($user, Notifications::READ_CACHE_KEY);

          DB::beginWork('Marking parent notification as read @ ' . __CLASS__);

          foreach($notification_ids as $notification_id) {
            $notification_id = (integer) $notification_id;

            DB::execute("UPDATE $recipients_table SET seen_on = UTC_TIMESTAMP() WHERE notification_id = ? AND recipient_id = ? AND seen_on IS NULL", $notification_id, $user->getId());
            DB::execute("UPDATE $recipients_table SET read_on = UTC_TIMESTAMP() WHERE notification_id = ? AND recipient_id = ? AND read_on IS NULL", $notification_id, $user->getId());

            if($cached_seen_values && is_array($cached_seen_values)) {
              $cached_seen_values[$notification_id] = true;
            } // if

            if($cached_read_values && is_array($cached_read_values)) {
              $cached_read_values[$notification_id] = true;
            } // if
          } // foreach

          DB::commit('Parent notification has been marked as read @ ' . __CLASS__);

          if(is_array($cached_seen_values)) {
            AngieApplication::cache()->setByObject($user, Notifications::SEEN_CACHE_KEY, $cached_seen_values);
          } // if

          if(is_array($cached_read_values)) {
            AngieApplication::cache()->setByObject($user, Notifications::READ_CACHE_KEY, $cached_read_values);
          } // if
        } catch(Exception $e) {
          DB::rollback('Failed to mark parent notification as read @ ' . __CLASS__);
          throw $e;
        } // try
      } // if

      AngieApplication::cache()->removeByModel('notifications');
    } // markReadByParent

    /**
     * Mark a single notification as unread
     *
     * @param Notification $notification
     * @param User $user
     */
    static function markUnread(Notification $notification, User $user) {
      if(Notifications::isRead($notification, $user, false, false)) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'notification_recipients SET read_on = NULL WHERE notification_id = ? AND recipient_id = ?', $notification->getId(), $user->getId());
        AngieApplication::cache()->removeByObject($notification);

        // Update read cache only if cache value exists (if not, system will rebuild it the first time it is needed)
        $cached_value = AngieApplication::cache()->getByObject($user, Notifications::READ_CACHE_KEY);

        if(is_array($cached_value)) {
          $cached_value[$notification->getId()] = false;

          AngieApplication::cache()->setByObject($user, Notifications::READ_CACHE_KEY, $cached_value);
        } // if
      } // if
    } // markUnread

    /**
     * Mass-change read status for given user
     *
     * @param User $user
     * @param $new_read_status
     * @param boolean $all_notifications
     * @param null $notification_ids
     * @throws InvalidParamError
     */
    static function updateReadStatusForRecipient(User $user, $new_read_status, $all_notifications = true, $notification_ids = null) {
      $new_read_on_value = $new_read_status ? 'UTC_TIMESTAMP()' : 'NULL';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      if($all_notifications) {
        DB::execute("UPDATE $recipients_table SET read_on = $new_read_on_value WHERE recipient_id = ?", $user->getId());

        if($new_read_status) {
          DB::execute("UPDATE $recipients_table SET seen_on = UTC_TIMESTAMP() WHERE recipient_id = ? AND seen_on IS NULL", $user->getId());
        } // if
      } else {
        if($notification_ids) {
          DB::execute("UPDATE $recipients_table SET read_on = $new_read_on_value WHERE notification_id IN (?) AND recipient_id = ?", $notification_ids, $user->getId());

          if($new_read_status) {
            DB::execute("UPDATE $recipients_table SET seen_on = UTC_TIMESTAMP() WHERE notification_id IN (?) AND recipient_id = ? AND seen_on IS NULL", $notification_ids, $user->getId());
          } // if
        } else {
          throw new InvalidParamError('notification_ids', $notification_ids, 'Missing notification ID-s');
        } // if
      } // if

      AngieApplication::cache()->removeByObject($user, Notifications::SEEN_CACHE_KEY);
      AngieApplication::cache()->removeByObject($user, Notifications::READ_CACHE_KEY);
    } // updateReadStatusForRecipient

    /**
     * Mass change seen status for given user
     */
    static function updateSeenStatusForRecipient(User $user, $new_seen_status, $all_notifications = true, $notification_ids = null) {
      $new_seen_on_value = $new_seen_status ? 'UTC_TIMESTAMP()' : 'NULL';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      if($all_notifications) {
          DB::execute("UPDATE $recipients_table SET seen_on = $new_seen_on_value WHERE recipient_id = ? AND seen_on IS NULL", $user->getId());
      } else {
        if($notification_ids) {
          DB::execute("UPDATE $recipients_table SET seen_on = $new_seen_on_value WHERE notification_id IN (?) AND recipient_id = ? AND seen_on IS NULL", $notification_ids, $user->getId());
        } else {
          throw new InvalidParamError('notification_ids', $notification_ids, 'Missing notification ID-s');
        } // if
      } // if

      AngieApplication::cache()->removeByObject($user, Notifications::SEEN_CACHE_KEY);
      AngieApplication::cache()->removeByObject($user, Notifications::READ_CACHE_KEY);
    } // updateSeenStatusForRecipient

    /**
     * Clear all notifications for a given recipient
     *
     * @param User $user
     * @param boolean $all_notifications
     * @param array|integer $notification_ids
     * @throws InvalidParamError
     */
    static function clearForRecipient(User $user, $all_notifications = true, $notification_ids = null) {
      if($all_notifications) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE recipient_id = ?', $user->getId());
      } else {
        if($notification_ids) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE notification_id IN (?) AND recipient_id = ?', $notification_ids, $user->getId());
        } else {
          throw new InvalidParamError('notification_ids', $notification_ids, 'Missing notification ID-s');
        } // if
      } // if

      AngieApplication::cache()->removeByObject($user, Notifications::SEEN_CACHE_KEY);
      AngieApplication::cache()->removeByObject($user, Notifications::READ_CACHE_KEY);
    } // clearForRecipient

    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------

    /**
     * Returns true if $field_name is set to a non-null value for a given recipient and a given notification
     *
     * This method is cache aware and it will maintain or rebuild cache if needed, based on provided parameters
     *
     * @param Notification|integer $notification
     * @param User $user
     * @param string $field_name
     * @param string $cache_key
     * @param bool $use_cache
     * @param bool $rebuild_stale_cache
     * @return bool
     */
    static private function isTimestampSet($notification, User $user, $field_name, $cache_key, $use_cache = true, $rebuild_stale_cache = true) {
      $recipients_table = TABLE_PREFIX . 'notification_recipients';
      $notification_id = $notification instanceof Notification ? $notification->getId() : $notification;

      if(empty($use_cache) && empty($rebuild_stale_cache)) {
        return (boolean) DB::executeFirstCell("SELECT COUNT(*) FROM $recipients_table WHERE notification_id = ? AND recipient_id = ? AND $field_name IS NOT NULL", $notification_id, $user->getId());
      } // if

      $cached_values = self::getSeenReadCache($user, $cache_key);

      return isset($cached_values[$notification_id]) && $cached_values[$notification_id];
    } // isTimestampSet


    /**
     * Get Seen Read Cache
     *
     * @param User $user
     * @param String $cache_key
     */
    static private function getSeenReadCache($user, $cache_key) {
      return AngieApplication::cache()->getByObject($user, $cache_key, function() use ($user, $cache_key) {
        $recipients_table = TABLE_PREFIX . 'notification_recipients';
        $field_name = $cache_key == Notifications::READ_CACHE_KEY ? 'read_on' : 'seen_on';

        $result = array();

        $rows = DB::execute("SELECT notification_id, $field_name FROM $recipients_table WHERE recipient_id = ?", $user->getId());
        if($rows) {
          foreach($rows as $row) {
            $result[(integer) $row['notification_id']] = (boolean) $row[$field_name];
          } // foreach
        } // if

        return $result;
      });
    } // getSeenReadCache

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Return recent notifications send to $user
     *
     * @param User $user
     * @param DateTimeValue|null $since
     * @return Notification[]
     */
    static function findRecentByUser(User $user, $since = null) {
      $notifications_table = TABLE_PREFIX . 'notifications';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      if($since instanceof DateTimeValue) {
        return Notifications::findBySQL("SELECT DISTINCT $notifications_table.* FROM $notifications_table JOIN $recipients_table ON $notifications_table.id = $recipients_table.notification_id WHERE $notifications_table.created_on > ? AND $recipients_table.recipient_id = ? ORDER BY $notifications_table.created_on DESC LIMIT 0, 100", $since, $user->getId());
      } else {
        return Notifications::findBySQL("SELECT DISTINCT $notifications_table.* FROM $notifications_table JOIN $recipients_table ON $notifications_table.id = $recipients_table.notification_id WHERE $recipients_table.recipient_id = ? ORDER BY $notifications_table.created_on DESC LIMIT 0, 100", $user->getId());
      } // if
    } // findRecentByUser

    /**
     * Return the number of unseen messages
     *
     * @param User $user
     * @return integer
     */
    static function countUnseenByUser(User $user) {
      $notifications_table = TABLE_PREFIX . 'notifications';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      return (integer) DB::executeFirstCell("SELECT COUNT($notifications_table.id) FROM $notifications_table, $recipients_table WHERE $notifications_table.id = $recipients_table.notification_id AND $recipients_table.recipient_id = ? AND $recipients_table.seen_on IS NULL", array($user->getId()));
    } // countUnseenByUser

    /**
     * Return unseen notifications by a given user
     *
     * @param User $user
     * @param integer $limit
     * @return Notification[]
     */
    static function findUnseenByUser(User $user, $limit = null) {
      $notifications_table = TABLE_PREFIX . 'notifications';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      $limit = (integer) $limit;

      if($limit < 1) {
        $limit = 100;
      } // if

      return Notifications::findBySQL("SELECT DISTINCT $notifications_table.* FROM $notifications_table JOIN $recipients_table ON $notifications_table.id = $recipients_table.notification_id WHERE $recipients_table.recipient_id = ? AND seen_on IS NULL ORDER BY $notifications_table.created_on DESC LIMIT 0, " . $limit, $user->getId());
    } // findUnseenByUser

    /**
     * Delete notifications by module
     *
     * @param AngieModule $module
     * @throws Exception
     */
    static function deleteByModule(AngieModule $module) {
      $notification_classes = array();
      $object_classes = $module->getObjectTypes();

      $files = get_files($module->getPath() . '/notifications', 'php');
      if($files) {
        foreach($files as $file) {
          $file_name = basename($file);

          if(str_ends_with($file_name, '.class.php')) {
            $notification_classes[] = str_replace('.class.php', '', $file_name);
          } // if
        } // foreach
      } // if

      if(count($notification_classes) && count($object_classes)) {
        $notification_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'notifications WHERE type IN (?) OR parent_type IN (?)', $notification_classes, $object_classes);
      } elseif(count($notification_classes)) {
        $notification_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'notifications WHERE type IN (?)', $notification_classes);
      } elseif(count($object_classes)) {
        $notification_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'notifications WHERE parent_type IN (?)', $object_classes);
      } else {
        $notification_ids = null;
      } // if

      if(count($notification_ids)) {
        try {
          DB::beginWork('Removing notifications by module @ ' . __CLASS__);

          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE notification_id IN (?)', $notification_ids);
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notifications WHERE id IN (?)', $notification_ids);

          DB::commit('Notifications removed by module @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to remove notifications by module @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByModule

    /**
     * Clean up old notifications
     */
    static function cleanUp() {
      $ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'notifications WHERE created_on < ?', DateValue::makeFromString('-30 days'));

      if($ids) {
        DB::transact(function() use ($ids) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE notification_id IN (?)', $ids);
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notifications WHERE id IN (?)', $ids);
        }, 'Cleaning up old notifications');
      } // if
    } // cleanUp

  }