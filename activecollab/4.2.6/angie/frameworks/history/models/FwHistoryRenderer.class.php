<?php

  /**
   * Framework level history renderer implementation
   *
   * @package angie.frameworks.history
   * @subpackage models
   */
  abstract class FwHistoryRenderer {
    
    /**
     * Object instance
     *
     * @var IHistory
     */
    protected $object;

    /**
     * Cached user id-name map
     *
     * @var array
     */
    static protected $cached_users = array();
    
    /**
     * Construct history renderer
     *
     * @param IHistory $object
     */
    function __construct(IHistory $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Prepare and render all logged modification
     *
     * @param IUser $user
     * @param Smarty $smarty
     * @return array
     */
    function render(IUser $user, &$smarty) {
      $result = array();
      
      $logs = ModificationLogs::findByParent($this->object);
      
      if(is_foreachable($logs)) {
        require_once ENVIRONMENT_FRAMEWORK_PATH . '/helpers/function.action_on_by.php';
        
        $logs = $logs->toArray();
        
        $rows = DB::execute('SELECT * FROM ' . TABLE_PREFIX . 'modification_log_values WHERE modification_id IN (?)', objects_array_extract($logs, 'getId'));
        if(is_foreachable($rows)) {
          $modifications = array();
          $cached_user_id_values = array();
          
          // Loop through rows and get modification => field => value map
          for($i = 0, $count = $rows->count(); $i < $count; $i++) {
            $field = $rows[$i]['field'];

            if (in_array($field, array('leader_id', 'user_id', 'assignee_id'))) {
              $cached_user_id_values[] = $rows[$i]['value'];
            } // if

            // Loop through older rows and get old value, if present
            $old_value = null;
            for($j = $i-1; $j >= 0; $j--) {
              if($rows[$j]['field'] == $field) {
                $old_value = $rows[$j]['value'];
                break;
              } // if
            } // for
            // And now map the data that we collected
            $modification_id = (integer) $rows[$i]['modification_id'];
            
            if(isset($modifications[$modification_id])) {
              $modifications[$modification_id][$field] = array($rows[$i]['value'], $old_value);
            } else {
              $modifications[$modification_id] = array(
                $field => array($rows[$i]['value'], $old_value)
              );
            } // if
          } // for
        } // if

        // Cache modification log creators and users in log values table
        $cached_user_ids = array();

        if (is_array($cached_user_id_values)) {
          $cached_user_ids = array_merge($cached_user_ids, $cached_user_id_values); // merge values from modification_log_values
        } // if

        $modification_author_ids = array_unique(objects_array_extract($logs, "getFieldValue", "created_by_id"));
        if (is_array($modification_author_ids)) {
          $cached_user_ids = array_merge($cached_user_ids, $modification_author_ids); // merge values from modification_logs
        } // if

        if (is_foreachable($cached_user_ids)) {
          self::$cached_users = Users::getIdNameMap($cached_user_ids, true);
        } // if

        foreach($logs as $log) {
          AngieApplication::useHelper('ago', GLOBALIZATION_FRAMEWORK, 'modifier');

          $alternative_user = $log->getFieldValue('created_by_name');

          // Object is created
          if($log->getIsFirst()) {
            $result[] = array(
              'head' => lang("Created by <b>:created_by</b>", array("created_by" => self::getCachedUserDetails($log->getFieldValue('created_by_id'), $alternative_user)))." ".smarty_modifier_ago($log->getCreatedOn(), null, true),
              'modifications' => lang(':type Created', array('type' => $this->object->getVerboseType())),
            );

          // Object is modified
          } else {
            $result[] = array(
              'head' => lang("Updated by <b>:updated_by</b>", array("updated_by" => self::getCachedUserDetails($log->getFieldValue('created_by_id'), $alternative_user)))." ".smarty_modifier_ago($log->getCreatedOn(), null, true),
              'modifications' => isset($modifications[$log->getId()]) ? $this->renderModifications($user, $modifications[$log->getId()]) : null,
            );
          } // if

        } // foreach

      } // if
      
      return $result;
    } // render
    
    /**
     * Render all modifications and return them as array
     *
     * @param IUser $user
     * @param array $modifications
     * @return array
     */
    function renderModifications(IUser $user, $modifications) {
      $result = array();

      $field_renders = $this->getFieldRenderers();
      
      foreach($modifications as $field => $v) {
        list($new_value, $old_value) = $v;

        if(isset($field_renders[$field]) && $field_renders[$field] instanceof Closure) {
          $result[] = $field_renders[$field]->__invoke($old_value, $new_value);
        } else {
          if($new_value) {
            if($old_value) {
              $result[] = lang(':field changed from :old_value to :new_value', array('field' => $field, 'old_value' => $old_value, 'new_value' => $new_value));
            } else {
              $result[] = lang(':field set to :new_value', array('field' => $field, 'new_value' => $new_value));
            } // if
          } else {
            if($old_value) {
              $result[] = lang(':field set to empty value', array('field' => $field));
            } // if
          } // if
        } // if
      } // if
      
      return $result;
    } // renderModifications

    /**
     * Return field renderers
     *
     * @return array
     */
    protected function getFieldRenderers() {
      $result = array(
        'name' => function($old_value, $new_value) {
          return lang('Name changed from <b>:old_value</b> to <b>:new_value</b>', array(
            'old_value' => $old_value,
            'new_value' => $new_value,
          ));
        },

        'body' => function($old_value, $new_value) {
          if($new_value) {
            if ($old_value) {
              return lang('Description updated') . ' (<a href="'.Router::assemble('compare_text').'" class="text_diffs">' . lang('diff') . '</a><pre class="old" style="display: none;">'.$old_value.'</pre><pre class="new" style="display: none;">'.$new_value.'</pre>)';
            } else {
              return lang('Description added');
            } // if
          } else {
            if($old_value) {
              return lang('Description removed');
            } // if
          } // if
        },

        'state' => function($old_value, $new_value) {
          if($new_value == STATE_TRASHED) {
            return lang('Moved to trash');
          } elseif($new_value == STATE_ARCHIVED) {
            if($old_value == STATE_VISIBLE) {
              return lang('Moved to archive');
            } elseif($old_value == STATE_TRASHED) {
              return lang('Restored from trash');
            } // if
          } elseif($new_value == STATE_VISIBLE) {
            if($old_value == STATE_ARCHIVED) {
              return lang('Restored from archive');
            } elseif($old_value == STATE_TRASHED) {
              return lang('Restored from trash');
            } // if
          } // if
        },

        'is_locked' => function($old_value, $new_value) {
          if($new_value) {
            return lang('Comments locked');
          } else {
            return lang('Comments unlocked');
          } // if
        },

        'visibility' => function($old_value, $new_value) {
          $verbose_visibility = array(
            VISIBILITY_NORMAL   => lang('Normal'),
            VISIBILITY_PRIVATE  => lang('Private'),
            VISIBILITY_PUBLIC   => lang('Public')
          );

          if (is_null($old_value)) {
            return lang('Visibility set to <b>:new_value</b>', array('new_value' => $verbose_visibility[$new_value]));
          } else {
            return lang('Visibility changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $verbose_visibility[$old_value], 'new_value' => $verbose_visibility[$new_value]));
          } // if
        },

        'priority' => function($old_value, $new_value) {
          $priorities = array(
            PRIORITY_LOWEST   => lang('Lowest'),
            PRIORITY_LOW      => lang('Low'),
            PRIORITY_NORMAL   => lang('Normal'),
            PRIORITY_HIGH     => lang('High'),
            PRIORITY_HIGHEST  => lang('Highest'),
          );

          return lang('Priority changed from <b>:old_value</b> to <b>:new_value</b>', array(
            'old_value' => isset($priorities[$old_value]) ? $priorities[$old_value] : lang('Normal'),
            'new_value' => isset($priorities[$new_value]) ? $priorities[$new_value] : lang('Normal'),
          ));
        },

        'completed_on' => function($old_value, $new_value) {
          $new_completed_on = $new_value ? new DateTimeValue($new_value) : null;

          if($new_completed_on instanceof DateTimeValue && $new_completed_on->getTimestamp() > 0) {
            return lang('Marked as completed');
          } else {
            return lang('Marked as open');
          } // if
        },

        'category_id' => function($old_value, $new_value) {
          $new_category = Categories::findById($new_value);
          $old_category = Categories::findById($old_value);
          if($new_category instanceof Category) {
            if($old_category instanceof Category) {
              return lang('Category changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_category->getName(), 'new_value' => $new_category->getName()));
            } else {
              return lang('Category set to <b>:new_value</b>', array('new_value' => $new_category->getName()));
            } // if
          } else {
            if($old_category instanceof Category || is_null($new_category)) {
              return lang('Category set to empty value');
            } // if
          } // if
        },

        'assignee_id' => function($old_value, $new_value) {
          $new_assignee = $new_value ? Users::findById($new_value) : null;
          $old_assignee = $old_value ? Users::findById($old_value) : null;

          if($new_assignee instanceof User && $old_assignee instanceof User) {
            return lang('Reassigned from <b>:old_assignee</b> to <b>:new_assignee</b>', array(
              'old_assignee' => $old_assignee->getName(),
              'new_assignee' => $new_assignee->getName(),
            ));
          } elseif($new_assignee instanceof User) {
            return lang('<b>:new_assignee</b> is responsible for this :object_type', array(
              'new_assignee' => $new_assignee->getName(),
              'object_type' => $this->object->getVerboseType()
            ));
          } elseif($old_assignee instanceof User) {
            return lang('<b>:old_assignee</b> is no longer responsible for this :object_type', array(
              'old_assignee' => $old_assignee->getName(),
              'object_type' => $this->object->getVerboseType()
            ));
          } // if
        },

        'password' => function($old_value, $new_value) {
          return lang('Password changed');
        },

        'is_pinned' => function($old_value, $new_value) {
          if($new_value) {
            return lang('Pinned');
          } else {
            return lang('Unpinned');
          } // if
        },

        'language_id' => function($old_value, $new_value) {
          $lang = Languages::findById($new_value);
          $new_language = lang('default');
          if ($lang instanceof Language) {
            $new_language = $lang->getName();
          } // if
          $lang = Languages::findById($old_value);
          $old_language = lang('default');
          if ($lang instanceof Language) {
            $old_language = $lang->getName();
          } // if
          return lang('Language changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_language, 'new_value' => $new_language));
        },

        'label_id' => function($old_value, $new_value) {
          if($new_value) {
            if($old_value) {
              return lang('Label changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => Labels::getLabelName($old_value, lang('Unknown Label')), 'new_value' => Labels::getLabelName($new_value, lang('Unknown Label'))));
            } else {
              return lang('Label set to <b>:new_value</b>', array('new_value' => Labels::getLabelName($new_value, lang('Unknown Label'))));
            } // if
          } else {
            if($old_value) {
              return lang('Label <b>:old_value</b> removed', array('old_value' => Labels::getLabelName($old_value, lang('Unknown Label'))));
            } // if
          } // if
        }
      );

      EventsManager::trigger('on_history_field_renderers', array(&$this->object, &$result));

      return $result;
    } // getFieldRenderers

    /**
     * Return cached user value for given user ID
     * 
     * @param integer $user_id
     * @param string|null $alternative_user
     * @return string
     */
    private static function getCachedUserDetails($user_id, $alternative_user = null) {
      if (isset(self::$cached_users[$user_id])) {
        return self::$cached_users[$user_id];
      } else {
        return $alternative_user ? $alternative_user : lang('Unknown User');
      } // if
    } // getCachedUserDetails
    
  }