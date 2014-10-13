<?php

  /**
   * Discussions manager class
   *
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class Discussions extends ProjectObjects {
    
    // Sharing context
    const SHARING_CONTEXT = 'discussion';
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access discussions section of $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'discussion', ($check_tab ? 'discussions' : null));
    } // canAccess
    
    /**
     * Returns true if $user can add discussions to $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'discussion', ($check_tab ? 'discussions' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage discussions in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'discussion', ($check_tab ? 'discussions' : null));
    } // canManage
    
    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------
    
    /**
     * Returns true if $user read this discussion
     * 
     * $discussion can be instance of Discussion class or discussion ID
     * 
     * @param Discussion $discussion
     * @param User $by
     * @param DateTimeValue $last_comment_on
     * @return boolean
     */
    static function isRead($discussion, User $by, $last_comment_on = null, $last_comment_created_by_id = null) {
      $discussion_id = $discussion instanceof Discussion ? $discussion->getId() : $discussion;
      
      if(empty($last_comment_on)) {
        if($discussion instanceof Discussion) {
          $last_comment_created_by_id = $discussion->getLastCommentById();
          if(empty($last_comment_on)) {
            $last_comment_on = $discussion->getCreatedOn();
            $last_comment_created_by_id = $by->getCreatedById();
          } // if
        } else {
          $row = DB::executeFirstRow('SELECT created_on, created_by_id, datetime_field_1, integer_field_1 FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND id = ?', 'Discussion', $discussion_id);
          if($row) {
            if ($row['datetime_field_1']) {
              $last_comment_on = new DateTimeValue($row['datetime_field_1']);
              $last_comment_created_by_id = $row['integer_field_1'];
            }else {
              $last_comment_on = new DateTimeValue($row['created_on']);
              $last_comment_created_by_id = $row['created_by_id'];
            };
          } // if
        } // if
      } else {
        if(is_string($last_comment_on)) {
          $last_comment_on = new DateTimeValue($last_comment_on);
        } // if
      } // if

      if(empty($last_comment_on)) {
        return true; // Discussion does not exist or data is invalid. Lets not assume too much and break here...
      } // if
      
      if($last_comment_on->getTimestamp() > DateTimeValue::makeFromString('-30 days')->beginningOfDay()->getTimestamp()) {
        return AccessLogs::isAccessedSince(array('Discussion', $discussion_id), $by, $last_comment_on) || ($by->getId() == $last_comment_created_by_id);
      } else {
        return true; // Last comment made more than 30 days ago
      } // if
    } // isRead
    
    /**
     * Return icon URL based on given flags
     * 
     * @param boolean $is_pinned
     * @param boolean $is_read
     * @param string $interface
     * @return string
     */
    static function getIconUrl($is_pinned, $is_read, $interface = null) {
    	if(empty($interface)) {
        $interface = AngieApplication::INTERFACE_DEFAULT;
      } // if
      
      if($interface == AngieApplication::INTERFACE_DEFAULT) {
      	if($is_read) {
	    		return $is_pinned ? AngieApplication::getImageUrl('icons/16x16/discussion-read-pinned.png', DISCUSSIONS_MODULE) : AngieApplication::getImageUrl('icons/16x16/discussion-read.png', DISCUSSIONS_MODULE);
	    	} else {
	    		return $is_pinned ? AngieApplication::getImageUrl('icons/16x16/discussion-unread-pinned.png', DISCUSSIONS_MODULE) : AngieApplication::getImageUrl('icons/16x16/discussion-unread.png', DISCUSSIONS_MODULE); 
	    	} // if
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		if($is_read) {
	    		return $is_pinned ? AngieApplication::getImageUrl('icons/32x32/discussion-read-pinned.png', DISCUSSIONS_MODULE, AngieApplication::INTERFACE_PHONE) : AngieApplication::getImageUrl('icons/32x32/discussion-read.png', DISCUSSIONS_MODULE, AngieApplication::INTERFACE_PHONE);
	    	} else {
	    		return $is_pinned ? AngieApplication::getImageUrl('icons/32x32/discussion-unread-pinned.png', DISCUSSIONS_MODULE, AngieApplication::INTERFACE_PHONE) : AngieApplication::getImageUrl('icons/32x32/discussion-unread.png', DISCUSSIONS_MODULE, AngieApplication::INTERFACE_PHONE); 
	    	} // if
    	} // if
    } // getIconUrl
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return discussions posted in a specific category
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    static function findByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $offset = null, $limit = null) {
      return Discussions::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
        'offset' => $offset,
        'limit' => $limit,
      ));
    } // findByProject
    
    /**
     * Return archived discussions by project
     *
     * @param Project $project
     * @param integer $state
     * @param integer $min_visibility
     * @return array
     */
    static function findArchivedByProject(Project $project, $state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      return Discussions::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state = ? AND visibility >= ?', $project->getId(), 'Discussion', $state, $min_visibility),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC'
      ));
    } // findArchivedByProject
    
    /**
     * Return discussions posted in a specific category in projects
     *
     * @param array $project_ids
     * @param integer $min_state
     * @param integer $min_visibility
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    static function findByProjectIds($project_ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $offset = null, $limit = null) {
      return Discussions::find(array(
        'conditions' => array('project_id IN (?) AND type = ? AND state >= ? AND visibility >= ?', $project_ids, 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1, datetime_field_1 DESC',
        'offset' => $offset,
        'limit' => $limit,
      ));
    } // findByProjectIds
    
    /**
     * Return discussions by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @param integer $limit
     * @param array $exclude
     * @param int $timestamp
     * @return DBResult|Discussion[]
     */
    static function findByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $limit = null, $exclude = null, $timestamp = null) {
    	$conditions = array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'Discussion', $min_state, $min_visibility); // Milestone ID + Project ID (integrity issue from activeCollab 2)
      if ($exclude && $timestamp) {
      	$conditions[0] .= ' AND id NOT IN (?) AND created_on < ?';
      	$conditions[] = $exclude;
      	$conditions[] = date(DATETIME_MYSQL, $timestamp); 
      }
    	return Discussions::find(array(
        'conditions' => $conditions,
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
        'limit' => $limit,
      ));
    } // findByMilestone
    
    /**
     * Return number of discussions by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return int
     */
    static function countByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Discussions::count(array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'Discussion', $min_state, $min_visibility));
    } // countByMilestone

    /**
     * Return active milestone discussions that given $user can access
     *
     * @param Milestone $milestone
     * @param User $user
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    static function findActiveByMilestone(Milestone $milestone, User $user, $offset = 0, $limit = 25) {
      return Discussions::find(array(
        'conditions' => array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'Discussion', STATE_VISIBLE, $user->getMinVisibility()),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
        'offset' => $offset,
        'limit' => $limit
      ));
    } // findActiveByMilestone
    
    /**
     * Return paginated discussions by project
     * 
     * Discussions are ordered by IS_PINNED flag and time of last reply
     *
     * @param Project $project
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function paginateByProject(Project $project, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
      ), $page, $per_page);
    } // paginateByProject
    
    /**
     * Return paginated discussions by project ids
     * 
     * Discussions are ordered by IS_PINNED flag and time of last reply
     *
     * @param array $project_ids
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function paginateByProjectIds($project_ids, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('project_id IN (?) AND type = ? AND state >= ? AND visibility >= ?', $project_ids, 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1, datetime_field_1 DESC',
      ), $page, $per_page);
    } // paginateByProjectIds
    
    /**
     * Return discussions by category
     *
     * @param DiscussionCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return unknown
     */
    static function findByCategory(DiscussionCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('category_id = ? AND type = ? AND state >= ? AND visibility >= ?', $category->getId(), 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
      ));
    } // findByCategory
    
    /**
     * Return number of tasks from a given category
     * 
     * @param DiscussionCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    static function countByCategory(DiscussionCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::count(array('category_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $category->getId(), 'Discussion', $min_state, $min_visibility));
    } // countByCategory
    
    /**
     * Return paginated discussions by Category
     *
     * @param DiscussionCategory $category
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function paginateByCategory(DiscussionCategory $category, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('category_id = ? AND type = ? AND state >= ? AND visibility >= ?', $category->getId(), 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
      ), $page, $per_page);
    } // paginateByCategory
    
    /**
     * Find all discussions in project, and prepare them for objects list
     * 
     * @param Project $project
     * @param User $user
     * @param int $state
     * @return array
     */
    static function findForObjectsList(Project $project, User $user, $state = STATE_VISIBLE) {
      $result = array();
    	
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $discussions = DB::execute("SELECT id, name, category_id, milestone_id, created_on, state, visibility, datetime_field_1 AS 'last_comment_on', boolean_field_1 AS 'is_pinned',integer_field_1 AS 'last_comment_by_id' FROM $project_objects_table WHERE type = ? AND project_id = ? AND state = ? AND visibility >= ? ORDER BY boolean_field_1 DESC, datetime_field_1 DESC, created_on DESC", 'Discussion', $project->getId(), $state, $user->getMinVisibility());
	    	  
      if ($discussions instanceof DBResult) {
        $discussions->setCasting(array(
          'id' => DBResult::CAST_INT,
          'category_id' => DBResult::CAST_INT,
          'milestone_id' => DBResult::CAST_INT,
          'is_pinned' => DBResult::CAST_INT,
        ));

        $discussions_url = Router::assemble('project_discussion', array('project_slug' => $project->getSlug(), 'discussion_id' => '--DISCUSSIONID--'));

        foreach ($discussions as $discussion) {
          $is_read = Discussions::isRead($discussion['id'], $user, $discussion['last_comment_on'] ? $discussion['last_comment_on'] : $discussion['created_on'], $discussion['last_comment_by_id']);
          $result[] = array(
            'id'              => $discussion['id'],
            'name'            => $discussion['name'],
            'category_id'     => $discussion['category_id'],
            'milestone_id'    => $discussion['milestone_id'],
            'icon'            => self::getIconUrl($discussion['is_pinned'], $is_read),
            'is_read'         => $is_read ? '1' : '0',
            'is_pinned'       => $discussion['is_pinned'],
            'permalink'       => str_replace('--DISCUSSIONID--', $discussion['id'], $discussions_url),
            'is_favorite'     => Favorites::isFavorite(array('Discussion', $discussion['id']), $user),
            'is_archived'     => $discussion['state'] == STATE_ARCHIVED ? '1' : '0',
            'visibility'      => $discussion['visibility']
          );
        } // foreach
      } // if

      return $result;
    } // findForObjectsList

    /**
     * Find all discussions in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function findForExport(Project $project, User $user, &$parents_map, $changes_since) {
      $result = array();

      if(Discussions::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $discussions = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', milestone_id, category_id, state, visibility, priority, created_by_id, created_on, updated_by_id, updated_on, is_locked, datetime_field_1 AS 'last_comment_on', integer_field_1 AS 'last_comment_by_id' FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY boolean_field_1 DESC, datetime_field_1 DESC, created_on DESC", 'Discussion', $project->getId(), STATE_ARCHIVED, $user->getMinVisibility());

        if($discussions instanceof DBResult) {
          $discussions->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'milestone_id' => DBResult::CAST_INT,
            'category_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT
          ));

          $discussion_url = Router::assemble('project_discussion', array('project_slug' => $project->getSlug(), 'discussion_id' => '--DISCUSSIONID--'));

          foreach($discussions as $discussion) {
            $is_read = Discussions::isRead($discussion['id'], $user, $discussion['last_comment_on'] ? $discussion['last_comment_on'] : $discussion['created_on'], $discussion['last_comment_by_id']);
            $result[] = array(
              'id'              => $discussion['id'],
              'type'            => $discussion['type'],
              'name'            => $discussion['name'],
              'body'            => $discussion['body'],
              'body_formatted'  => $discussion['body_formatted'],
              'milestone_id'    => $discussion['milestone_id'],
              'category_id'     => $discussion['category_id'],
              'state'           => $discussion['state'],
              'visibility'      => $discussion['visibility'],
              'priority'        => $discussion['priority'],
              'is_read'         => $is_read ? '1' : '0',
              'is_locked'       => $discussion['is_locked'],
              'created_by_id'   => $discussion['created_by_id'],
              'created_on'      => $discussion['created_on'],
              'updated_by_id'   => $discussion['updated_by_id'],
              'updated_on'      => $discussion['updated_on'],
              'permalink'       => str_replace('--DISCUSSIONID--', $discussion['id'], $discussion_url)
            );

            $parents_map[$discussion['type']][] = $discussion['id'];
          } // foreach
        } // if
      } // if

      return $result;
    } // findForExport

    /**
     * Find all discussions in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param string $output_file
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     * @throws Error
     */
    static function exportToFileByProject(Project $project, User $user, $output_file, &$parents_map, $changes_since) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // Open json array
      fwrite($output_handle, '[');

      $count = 0;
      if(Discussions::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $discussions = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', project_id, milestone_id, category_id, state, visibility, priority, created_by_id, created_on, updated_by_id, updated_on, is_locked, datetime_field_1 AS 'last_comment_on', integer_field_1 AS 'last_comment_by_id' FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY boolean_field_1 DESC, datetime_field_1 DESC, created_on DESC", 'Discussion', $project->getId(), (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED, $user->getMinVisibility());

        if($discussions instanceof DBResult) {
          $discussions->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'milestone_id' => DBResult::CAST_INT,
            'category_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT
          ));

          $discussion_url = Router::assemble('project_discussion', array('project_slug' => $project->getSlug(), 'discussion_id' => '--DISCUSSIONID--'));

          $buffer = '';
          foreach($discussions as $discussion) {
            $is_read = Discussions::isRead($discussion['id'], $user, $discussion['last_comment_on'] ? $discussion['last_comment_on'] : $discussion['created_on'], $discussion['last_comment_by_id']);

            if($count > 0) $buffer .= ',';

            $buffer .= JSON::encode(array(
              'id'              => $discussion['id'],
              'type'            => $discussion['type'],
              'name'            => $discussion['name'],
              'body'            => $discussion['body'],
              'body_formatted'  => $discussion['body_formatted'],
              'project_id'      => $discussion['project_id'],
              'milestone_id'    => $discussion['milestone_id'],
              'category_id'     => $discussion['category_id'],
              'state'           => $discussion['state'],
              'visibility'      => $discussion['visibility'],
              'priority'        => $discussion['priority'],
              'is_read'         => $is_read ? '1' : '0',
              'is_locked'       => $discussion['is_locked'],
              'created_by_id'   => $discussion['created_by_id'],
              'created_on'      => $discussion['created_on'],
              'updated_by_id'   => $discussion['updated_by_id'],
              'updated_on'      => $discussion['updated_on'],
              'permalink'       => str_replace('--DISCUSSIONID--', $discussion['id'], $discussion_url),
              'is_favorite'     => Favorites::isFavorite(array('Discussion', $discussion['id']), $user),
            ));

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $parents_map[$discussion['type']][] = $discussion['id'];
            $count++;
          } // foreach

          if($buffer) {
            fwrite($output_handle, $buffer);
          } // if
        } // if
      } // if

      // Close json array
      fwrite($output_handle, ']');

      // Close the handle and set correct permissions
      fclose($output_handle);
      @chmod($output_file, 0777);

      return $count;
    } // exportToFileByProject
    
    /**
     * Find all discussions in project and prepare them for phone list
     * 
     * @param Project $project
     * @param User $user
     * @return array
     */
    static function findForPhoneList(Project $project, User $user) {
    	$result = array();
    	$read = array();
    	
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $reference_date = new DateTimeValue('-90 days');
      
      $discussions = DB::execute("SELECT id, name, created_on, datetime_field_1 AS 'last_comment_on', boolean_field_1 AS 'is_pinned', integer_field_1 AS 'last_comment_by_id' FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? ORDER BY boolean_field_1 DESC, datetime_field_1 DESC, created_on DESC", 'Discussion', $project->getId(), STATE_VISIBLE, $user->getMinVisibility());
      
    	if($discussions instanceof DBResult) {
    	  $discussions_url = Router::assemble('project_discussion', array('project_slug' => $project->getSlug(), 'discussion_id' => '--DISCUSSIONID--'));
    	  
    		foreach($discussions as $discussion) {
          $is_read = Discussions::isRead($discussion['id'], $user, $discussion['last_comment_on'] ? $discussion['last_comment_on'] : $discussion['created_on'], $discussion['last_comment_by_id']);
          
          if(!$is_read || $discussion['is_pinned']) {
          	$result[] = array(
	    				'id' => $discussion['id'], 
	    				'name' => $discussion['name'],
	    				'icon' => self::getIconUrl($discussion['is_pinned'], $is_read, AngieApplication::INTERFACE_PHONE),
	    				'permalink' => str_replace('--DISCUSSIONID--', $discussion['id'], $discussions_url)
	    			);
          } elseif($is_read && !$discussion['is_pinned']) {
          	$read[] = array(
	    				'id' => $discussion['id'], 
	    				'name' => $discussion['name'],
	    				'icon' => self::getIconUrl($discussion['is_pinned'], $is_read, AngieApplication::INTERFACE_PHONE),
	    				'permalink' => str_replace('--DISCUSSIONID--', $discussion['id'], $discussions_url)
	    			);
          } // if
    		} // foreach
    	} // if
    	
    	return array_merge($result, $read);
    } // findForPhoneList
    
    /**
     * Find discussions for printing by grouping and filtering criteria
     * 
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @param string $group_by
     * @param array $filter_by
     * @return DBResult
     */
    public function findForPrint(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $group_by = null, $filter_by = null, IUser $user) { 	
      // initial condition
      $conditions = array(
      	DB::prepare('(project_id = ? AND type = ? AND state = ? AND visibility >= ?)', $project->getId(), 'Discussion', $min_state, $min_visibility),
      );
      if (!in_array($group_by, array('milestone_id', 'category_id', 'is_read','boolean_field_1 DESC'))) {
      	$group_by = null;
      } // if
      
      $filter_by = array_var($filter_by,'is_archived');
      // filter by completion status
      if ($filter_by === '0') {
		    $conditions[] = DB::prepare('(state = ?)', STATE_VISIBLE);
      } else if ($filter_by === '1') {
      	$conditions[] = DB::prepare('(state = ?)', STATE_ARCHIVED);
      } // if
  
      // do find discussions
      $discussions = Discussions::find(array(
      	'conditions' => implode(' AND ', $conditions),
      	'order' => ($group_by ? $group_by . ', ' : '') .  'boolean_field_1 DESC,datetime_field_1 DESC,created_on DESC'
      )); 
      
      if($group_by == 'boolean_field_1 DESC') {
        if(is_foreachable($discussions)) {
          $read = array();
          $unread = array();
          foreach ($discussions as $discussion) {
            if($discussion->isRead($user)) {
              $read[] = $discussion;
            } else {
              $unread[] = $discussion;
            }//if
          }//foreach
          $temp = array_merge($unread,$read);
        }//if
        $discussions = $temp;
      }
     
      return $discussions;
    } // findForPrint
    
    /**
     * Count discussions by project
     * 
     * @param Project $project
     * @param Category $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return number
     */
    public function countByProject(Project $project, $category = null, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
    	if ($category instanceof DiscussionCategory) {
    		return Discussions::count(array('project_id = ? AND type = ? AND category_id = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Discussion', $category->getId(), $min_state, $min_visibility));
    	} else {
    		return Discussions::count(array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Discussion', $min_state, $min_visibility));
    	} // if
    } // countByProject
    
    /**
     * Return user discussions
     * 
     * If $extended is true, system will return not just discussions started by 
     * a user, but also discussion in which the user was involved (by posting a 
     * comment)
     * 
     * @param IUser $user
     * @param boolean $extended
     * @param boolean $include_completed_projects
     * @param integer $limit
     * @return Discussion[]
     */
    static function findByUser(IUser $user, $extended = false, $include_completed_projects = false, $limit = null) {
      if($include_completed_projects) {
        $project_ids = Projects::findIdsByUser($user, true, DB::prepare('state >= ?', STATE_ARCHIVED));
      } else {
        $project_ids = Projects::findIdsByUser($user, true, DB::prepare('state > ? AND completed_on IS NULL', STATE_ARCHIVED));
      } // if
      
      if($project_ids) {
        $conditions = DB::prepare('type = ? AND created_by_id = ? AND state >= ? AND visibility >= ? AND project_id IN (?)', 'Discussion', $user->getId(), STATE_VISIBLE, $user->getMinVisibility(), $project_ids);

        if($extended) {
          $involved_in_ids = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND created_by_id = ? AND state >= ?', 'Discussion', $user->getId(), STATE_VISIBLE);
          
          if($involved_in_ids) {
            $conditions = DB::prepare('type = ? AND (created_by_id = ? OR id IN (?)) AND state >= ? AND visibility >= ? AND project_id IN (?)', 'Discussion', $user->getId(), $involved_in_ids, STATE_VISIBLE, $user->getMinVisibility(), $project_ids);
          } // if
        } // if
        
        return Discussions::find(array(
          'conditions' => $conditions,
          'order' => 'datetime_field_1 DESC',
          'offset' => 0,
          'limit' => $limit,
        ));
      } else {
        return null;
      } // if
    } // findByUser
    
    /**
     * Get all items from result and describes array for paged list 
     * 
     * @param DBResult $result
     * @param Project $active_project
     * @param User $logged_user
     * @param int $items_limit
     * @return Array
     */
    static function getDescribedDiscussionArray(DBResult $result, Project $active_project, User $logged_user, $items_limit = null) {
    	$return_value = array();
    	
    	if ($result instanceof DBResult) {
    		
    		$user_ids = array();
    		foreach($result as $row) {
    			if ($row['created_by_id'] && !in_array($row['created_by_id'], $user_ids)) {
    				$user_ids[] = $row['created_by_id'];
    			} //if
    		} //if

        $users_array = count($user_ids) ? Users::findByIds($user_ids)->toArrayIndexedBy('getId') : array();
    		
    	  foreach($result as $row) {

					$discussion = array();
	    		// Discussion Details
	    		$discussion['id'] = $row['id'];
	    		$discussion['name'] = clean($row['name']);
	    		$discussion['is_favorite'] = Favorites::isFavorite(array('Discussion', $discussion['id']), $logged_user);
	    		
          // Favorite
	    		$favorite_params = $logged_user->getRoutingContextParams();
	    		$favorite_params['object_type'] = $row['type'];
	    		$favorite_params['object_id'] = $row['id'];

          // Urls
          $discussion['urls']['remove_from_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_remove_from_favorites', $favorite_params);
          $discussion['urls']['add_to_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_add_to_favorites', $favorite_params);
          $discussion['urls']['view'] = Router::assemble('project_discussion', array('project_slug' => $active_project->getSlug(), 'discussion_id' => $row['id']));
          $discussion['urls']['edit'] = Router::assemble('project_discussion_edit', array('project_slug' => $active_project->getSlug(), 'discussion_id' => $row['id']));
          $discussion['urls']['trash'] = Router::assemble('project_discussion_trash', array('project_slug' => $active_project->getSlug(), 'discussion_id' => $row['id']));

          // CRUD

          $discussion['permissions']['can_edit'] = Discussions::canManage($logged_user, $active_project);
          $discussion['permissions']['can_trash'] = Discussions::canManage($logged_user, $active_project);
	    		
	    		// User & datetime details
	        
	    		$discussion['created_on'] = datetimeval($row['created_on']);
	    		$discussion['last_commented_on'] = datetimeval($row['datetime_field_1']);
	    		
	    		if($row['created_by_id']) {
	          $discussion['created_by'] = $users_array[$row['created_by_id']];
	        } elseif($row['created_by_email']) {
	          $discussion['created_by'] = new AnonymousUser($row['created_by_name'], $row['created_by_email']);
	        } else {
	          $discussion['created_by'] = null;
	        } // if
	    		$return_value[] = $discussion;
	    		
	    		if (count($return_value) === $items_limit) {
	    			break;
	    		} //if
    		} // foreach
    	} //if
    	return $return_value;
    } // getDescribedDiscussionArray
    
  }