<?php

  /**
   * Framework level activity logs manager
   *
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  abstract class FwActivityLogs extends BaseActivityLogs {
    
    /**
     * Log activity
     * 
     * @param IActivityLogs $subject
     * @param string $action
     * @param IUser $by
     * @param mixed $target
     * @param string $comment
     * @param string $sub_context
     * @return ActivityLog
     * @throws InvalidInstanceError
     */
    static function log(IActivityLogs $subject, $action, IUser $by, $target = null, $comment = null, $sub_context = null) {
      if($subject instanceof IActivityLogs && $subject instanceof IObjectContext) {
        $log = new ActivityLog();

        // Make sure that we can remember sub-context, in case of versions, sub-tasks etc
        $context = empty($sub_context) ? $subject->getObjectContextDomain() . ':' . $subject->getObjectContextPath() : with_slash($subject->getObjectContextDomain() . ':' . $subject->getObjectContextPath()) . $sub_context;
        
        $log->setSubject($subject);
        $log->setSubjectContext($context);
        $log->setAction($action);
        $log->setCreatedBy($by);
        
        if($target instanceof Applicationobject) {
          $log->setTarget($target);
        } // if
        
        if($comment) {
          $log->setComment($comment);
        } // if
        
        $log->save();
        
        return $log;
      } else {
        throw new InvalidInstanceError('subject', $subject, 'IObjectContext');
      } // if
    } // log

    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------

    /**
     * Reutrn callbacks that will render activity logs, RSS feeds etc
     *
     * @return array
     */
    static function getCallbacks() {
      $callbacks = array(
        '*/created' => new ParentCreatedActivityLogCallback(),
        '*/completed' => new ParentCompletedActivityLogCallback(),
        '*/reopened' => new ParentReopenedActivityLogCallback(),
        '*/moved_to_archive' => new ParentMovedToArchiveActivityLogCallback(),
        '*/moved_to_trash' => new ParentMovedToTrashActivityLogCallback(),
        '*/restored_from_archive' => new ParentRestoredFromArchiveActivityLogCallback(),
        '*/restored_from_trash' => new ParentRestoredFromTrashActivityLogCallback(),
      );

      EventsManager::trigger('on_activity_log_callbacks', array(&$callbacks));

      return $callbacks;
    } // getCallbacks

    /**
     * Load related data from activities
     *
     * @param ActivityLog[] $activity_logs
     * @param IUser $user
     * @param Closure $populate_authors_callback
     * @return array
     */
    static function loadRelatedDataFromActivities($activity_logs, IUser $user, $populate_authors_callback = null) {
      $authors = $subjects = $targets = null;

      if($activity_logs) {
        $authors = $subjects = $targets = array();
        $language = $user->getLanguage();

        foreach($activity_logs as $activity_log) {
          if($activity_log['created_by_id'] && !in_array($activity_log['created_by_id'], $authors)) {
            $authors[] = (integer) $activity_log['created_by_id'];
          } // if

          if(isset($subjects[$activity_log['subject_type']])) {
            $subjects[$activity_log['subject_type']][] = (integer) $activity_log['subject_id'];
          } else {
            $subjects[$activity_log['subject_type']] = array((integer) $activity_log['subject_id']);
          } // if

          if($activity_log['target_type'] && $activity_log['target_id']) {
            if(isset($targets[$activity_log['target_type']])) {
              $targets[$activity_log['target_type']][] = (integer) $activity_log['target_id'];
            } else {
              $targets[$activity_log['target_type']] = array((integer) $activity_log['target_id']);
            } // if
          } // if
        } // foreach

        if($populate_authors_callback instanceof Closure) {
          $authors = $populate_authors_callback($authors);
        } // if

        // get the full subjects and describe them for activity log
        $full_subjects = DataObjectPool::getByTypeIdsMap($subjects);
        $subjects = array();
        if (is_foreachable($full_subjects)) {
          foreach ($full_subjects as $subject_type => $subjects_by_type) {
            if (is_foreachable($subjects_by_type)) {
              $subjects[$subject_type] = array();
              foreach ($subjects_by_type as $full_subject_id => $full_subject) {
                if($full_subject instanceof IActivityLogs) {
                  $subjects[$subject_type][$full_subject_id] = $full_subject->activityLogs()->describeForLog($user);
                } else {
                  $subjects[$subject_type][$full_subject_id] = array(
                    'name' => $full_subject->getName(),
                    'verbose_type' => $full_subject->getVerboseType(false, $language),
                    'verbose_type_lowercase' => $full_subject->getVerboseType(true, $language),
                    'urls' => array(
                      'view' => $full_subject->getViewUrl()
                    ),
                    'permalink' => $full_subject->getViewUrl()
                  );
                } // if
              } // foreach
            } // if
          } // foreach
        } // if
        unset($full_subjects);

        $full_targets = DataObjectPool::getByTypeIdsMap($targets);
        $targets = array();
        if (is_foreachable($full_targets)) {
          foreach ($full_targets as $target_type => $targets_by_type) {
            if (is_foreachable($targets_by_type)) {
              $targets[$target_type] = array();
              foreach ($targets_by_type as $full_target_id => $full_target) {
                if($full_target instanceof IActivityLogs) {
                  $targets[$target_type][$full_target_id] = $full_target->activityLogs()->describeForLog($user);
                } else {
                  $targets[$target_type][$full_target_id] = array(
                    'name' => $full_target->getName(),
                    'verbose_type' => $full_target->getVerboseType(false, $language),
                    'verbose_type_lowercase' => $full_target->getVerboseType(true, $language),
                    'urls' => array(
                      'view' => $full_target->getViewUrl()
                    ),
                    'permalink' => $full_target->getViewUrl()
                  );
                } // if
              } // foreach
            } // if
          } // foreach
        } // if
      } // if
      unset($full_targets);

      return array($authors, $subjects, $targets);
    } // loadRelatedDataFromActivities

    /**
     * Populate feed with given activities
     *
     * @param Feed $feed
     * @param IUser $user
     * @param DBResult $activity_logs
     */
    static function populateFeedWithActivities(Feed &$feed, IUser &$user, DBResult $activity_logs) {
      if($activity_logs) {
        $callbacks = ActivityLogs::getCallbacks();

        list($authors, $subjects, $targets) = ActivityLogs::loadRelatedDataFromActivities($activity_logs, $user);

        foreach($activity_logs as $activity_log) {
          $action = $activity_log['action'];
          $any_action = '*' . substr($action, strpos($action, '/'));

          if(isset($callbacks[$action])) {
            $callback = $callbacks[$action];
          } elseif(isset($callbacks[$any_action])) {
            $callback = $callbacks[$any_action];
          } else {
            $callback = null;
          } // if

          if($callback instanceof JavaScriptCallback) {
            $created_by_id = $activity_log['created_by_id'];

            $author = isset($authors[$created_by_id]) && $authors[$created_by_id] ? $authors[$created_by_id] : null;

            $subject_type = $activity_log['subject_type'];
            $subject_id = $activity_log['subject_id'];

            $subject = isset($subjects[$subject_type]) && isset($subjects[$subject_type][$subject_id]) && $subjects[$subject_type][$subject_id] ? $subjects[$subject_type][$subject_id] : null;

            if($author && $subject) {
              $target_type = $activity_log['target_type'];
              $target_id = $activity_log['target_id'];

              $target = isset($targets[$target_type]) && isset($targets[$target_type][$target_id]) && $targets[$target_type][$target_id] ? $targets[$target_type][$target_id] : null;

              $item = new FeedItem($callback->renderRssSubject($activity_log, $author, $subject, $target), $subject['urls']['view'], '', DateTimeValue::makeFromString($activity_log['created_on']));
              $item->setId(extend_url($subject['urls']['view'], array('guid' => $activity_log['id'])));

              $feed->addItem($item);
            } // if
          } // if
        } // foreach
      } // if
    } // populateFeedWithActivities

    /**
     * Populate activity logs API response from list of activities
     *
     * @param array $result
     * @param IUser $user
     * @param DBResult $activity_logs
     */
    static function populateApiResponseFromActivities(&$result, IUser &$user, DBResult $activity_logs) {
      if($activity_logs) {
        list($authors, $subjects, $targets) = ActivityLogs::loadRelatedDataFromActivities($activity_logs, $user);

        foreach($activity_logs as $activity_log) {
          $created_by_id = $activity_log['created_by_id'];

          $author = isset($authors[$created_by_id]) && $authors[$created_by_id] ? $authors[$created_by_id] : null;

          $subject_type = $activity_log['subject_type'];
          $subject_id = $activity_log['subject_id'];

          $subject = isset($subjects[$subject_type]) && isset($subjects[$subject_type][$subject_id]) && $subjects[$subject_type][$subject_id] ? $subjects[$subject_type][$subject_id] : null;

          if($author && $subject) {
            $target_type = $activity_log['target_type'];
            $target_id = $activity_log['target_id'];

            $target = isset($targets[$target_type]) && isset($targets[$target_type][$target_id]) && $targets[$target_type][$target_id] ? $targets[$target_type][$target_id] : null;

            $result[] = array(
              'id' =>$activity_log['id'],
              'action' => $activity_log['action'],
              'taken_on' => $activity_log['created_on'] instanceof DateTimeValue ? $activity_log['created_on']->toMySQL() : null,
              'taken_by' => array(
                'id' => $created_by_id,
                'first_name' => $author['first_name'],
                'last_name' => $author['last_name'],
                'email' => $author['email'],
                'display_name' => $author['display_name'],
                'url' => $author['urls']['view'],
              ),
              'subject' => array(
                'type' => $subject_type,
                'id' => $subject_id,
                'url' => $subject['urls']['view'],
              ),
              'target' => $target ? array(
                'type' => $target_type,
                'id' => $target_id,
                'url' => $target['urls']['view'],
              ) : null,
            );
          } // if
        } // foreach
      } // if
    } // populateApiResponseFromActivities
    
    /**
     * Update object context
     * 
     * @param IObjectContext $object
     * @param string $old_context
     * @param string $new_context
     * @throws Exception
     */
    static function updateObjectContext(IObjectContext &$object, $old_context, $new_context) {
      try {
        DB::beginWork('Failed to update object contexts @ ' . __CLASS__);
        
        $rows = DB::execute('SELECT id, subject_context FROM ' . TABLE_PREFIX . 'activity_logs WHERE subject_context LIKE ?', "$old_context%");
        if($rows) {
          foreach($rows as $row) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'activity_logs SET subject_context = ? WHERE id = ?', str_replace($old_context, $new_context, $row['subject_context']), $row['id']);
          } // foreach
        } // if
        
        DB::commit('Object contexts updated @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update object contexts @ ' . __CLASS__);
        throw $e;
      } // try
    } // updateObjectContext

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return recent log entires
     * 
     * @param IUser $user
     * @return DbResult|null
     */
    static function findRecent(IUser $user) {
      list($contexts, $ignore_contexts) = ApplicationObjects::getVisibileContexts($user);
      
      if($contexts) {
        $result = DB::execute('SELECT * FROM ' . TABLE_PREFIX . 'activity_logs WHERE ' . self::conditionsFromContexts($contexts, $ignore_contexts) . ' ORDER BY created_on DESC, id DESC LIMIT 0, 30');
      
        if($result instanceof DBResult) {
          $result->setCasting(array(
            'id' => DBResult::CAST_INT, 
            'subject_id' => DBResult::CAST_INT, 
            'target_id' => DBResult::CAST_INT, 
            'created_on' => DBResult::CAST_DATETIME, 
            'created_by_id' => DBResult::CAST_INT, 
          ));
        } // if
        
        return $result;
      } else {
        return null;
      } // if
    } // findRecent
    
    /**
     * Find recent entries that are logged for $by user
     * 
     * @param IUser $user
     * @param IUser $by
     * @return array
     */
    static function findRecentBy(IUser $user, IUser $by) {
      list($contexts, $ignore_contexts) = ApplicationObjects::getVisibileContexts($user);
      
      if($contexts) {
        $result = DB::execute('SELECT * FROM ' . TABLE_PREFIX . 'activity_logs WHERE created_by_id = ? AND (' . self::conditionsFromContexts($contexts, $ignore_contexts) . ') ORDER BY created_on DESC, id DESC LIMIT 0, 50', $by->getId());
      
        if($result instanceof DBResult) {
          $result->setCasting(array(
            'id' => DBResult::CAST_INT, 
            'subject_id' => DBResult::CAST_INT, 
            'target_id' => DBResult::CAST_INT, 
            'created_on' => DBResult::CAST_DATETIME, 
            'created_by_id' => DBResult::CAST_INT, 
          ));
        } // if
        
        return $result;
      } else {
        return null;
      } // if
    } // findRecentBy
    
    /**
     * Find recent entries in a given object (object defined contexts)
     * 
     * @param IUser $user
     * @param ApplicationObject $in
     * @return array
     */
    static function findRecentIn(IUser $user, ApplicationObject $in) {
      list($contexts, $ignore_contexts) = ApplicationObjects::getVisibileContexts($user, $in);
      
      if($contexts) {
        $result = DB::execute('SELECT * FROM ' . TABLE_PREFIX . 'activity_logs WHERE ' . self::conditionsFromContexts($contexts, $ignore_contexts) . ' ORDER BY created_on DESC, id DESC LIMIT 0, 50');
      
        if($result instanceof DBResult) {
          $result->setCasting(array(
            'id' => DBResult::CAST_INT, 
            'subject_id' => DBResult::CAST_INT, 
            'target_id' => DBResult::CAST_INT, 
            'created_on' => DBResult::CAST_DATETIME, 
            'created_by_id' => DBResult::CAST_INT, 
          ));
        } // if
        
        return $result;
      } else {
        return null;
      } // if
    } // findRecentIn
    
    /**
     * Prepare conditions based on available and ignored contexts
     * 
     * @param array $contexts
     * @param array $ignore_contexts
     * @return string
     */
    static protected function conditionsFromContexts($contexts, $ignore_contexts) {
      $conditions = array();
        
      foreach($contexts as $context) {
        $conditions[] = DB::prepare('subject_context LIKE ?', $context);
      } // foreach
      
      $conditions = '(' . implode(' OR ', $conditions) . ')';
      
      if(is_foreachable($ignore_contexts)) {
        $ignore_conditions = array();
        
        foreach($ignore_contexts as $context) {
          $ignore_conditions[] = DB::prepare('subject_context LIKE ?', $context);
        } // foreach
        
        $conditions .= ' AND NOT ('  . implode(' OR ', $ignore_conditions) . ')';
      } // if
      
      return $conditions;
    } // conditionsFromContexts
    
    /**
     * Return number of activity log entries by context
     * 
     * Result key is context, and value is number of log entires that belong to 
     * the given context
     * 
     * @return array
     */
    static function countGroupedByContext() {
      $result = array();
      
      $rows = DB::execute("SELECT SUBSTRING_INDEX(subject_context, ':', 1) AS 'context', COUNT(id) AS 'count' FROM " . TABLE_PREFIX . "activity_logs GROUP BY context");
      if($rows) {
        foreach($rows as $row) {
          $result[$row['context']] = (integer) $row['count'];
        } // foreach
      } // if
      
      return $result;
    } // countGroupedByContext
    
    /**
     * Calculate size of activity log index
     * 
     * @return integer
     */
    static function calculateSize() {
      $row = DB::executeFirstRow('SHOW TABLE STATUS LIKE ?', TABLE_PREFIX . 'activity_logs');
      
      if($row && isset($row['Data_length']) && isset($row['Index_length'])) {
        return $row['Data_length'] + $row['Index_length'];
      } else {
        return 0;
      } // if
    } // calculateSize
    
    /**
     * Return rebuild actions
     * 
     * @return array
     */
    static function getRebuildActions() {
      $actions = array(Router::assemble('activity_logs_admin_clean') => lang('Clean up existing log entries'));
      
      EventsManager::trigger('on_rebuild_activity_log_actions', array(&$actions));
      
      return $actions;
    } // getRebuildActions
    
    // ---------------------------------------------------
    //  Rebuilds
    // ---------------------------------------------------
    
    /**
     * Rebuild state change activity logs
     * 
     * @param string $type
     * @param integer $id
     * @param string $context
     * @param string $target_type
     * @param integer $target_id
     * @param string $action_prefix
     */
    static function rebuildStateChangeActivityLogs($type, $id, $context, $target_type, $target_id, $action_prefix) {
      $logs_table = TABLE_PREFIX . 'modification_logs';
      $values_table = TABLE_PREFIX . 'modification_log_values';
      
      $log_entries = DB::execute("SELECT $logs_table.id, $logs_table.parent_type, $logs_table.parent_id, $logs_table.created_on, $logs_table.created_by_id, $logs_table.created_by_name, $logs_table.created_by_email, $values_table.value FROM $logs_table, $values_table WHERE $logs_table.id = $values_table.modification_id AND $logs_table.parent_type = ? AND $logs_table.parent_id = ? AND field = 'state' ORDER BY $logs_table.created_on", $type, $id);
      
      if($log_entries) {
        $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
        
        $previous_state = STATE_VISIBLE; // Assume that all objects start as visible
        
        foreach($log_entries as $log_entry) {
          if($log_entry['value'] == STATE_DELETED || ($log_entry['value'] == STATE_VISIBLE && !($previous_state == STATE_ARCHIVED || $previous_state == STATE_TRASHED))) {
            continue;
          } // if
          
          switch($log_entry['value']) {
            case STATE_TRASHED:
              $action = "$action_prefix/moved_to_trash";
              break;
            case STATE_ARCHIVED:
              if($previous_state == STATE_TRASHED) {
                $action = "$action_prefix/restored_from_trash";
              } else {
                $action = "$action_prefix/moved_to_archive";
              } // if

              break;
            default:
              if($previous_state == STATE_TRASHED) {
                $action = "$action_prefix/restored_from_trash";
              } else {
                $action = "$action_prefix/restored_from_archive";
              } // if
          } // switch
          
          $batch->insert($type, $id, $context, $action, $target_type, $target_id, $log_entry['created_on'], $log_entry['created_by_id'], $log_entry['created_by_name'], $log_entry['created_by_email']);
          
          $previous_state = $log_entry['value'];
        } // foreach
        
        $batch->done();
      } // if
    } // rebuildStateChangeActivityLogs
    
    /**
     * Rebuild completion activity logs
     * 
     * @param string $type
     * @param integer $id
     * @param string $context
     * @param string $target_type
     * @param string $target_id
     * @param string $action
     */
    static function rebuildCompletionActivityLogs($type, $id, $context, $target_type, $target_id, $complete_action, $reopen_action) {
      $logs_table = TABLE_PREFIX . 'modification_logs';
      $values_table = TABLE_PREFIX . 'modification_log_values';
      
      $log_entries = DB::execute("SELECT $logs_table.id, $logs_table.parent_type, $logs_table.parent_id, $logs_table.created_on, $logs_table.created_by_id, $logs_table.created_by_name, $logs_table.created_by_email, $values_table.value FROM $logs_table, $values_table WHERE $logs_table.id = $values_table.modification_id AND $logs_table.parent_type = ? AND $logs_table.parent_id = ? AND field = 'completed_on' ORDER BY $logs_table.created_on", $type, $id);
      
      if($log_entries) {
        $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
        
        foreach($log_entries as $log_entry) {
          $batch->insert($type, $id, $context, ($log_entry['value'] ? $complete_action : $reopen_action), $target_type, $target_id, $log_entry['created_on'], $log_entry['created_by_id'], $log_entry['created_by_name'], $log_entry['created_by_email']);
        } // foreach
        
        $batch->done();
      } // if
    } // rebuildCompletionActivityLogs
    
    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------
    
    /**
     * Cached array of decorators, indexed by interface
     *
     * @var array
     */
    static private $decorators = array();
    
    /**
     * Return activity log decorator callback
     * 
     * @param string $interface
     * @return string
     */
    static function getDecorator($interface = AngieApplication::INTERFACE_DEFAULT) {
      if(!array_key_exists($interface, self::$decorators)) {
        self::$decorators[$interface] = '';
        EventsManager::trigger('on_activity_log_decorator', array(&self::$decorators[$interface], $interface));
        
        if(empty(self::$decorators[$interface])) {
          self::$decorators[$interface] = null;
        } // if
      } // if
      
      return self::$decorators[$interface];
    } // getDecorator
    
    // ---------------------------------------------------
    //  Legacy, refactor
    // ---------------------------------------------------
    
    /**
     * Delete activity logs by parent
     *
     * @param IActivityLogs $parent
     * @param array $additional
     * @return boolean
     */
    static function deleteByParent(IActivityLogs &$parent, $additional = null) {
      return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'activity_logs WHERE (subject_type = ? AND subject_id = ?) OR (target_type = ? AND target_id = ?)', get_class($parent), $parent->getId(), get_class($parent), $parent->getId());
    } // deleteByParent
    
    /**
     * Delete logged activitys by parent and additional property
     *
     * @param IActivityLogs $parent
     * @param string $property_name
     * @param mixed $property_value
     * @return boolean
     */
    static function deleteByParentAndAdditionalProperty(IActivityLogs $parent, $property_name, $property_value) {
      $rows = DB::execute('SELECT id, raw_additional_properties FROM ' . TABLE_PREFIX . 'activity_logs WHERE subject_type = ? AND subject_id = ?', get_class($parent), $parent->getId());
      if(is_foreachable($rows)) {
        $to_delete = array();
        
        foreach($rows as $row) {
          if($row['additional_properties']) {
            $properties = unserialize($row['additional_properties']);
            if(is_array($properties) && isset($properties[$property_name]) && $properties[$property_name] == $property_value) {
              $to_delete[] = (integer) $row['id'];
            } // if
          } // if
        } // foreach
        
        if(count($to_delete)) {
          return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'activity_logs WHERE id IN (?)', $to_delete);
        } // if
      } // if
      
      return true;
    } // deleteByParentAndAdditionalProperty
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     */
    static function deleteByParents($parents) {
      if(is_foreachable($parents)) {
        foreach($parents as $parent_type => $parent_ids) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'activity_logs WHERE (subject_type = ? AND subject_id IN (?)) OR (target_type IN (?) AND target_id IN (?))', $parent_type, $parent_ids, $parent_type, $parent_ids);
        } // foreach
      } // if
    } // deleteByParents
    
    /**
     * Delete by parent types
     * 
     * @param array $types
     */
    static function deleteByParentTypes($types) {
      if($types) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'activity_logs WHERE subject_type IN (?) OR target_type IN (?)', $types, $types);
      } // if
    } // deleteByParentTypes

    /**
     * Clean up activity log entries and reset auto-increment value
     */
    static function cleanUp() {
      DB::execute('TRUNCATE TABLE ' . TABLE_PREFIX . 'activity_logs');
    } // reset
    
  }