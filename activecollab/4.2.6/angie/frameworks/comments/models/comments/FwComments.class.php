<?php

  /**
   * Framework level comments manager implementation
   *
   * @package angie.frameworks.comments
   * @subpackage models
   */
  abstract class FwComments extends BaseComments {
    
    /**
     * Return object comments
     *
     * @param IComments $object
     * @return array
     */
    static function findByObject(IComments $object) {
      return self::find(array(
        'conditions' => array("parent_type = ? AND parent_id = ? AND state >= ?", get_class($object), $object->getId(), ($object instanceof IState ? $object->getState() : STATE_VISIBLE)),
        'order' => 'created_on DESC',
      ));
    } // findByObject
    
    /**
     * Return public comments by object
     * 
     * @param IComments $object
     * @return Comment[]
     */
    static function findPublicByObject(IComments $object) {
      return self::find(array(
        'conditions' => array("parent_type = ? AND parent_id = ? AND state >= ?", get_class($object), $object->getId(), STATE_VISIBLE),
        'order' => 'created_on DESC',
      ));
    } // findPublicByObject
    
    /**
     * Return $num latest comments for $object
     *
     * @param IComments $object
     * @param IUser $user
     * @param integer $num
     * @return DBResult
     */
    static function findLatestByObject(IComments $object, IUser $user, $num = 10) {
      if($object instanceof IState) {
        $conditions = array("parent_type = ? AND parent_id = ? AND state >= ?", get_class($object), $object->getId(), $object->getState());
      } else {
        $conditions = array("parent_type = ? AND parent_id = ?", get_class($object), $object->getId());
      } // if
      
      return self::find(array(
        'conditions' => $conditions, 
        'order' => 'created_on DESC', 
        'offset' => 0, 
        'limit' => $num, 
      ));
    } // findLatestByObject
    
    /**
     * Load more comments by object
     *
     * @param IComments $object
     * @param IUser $user
     * @param integer $exclude
     * @param integer $num
     * @return DBResult
     */
    static function loadMoreByObject(IComments $object, IUser $user, DateTimeValue $load_older_than, $exclude) {
      if($object instanceof IState) {
        $conditions = array("parent_type = ? AND parent_id = ? AND created_on < ? AND id NOT IN (?) AND state >= ?", get_class($object), $object->getId(), $load_older_than, $exclude, $object->getState());
      } else {
        $conditions = array("parent_type = ? AND parent_id = ? AND created_on < ? AND id NOT IN (?)", get_class($object), $object->getId(), $load_older_than, $exclude);
      } // if
      
      return self::find(array(
        'conditions' => $conditions,
        'order' => 'created_on DESC',
      ));
    } // loadMoreByObject
    
    /**
     * Return last comment for a given object
     *
     * @param IComments $object
     * @param integer $min_state
     * @return Comment
     */
    static function findLastByObject(IComments $object) {
      return Comments::find(array(
        'conditions' => array("parent_type = ? AND parent_id = ? AND state >= ?", get_class($object), $object->getId(), ($object instanceof IState ? $object->getState() : STATE_VISIBLE)), 
        'order' => 'created_on DESC', 
        'one' => true, 
      ));
    } // findLastByObject
    
    /**
     * Return number of comments that match given criteria
     *
     * @param IComments $object
     * @param boolean $use_cache
     * @return integer
     */
    static function countByParent(IComments $parent, $use_cache = true) {
      return AngieApplication::cache()->getByObject($parent, 'comments_count', function() use ($parent) {
        return (integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND parent_id = ? AND state >= ? GROUP BY parent_type, parent_id', get_class($parent), $parent->getId(), STATE_ARCHIVED);
      }, !$use_cache);
    } // countByParent
    
    /**
     * Return number of public comments
     *
     * @param IComments $object
     * @return integer
     */
    static function countPublicByObject(IComments $object) {
      return self::count(array("parent_type = ? AND parent_id = ? AND state >= ?", get_class($object), $object->getId(), STATE_VISIBLE));
    } // countPublicByObject
    
    /**
     * Return number of new comments since user's visit
     * 
     * @param IComments $object
     * @param User $user $user
     * @param DateTimeValue $visit
     */
    static function countByObjectSinceVisit(IComments $object, User $user, DateTimeValue $visit) {
      return self::count(array("parent_type = ? AND parent_id = ? AND created_by_id != ? AND created_on >= ? AND state >= ?", get_class($object), $object->getId(), $user->getId(), $visit, ($object instanceof IState ? $object->getState() : STATE_VISIBLE)));
    } // countByObjectSinceVisit
    
    /**
     * Paginate comments by object
     *
     * @param IComments $object
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    static function paginateByObject(IComments $object, $page = 1, $per_page = 30) {
      return self::paginate(array(
        'conditions' => array("parent_type = ? AND parent_id = ? AND state >= ?", get_class($object), $object->getId(), ($object instanceof IState ? $object->getState() : STATE_VISIBLE)),
        'order' => 'created_on DESC',
      ), $page, $per_page);
    } // paginateByObject
    
    /**
     * Return people who commented on $object that $user can see
     *
     * @param IComments $object
     * @param IUser $user
     * @return DBResult
     */
    static function findCommenters(IComments $object, IUser $user) {
  	  $visible_user_ids = Users::findVisibleUserIds($user);
  	  
    	if($visible_user_ids) {
    	  $users_table = TABLE_PREFIX . 'users';
    	  $comments_table = TABLE_PREFIX . 'comments';
    	  
    	  return Users::findBySQL("SELECT DISTINCT $users_table.* FROM $users_table JOIN $comments_table ON $users_table.id = $comments_table.created_by_id WHERE $comments_table.parent_type = ? AND $comments_table.parent_id = ? AND $comments_table.state >= ? AND $users_table.id IN (?) ORDER BY CONCAT($users_table.first_name, $users_table.last_name, $users_table.email)", get_class($object), $object->getId(), STATE_VISIBLE, $visible_user_ids);
    	} else {
    		return null;
    	} // if
    } // findCommenters

    /**
     * Return attachment disk space usage by parents
     *
     * $parents is an array where key is parent type and value is array of
     * object ID-s of that particular parent
     *
     * @param array $parents
     * @param integer $project_disk_space_usage
     */
    static function getAttachmentDiscSpaceUsageByParents($parents, &$project_disk_space_usage) {
      if(is_foreachable($parents)) {
        $comments_table = TABLE_PREFIX . 'comments';

        foreach($parents as $parent_type => $parent_ids) {
          $rows = DB::execute("SELECT id, type FROM $comments_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);

          if($rows) {
            $comments = array();

            foreach($rows as $row) {
              if(array_key_exists($row['type'], $comments)) {
                $comments[$row['type']][] = (integer) $row['id'];
              } else {
                $comments[$row['type']] = array((integer) $row['id']);
              } // if
            } // foreach

            Attachments::getDiscSpaceUsageByParents($comments, $project_disk_space_usage);
          } // if
        } // foreach
      } // if
    } // getAttachmentDiscSpaceUsageByParents
    
    // ---------------------------------------------------
    //  State
    // ---------------------------------------------------
    
    /**
     * Archive comments attached to a given parent object
     *
     * @param IComments $parent
     * @throws InvalidInstanceError
     */
    static function archiveByParent(IComments &$parent) {
      if($parent instanceof IState) {
        $rows = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());
        if($rows) {
          $comment_ids = array();
          $attachment_parents = array();
          
          foreach($rows as $row) {
            $comment_ids[] = (integer) $row['id'];
            $attachment_parents[] = DB::prepare('(parent_type = ? AND parent_id = ?)', $row['type'], $row['id']);
          } // foreach
          
          $parent->state()->archiveSubitems(TABLE_PREFIX . 'comments', array('id IN (?)', $comment_ids), true);
          $parent->state()->archiveSubitems(TABLE_PREFIX . 'attachments', implode(' AND ', $attachment_parents));
        } // if
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // archiveByParent
    
    /**
     * Unarchive comments attached to a given parent object
     *
     * @param IComments $parent
     * @throws InvalidInstanceError
     */
    static function unarchiveByParent(IComments &$parent) {
      if($parent instanceof IState) {
        $rows = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());
        if(is_foreachable($rows)) {
          $comment_ids = array();
          $attachment_parents = array();
          
          foreach($rows as $row) {
            $comment_ids[] = (integer) $row['id'];
            $attachment_parents[] = DB::prepare('(parent_type = ? AND parent_id = ?)', $row['type'], $row['id']);
          } // foreach
          
          $parent->state()->unarchiveSubitems(TABLE_PREFIX . 'comments', array('id IN (?)', $comment_ids), true);
          $parent->state()->unarchiveSubitems(TABLE_PREFIX . 'attachments', implode(' AND ', $attachment_parents));
        } // if
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // unarchiveByParent
    
    /**
     * Trash comments attached to a given parent object
     *
     * @param IComments $parent
     * @throws InvalidInstanceError
     */
    static function trashByParent(IComments &$parent) {
      if($parent instanceof IState) {
        $rows = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());
        if(is_foreachable($rows)) {
          $comment_ids = array();
          $attachment_parents = array();
          
          foreach($rows as $row) {
            $comment_ids[] = (integer) $row['id'];
            $attachment_parents[] = DB::prepare('(parent_type = ? AND parent_id = ?)', $row['type'], $row['id']);
          } // foreach
          
          $parent->state()->trashSubitems(TABLE_PREFIX . 'comments', array('id IN (?)', $comment_ids), true);
          $parent->state()->trashSubitems(TABLE_PREFIX . 'attachments', implode(' AND ', $attachment_parents));
        } // if
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // trashByParent
    
    /**
     * Restore from trash comments attached to a given parent object
     *
     * @param IComments $parent
     * @throws InvalidInstanceError
     */
    static function untrashByParent(IComments &$parent) {
      if($parent instanceof IState) {
        $rows = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());
        if(is_foreachable($rows)) {
          $comment_ids = array();
          $attachment_parents = array();
          
          foreach($rows as $row) {
            $comment_ids[] = (integer) $row['id'];
            $attachment_parents[] = DB::prepare('(parent_type = ? AND parent_id = ?)', $row['type'], $row['id']);
          } // foreach
          
          $parent->state()->untrashSubitems(TABLE_PREFIX . 'comments', array('id IN (?)', $comment_ids), true);
          $parent->state()->untrashSubitems(TABLE_PREFIX . 'attachments', implode(' AND ', $attachment_parents));
        } // if
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // untrashByParent
    
    /**
     * Trash comments attached to a given parent object
     *
     * @param IComments $parent
     * @param boolean $soft
     * @throws Exception
     */
    static function deleteByParent(IComments &$parent, $soft = true) {
      $comments_table = TABLE_PREFIX . 'comments';
      
      $rows = DB::execute("SELECT id, type FROM $comments_table WHERE parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId());
      
      if($rows) {
        $attachments_table = TABLE_PREFIX . 'attachments';
        
        $comment_ids = array();
        $attachment_parents = array();
        
        foreach($rows as $row) {
          $id = (integer) $row['id'];
          $type = $row['type'];
          
          $comment_ids[] = $id;
          
          if(isset($attachment_parents[$type])) {
            $attachment_parents[$type][] = $id;
          } else {
            $attachment_parents[$type] = array($id);
          } // if
        } // foreach
        
        try {
          DB::beginWork('Droping comments @ ' . __CLASS__);
          
          $attachment_parent_conditions = array();
          foreach($attachment_parents as $type => $ids) {
            $attachment_parent_conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
          } // if
          $attachment_parent_conditions = implode(' AND ', $attachment_parent_conditions);
          
          if($soft && $parent instanceof IState) {
            $parent->state()->deleteSubitems($comments_table, array('id IN (?)', $comment_ids), true);
            
            $parent->state()->deleteSubitems($attachments_table, $attachment_parent_conditions);
          } else {
            if($rows) {
              $comments = array();

              foreach($rows as $row) {
                if(array_key_exists($row['type'], $comments)) {
                  $comments[$row['type']][] = (integer) $row['id'];
                } else {
                  $comments[$row['type']] = array((integer) $row['id']);
                } // if
              } // foreach

              DB::execute("DELETE FROM $comments_table WHERE parent_type = ? AND parent_id IN (?)", get_class($parent), $parent->getId());

              ActivityLogs::deleteByParents($comments);
              Attachments::deleteByParents($comments);
              ModificationLogs::deleteByParents($comments);
            } // if
          } // if

          DB::commit('Comments dropped @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to drop comments @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByParent
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     * @throws Exception
     */
    static function deleteByParents($parents) {
      $comments_table = TABLE_PREFIX . 'comments';
      
      try {
        DB::beginWork('Removing comments by parent type and parent IDs @ ' . __CLASS__);
        
        if(is_foreachable($parents)) {
          foreach($parents as $parent_type => $parent_ids) {
            $rows = DB::execute("SELECT id, type FROM $comments_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
            
            if($rows) {
              $comments = array();
              
              foreach($rows as $row) {
                if(array_key_exists($row['type'], $comments)) {
                  $comments[$row['type']][] = (integer) $row['id'];
                } else {
                  $comments[$row['type']] = array((integer) $row['id']);
                } // if
              } // foreach
              
              DB::execute("DELETE FROM $comments_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
              
              ActivityLogs::deleteByParents($comments);
              Attachments::deleteByParents($comments);
              ModificationLogs::deleteByParents($comments);
            } // if
          } // foreach
        } // if
        
        DB::commit('Comments removed by parent type and parent IDs @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove comments by parent type and parent IDs @ ' . __CLASS__);
        throw $e;
      } // try
    } // deleteByParents
    
    /**
     * Remove comments by parent types
     * 
     * @param array $types
     * @throws Exception
     */
    static function deleteByParentTypes($types) {
      $comments_table = TABLE_PREFIX . 'comments';
      
      $rows = DB::executeFirstColumn("SELECT id, type FROM $comments_table WHERE parent_type IN (?)", $types);
      
      if($rows) {
        $parents = array();
        
        foreach($rows as $row) {
          if(array_key_exists($row['type'], $parents)) {
            $parents[$row['type']][] = (integer) $row['id'];
          } else {
            $parents[$row['type']] = array((integer) $row['id']);
          } // if
        } // foreach
        
        try {
          DB::beginWork('Cleaning up comment data @ ' . __CLASS__);
          
          DB::execute("DELETE FROM $comments_table WHERE parent_type IN (?)", $types);
          
          ActivityLogs::deleteByParents($parents);
          Attachments::deleteByParents($parents);
          ModificationLogs::deleteByParents($parents);
          
          DB::commit('Comment data cleaned up @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to clean up comment data @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByParentTypes
    
    /**
     * Find comments for widget
     * 
     * 	- Return array MUST resemble fwComments->describe() output
     * 
     * @param IComments $parent
     * @param IUser $user
     * @param int $offset
     * @param int $limit
     * @return array
     */
    static function findForWidget(IComments $parent, IUser $user, $offset = 0, $limit = 10) {
    	$comment_fields = array('id', 'type' ,'body', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'state', 'type'); // fields we need from comments
      $min_state = $parent instanceof IState ? $parent->getState() : STATE_VISIBLE;

      $comments = DB::execute("SELECT " . implode(', ', $comment_fields) . " FROM " . TABLE_PREFIX . "comments WHERE parent_type = ? AND parent_id = ? AND state >= ? ORDER BY created_on DESC LIMIT $offset, $limit", get_class($parent), $parent->getId(), $min_state);

      if($comments) {
        $comments->setCasting(array(
          'created_on'	=> DBResult::CAST_DATETIME,
        ));

        // extract comment and user ids
        $comment_ids = array();
        $user_ids = array();
        $comment_type = null;
        foreach ($comments as $comment) {
          $comment_ids[] = $comment['id'];
          if ($comment['created_by_id'] && !in_array($comment['created_by_id'], $user_ids)) {
            $user_ids[] = $comment['created_by_id'];
          } // if

          if ($comment_type === null) {
            $comment_type = $comment['type'];
          } // if
        } // foreach

        // Find users
        $users = is_foreachable($user_ids) ? Users::getIdDetailsMap($user_ids, array('first_name', 'last_name', 'email'), STATE_ARCHIVED, true, IUserAvatarImplementation::SIZE_BIG) : null;

        // find attachments
        $attachments = DB::execute("SELECT id, name, parent_id, location FROM " . TABLE_PREFIX . "attachments WHERE parent_type = ? AND parent_id IN (?) AND state >= ? ORDER BY created_on", $comment_type, $comment_ids, ($parent instanceof IState ? $parent->getState() : STATE_VISIBLE));
        $attachments_by_parent = array();
        if ($attachments) {
          foreach ($attachments as $attachment) {
            if(isset($attachments_by_parent[$attachment['parent_id']])) {
              if (!is_array($attachments_by_parent[$attachment['parent_id']])) {
                $attachments_by_parent[$attachment['parent_id']] = array();
              }	// if
            } else {
              $attachments_by_parent[$attachment['parent_id']] = array();
            } // if

            $attachments_by_parent[$attachment['parent_id']][] = $attachment;
          } // foreach
        } // if

        // url bases
        $routing_context = $parent->getRoutingContext() . '_comment';
        $routing_context_params = array_merge((array) $parent->getRoutingContextParams(), array('comment_id' => '--COMMENT--ID--'));
        $edit_comment_url_base = Router::assemble($routing_context . '_edit', $routing_context_params);
        $delete_comment_url_base = Router::assemble($routing_context . '_trash', $routing_context_params);
        $attachments_download_url_base = Router::assemble($routing_context . '_attachment_download', array_merge($routing_context_params, array('attachment_id' => '--ATTACHMENT--ID--')));
        $attachments_preview_url_base = Router::assemble($routing_context . '_attachment_preview', array_merge($routing_context_params, array('attachment_id' => '--ATTACHMENT--ID--')));

        // images
        $edit_image_url = AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK);
        $trash_image_url = AngieApplication::getImageUrl('/icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK);

        $result = array();
        foreach ($comments as $comment) {
          $new_comment = array(
            'id' => $comment['id'],
            'class' => $comment['type'],
            'body_formatted' => HTML::toRichText($comment['body']),
            'created_on' => $comment['created_on'],
            'attachments' => null,
            'options' =>  null,
            'event_names' => array(
              'updated' => 'comment_updated'
            )
          );

          $user_id = $comment['created_by_id'];
          $creator = isset($users[$user_id]) ? $users[$user_id] : null;
          if ($creator) {
            $new_comment['created_by'] = array(
              'id' => $user_id,
              'short_display_name' => Users::getUserDisplayName($creator, true),
              'email' => $comment['created_by_email'],
              'urls' => array(
                'view' => $creator['permalink'],
              ),
              'avatar' => array(
                'large' => $creator['avatar']
              ),
              'permalink' => $creator['permalink']
            );
          } else {
            $permalink = 'mailto:' . $comment['created_by_email'];
            $new_comment['created_by'] = array(
              'id' => null,
              'short_display_name' => $comment['created_by_name'] ? $comment['created_by_name'] : $comment['created_by_email'],
              'email' => $comment['created_by_email'],
              'urls' => array(
                'view' => $permalink,
              ),
              'avatar' => array(
                'large' => AngieApplication::getImageUrl("user-roles/member.40x40.png", AUTHENTICATION_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
              ),
              'permalink' => $permalink,
            );
          } // if

          $new_comment['options'] = new NamedList();

          // creator can edit
          $creator_can_edit = ($user_id == $user->getId()) && (($comment['created_on']->getTimestamp() + 1800) > DateTimeValue::now()->getTimestamp());

          // can edit
          $can_edit = ($comment['state'] > STATE_TRASHED) && ($user->isAdministrator() || $creator_can_edit);

          if ($parent instanceof ProjectObject) {
            $can_edit = $can_edit || $user->isProjectManager();
          } // if

          // this is 'magic' or 'hack', depending on point of view.
          // it makes comments non-editable if implementation has defined property 'comments_never_editable'
          if ($parent instanceof IComments && property_exists($parent->comments(), "comments_never_editable")) {
            $can_edit = false;
          } // if

          if ($can_edit) {
            $new_comment['options']->add('edit', array(
              'text'	=> lang('Edit'),
              'url'		=> str_replace('--COMMENT--ID--', $comment['id'], $edit_comment_url_base),
              'icon'	=> $edit_image_url,
            ));

            $new_comment['options']->add('trash', array(
              'text'	=> lang('Trash'),
              'url'		=> str_replace('--COMMENT--ID--', $comment['id'], $delete_comment_url_base),
              'icon'	=> $trash_image_url,
            ));
          } // if

          $comment_attachments = isset($attachments_by_parent[$comment['id']]) ? $attachments_by_parent[$comment['id']] : null;

          if (is_foreachable($comment_attachments)) {
            $new_comment['attachments'] = array();
            foreach ($comment_attachments as $comment_attachment) {

              $described_attachment = array(
                'id' => $comment_attachment['id'],
                'name' => $comment_attachment['name'],
                'urls' => array(
                  'view' => str_replace('--COMMENT--ID--', $comment['id'], str_replace('--ATTACHMENT--ID--', $comment_attachment['id'], $attachments_download_url_base))
                ),
                'preview' => array(
                  'icons' => array(
                    'large' => get_file_icon_url($comment_attachment['name'], '48x48')
                  )
                ),
              );

              $preview_type = Attachments::getPreviewType(array($comment_attachment['name'], UPLOAD_PATH . '/' . $comment_attachment['location']));
              if ($preview_type) {
                $described_attachment['urls']['preview'] = str_replace('--COMMENT--ID--', $comment['id'], str_replace('--ATTACHMENT--ID--', $comment_attachment['id'], $attachments_preview_url_base));
              } // if

              $new_comment['attachments'][] = $described_attachment;
            } // foreach
          } // if

          $result[] = $new_comment;
        } // foreach

        EventsManager::trigger('on_comments_for_widget_options', array(&$parent, &$user, &$result, &$comment_ids, array(
          'routing_context' => $routing_context,
          'routing_context_params'	=> $routing_context_params,
        )));

        return $result;
      } // if

      return null;
    } // findForWidget
    
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
    	$trashed_comments = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'comments WHERE state = ?', STATE_TRASHED);
    	    	
    	if (!is_foreachable($trashed_comments)) {
    		return null;
    	} // if
    	
    	$result = array();
    	
    	foreach ($trashed_comments as $trashed_comment) {
    		$type = strtolower($trashed_comment['type']);
    		
    		if (!isset($result[$type])) {
    			$result[$type] = array();
    		} // if 
    		
    		$result[$type][] = $trashed_comment['id'];
    	} // foreach
    	
    	return $result;
    } // getTrashedMap
    
    /**
     * Find trashed comments
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$query = Trash::getParentQuery($map);    	
    	if ($query) {
    		$trashed_comments = DB::execute('SELECT id, body, type, parent_id, parent_type FROM ' . TABLE_PREFIX . 'comments WHERE state = ? AND ' . $query . ' ORDER BY updated_on DESC, created_on DESC', STATE_TRASHED);
    	} else {
    		$trashed_comments = DB::execute('SELECT id, body, type, parent_id, parent_type FROM ' . TABLE_PREFIX . 'comments WHERE state = ? ORDER BY updated_on DESC, created_on DESC', STATE_TRASHED);
    	} // if
    	
    	if (!is_foreachable($trashed_comments)) {
    		return null;
    	} // if
    	
    	$items = array();
    	foreach ($trashed_comments as $comment) {
    		$items[] = array(
    			'id'						=> $comment['id'],
    			'name'					=> HTML::toPlainText($comment['body']),
    			'type'					=> $comment['type'],
    		);
    	} // foreach
    	
    	return $items;
    } // findTrashed
    
    /**
     * Delete trashed comments
     * 
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$comments = Comments::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($comments)) {
    		foreach ($comments as $comment) {
    			$comment->state()->delete();
    		} // foreach
    	} // if
    	
    	return true;
    } // deleteTrashed
    
  }