<?php

  /**
   * Implementation of IComments interface that is attached to actual objects
   *
   * @package angie.frameworks.comments
   * @subpackage models
   */
  abstract class ICommentsImplementation {
    
    /**
     * Object to which this implementation instance is attached to
     *
     * @var ApplicationObject|IComments
     */
    protected $object;
    
    /**
     * Construct project object commetns interface
     *
     * @param IComments $object
     * @throws InvalidInstanceError
     */
    function __construct(IComments $object) {
      if($object instanceof IComments && $object instanceof IObjectContext) {
        $this->object = $object;
      } else {
        throw new InvalidInstanceError('object', $object, array('IComments', 'IObjectConext'));
      } // if
    } // __construct
    
    /**
     * Create a new comment instance
     *
     * @return Comment
     */
    abstract function newComment();

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return '';
    } // getNotificationSubjectPrefix

    /**
     * Return code that will tell the application where to route replies to comments
     *
     * @return string
     */
    function getCommentRoutingCode() {
      $object = $this->object;

      return AngieApplication::cache()->getByObject($object, 'comment_routing_code', function() use ($object) {
        return strtoupper(str_replace('_', '-', Inflector::underscore(get_class($object)))) . '/' . $object->getId();
      });
    } // getCommentRoutingCode
    
    /**
     * Return comment submitted for this project object
     *
     * @param IUser $user
     * @return DBResult
     */
    function get(IUser $user) {
      return Comments::findByObject($this->object);
    } // get
    
    /**
     * Return all public comments
     * 
     * @return DBResult
     */
    function getPublic() {
      return Comments::findPublicByObject($this->object);
    } // getPublic
    
    /**
     * Return $count of latest comments
     * 
     * @param IUser $user
     * @param integer $count
     * @return DBResult
     */
    function getLatest(IUser $user, $count = 10) {
      return Comments::findLatestByObject($this->object, $user, $count);
    } // getLatest
    
    /**
     * Load more comments
     * 
     * @param IUser $user
     * @param array $loaded_comment_ids
     * @param DateTimeValue $reference
     * @return DBResult
     */
    function getMore(IUser $user, $loaded_comment_ids, DateTimeValue $reference) {
      return Comments::loadMoreByObject($this->object, $user, $reference, $loaded_comment_ids);
    } // getMore
    
    /**
     * Return last comment by user
     * 
     * @param IUser $user
     * @return Comment
     */
    function getLast(IUser $user) {
      return Comments::findLastByObject($this->object);
    } // getLast
    
    /**
     * Return number of comments for this particular object
     *
     * @param IUser $user
     * @param boolean $use_cache
     * @return integer
     */
    function count(IUser $user, $use_cache = true) {
      return Comments::countByParent($this->object, $use_cache);
    } // count
    
    /**
     * Return number of public comments
     * 
     * @return integer
     */
    function countPublic() {
      return Comments::countPublicByObject($this->object);
    } // countPublic
    
    /**
     * Count new objects since last visit
     * 
     * @param IUser $user
     * @param DateTimeValue $visit
     * @return integer
     */
    function countSinceVisit(IUser $user, $visit) {
      return Comments::countByObjectSinceVisit($this->object, $user, $visit);
    } // countSinceVisit
    
    /**
     * Return comments for a given page (paginate commetns)
     *
     * @param IUser $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginate(IUser $user, $page = 1, $per_page = 10) {
      return Comments::paginateByObject($this->object, $page, $per_page);
    } // paginate
    
    /**
     * Return list of users involved in a discussion
     * 
     * @param IUser $user
     * @return DBResult
     */
    function getCommenters(IUser $user) {
      return Comments::findCommenters($this->object, $user);
    } // getCommenters
    
    /**
     * Prepare state related object options
     *
     * @param NamedList $options
     * @param User $user
     * @param string $interface
     */
    function prepareObjectOptions(NamedList $options, User $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canChangeLockedState($user)) {
      	
      	// Regular web browser request
	    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
	    	  $options->add('lock_unlock', array(
	          'text' => 'Lock/Unlock', 
	          'url' => '#',
	          'icon' => AngieApplication::getImageUrl(($this->isLocked() ? 'icons/12x12/unlock-comments.png' : 'icons/12x12/lock-comments.png'), COMMENTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
	          'onclick' => new AsyncTogglerCallback(array(
	            'text' => lang('Unlock Comments'),
	            'url' => $this->getUnlockUrl(), 
	            'success_message' => lang('Comments have been successfully unlocked'),
	            'success_event' => $this->object->getUpdatedEventName(),
	          ), array(
	            'text' => lang('Lock Comments'), 
	            'url' => $this->getLockUrl(), 
	            'success_message' => lang('Comments have been successfully locked'),
	            'success_event' => $this->object->getUpdatedEventName(),
	          ), $this->isLocked()),
	        ));
		    	
		    // Phone device
	    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
	    		if($this->isLocked()) {
	    			$options->add('unlock', array(
	            'text' => lang('Unlock Comments'),
	            'url' => $this->getUnlockUrl(),
	            'icon' => AngieApplication::getImageUrl('icons/navbar/unlock-comments.png', COMMENTS_FRAMEWORK, AngieApplication::INTERFACE_PHONE)
	          ));
	    		} else {
	    			$options->add('lock', array(
	            'text' => lang('Lock Comments'),
	            'url' => $this->getLockUrl(),
	            'icon' => AngieApplication::getImageUrl('icons/navbar/lock-comments.png', COMMENTS_FRAMEWORK, AngieApplication::INTERFACE_PHONE)
	          ));
	    		} // if
	    	} // if
      } // if
    } // prepareObjectOptions
    
    /**
     * Describe comment related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['comments_url'] = $this->getUrl();
      $result['comments_count'] = $this->count($user);

      $result['is_locked'] = $this->isLocked();

      $result['permissions']['can_comment'] = $this->canComment($user);
    } // describe

    /**
     * Describe comment related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['comments_url'] = $this->getUrl();
      $result['comments_count'] = $this->count($user);

      $result['is_locked'] = $this->isLocked();

      if($detailed) {
        $result['permissions']['can_comment'] = $this->canComment($user);
      } // if
    } // describeForApi

    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------
    
    /**
     * Returns true if comments are locked for parent object
     * 
     * @return boolean
     */
    function isLocked() {
      return $this->object->getIsLocked();
    } // isLocked
    
    /**
     * Quickly create and submit a comment
     * 
     * Additional features:
     * 
     * - set_source - Set comment source, default is web
     * - attach_uploaded_files - TRUE by default
     * - complete_if_open - FALSE by default
     * - reopen_if_completed - FALSE by default
     * - log_creation - TRUE by default
     * - subscribe_author - TRUE by default
     * - subscribe_users - Optional list of user ID-s that need to be subscribed
     * - notify_subscribers - TRUE by default
     * - attach_files - array of files info to attach to comment - array(path,filename,type) - used in 'create new comment' incoming mail action
     * - created_on - created on datetime - used import comments from external source (i.e. basecamp)
     *
     * @param string $body
     * @param IUser $by
     * @param array $additional
     * @throws Exception
     * @return Comment
     * @throws Exception
     */
    function submit($body, IUser $by, $additional = null) {
      try {
        DB::beginWork('Creating comment @ ' . __CLASS__);
        
        $comment = $this->newComment();
        
        if($additional && isset($additional['comment_attributes']) && $additional['comment_attributes']) {
          $comment->setAttributes($additional['comment_attributes']);
        } // if

        if(isset($additional['created_on']) && $additional['created_on']) {
          $comment->setCreatedOn($additional['created_on']);
        } //if

        // strip any div tags since they do not belong in comment and cause weird bugs
        // $body = HTML::stripSingleTag('div', $body);

        $comment->setBody($body);
        $comment->setCreatedBy($by);
        
        $comment->setState(($this->object instanceof IState ? $this->object->getState() : STATE_VISIBLE));
        $comment->setIpAddress(AngieApplication::getVisitorIp());
        
        //for incoming mail - new comment action
        $to_attach = array_var($additional, 'attach_files', false);
        if($to_attach) {
          $comment->attachments()->attachFromArray($to_attach);
        }//if
        
        if(array_var($additional, 'attach_uploaded_files', true)) {
          $comment->attachments()->attachUploadedFiles($by);
        } // if
        
        if(!array_var($additional, 'log_creation', true)) {
          $comment->activityLogs()->gag();
        } // if

        $comment->save();
        
        if($this->object instanceof ISubscriptions && array_var($additional, 'subscribe_author', true)) {
          $this->object->subscriptions()->subscribe($by);
        } // if

        $parent_fields = array_var($additional, 'update_parent', null);
        if (is_foreachable($parent_fields)) {
          
          // Completion status control
          $assignee_updated = false;
          if($this->object instanceof IComplete && isset($parent_fields['is_completed']) && $this->object->complete()->canChangeStatus($by)) {
            if(isset($additional['reopen_if_completed'])) {
              unset($additional['reopen_if_completed']); // Ignore, since we have comment property as direct override
            } // if

            $is_completed = array_var($parent_fields, 'is_completed', false);
            if ($is_completed && !$this->object->complete()->isCompleted()) {
              $this->object->complete()->complete($by, $comment);
            } else if (!$is_completed && $this->object->complete()->isCompleted()) {
              $this->object->complete()->open($by, $comment);
            } // if

            $assignee_id = (integer) array_var($parent_fields, 'assignee_id', 0);
            if ($assignee_id !== $this->object->getAssigneeId()) {
              $new_assignee = Users::findById($assignee_id);
              $this->object->assignees()->setAssignee($new_assignee, $by);
              $assignee_updated = true;

              // Make sure that new assignee is subscribed also
              if ($this->object instanceof ISubscriptions && $new_assignee instanceof User) {
                $this->object->subscriptions()->subscribe($new_assignee);
              } // if
            } // if
          } // if
          
          $object_changed = $assignee_updated;
          
          // Label update control
          if ($this->object instanceof ILabel && isset($parent_fields['label_id']) && $this->object->canEdit($by)) {
            $label_id = array_var($parent_fields, 'label_id');
            if ($label_id != $this->object->getLabelId()) {
              $this->object->label()->set($label_id);
              $object_changed = true;
            } // if
          } // if
          
          if ($this->object instanceof ICategory && isset($parent_fields['category_id']) && $this->object->canEdit($by)) {
            $category_id = array_var($parent_fields, 'category_id');
            if ($category_id != $this->object->getCategoryId()) {
              $category = Categories::findById($category_id);
              $this->object->category()->set($category);
              $object_changed = true;
            } // if
          } // if
          
          if ($object_changed) {
            $this->object->save();
          } // if
        } // if

        // Reopen if completed on new comment
        if($this->object instanceof IComplete && $this->object->complete()->isCompleted() && isset($additional['reopen_if_completed']) && $additional['reopen_if_completed']) {
          $this->object->complete()->open($by, $comment);
        } // if
        
        DB::commit('Comment created @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to create comment @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      // Comment is submitted, notify people (any exception thrown by notifier 
      // is not relevant to comment creation process) - except incoming mail
      if($this->object instanceof ISubscriptions && array_var($additional, 'notify_subscribers', true)) {
        AngieApplication::notifications()
          ->notifyAbout('new_comment', $this->object, $by)
          ->setComment($comment)
          ->sendToSubscribers();
      } // if
      
      return $comment;
    } // submit
    
    /**
     * Lock comments
     * 
     * @param IUser $user
     * @param boolean $save
     */
    function lock(IUser $user, $save = true) {
      $this->object->setIsLocked(true);
      
      if($save) {
        $this->object->save();
      } // if
    } // lock
    
    /**
     * Unlock comments
     * 
     * @param IUser $user
     * @param boolean $save
     */
    function unlock(IUser $user, $save = true) {
      $this->object->setIsLocked(false);
      
      if($save) {
        $this->object->save();
      } // if
    } // unlock
    
    /**
     * Clone comment to a $to object
     * 
     * @param IComments $to
     * @throws Exception
     */
    function cloneTo(IComments $to) {
      $comments_table = TABLE_PREFIX . 'comments';
      $attachments_table = TABLE_PREFIX . 'attachments';
      
      $rows = DB::execute("SELECT id, type, source, body, ip_address, state, original_state, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email FROM $comments_table WHERE parent_type = ? AND parent_id = ?", get_class($this->object), $this->object->getId(), ($this->object instanceof IState ? $this->object->getState() : STATE_VISIBLE));
      if($rows) {
        $parent_type = get_class($to);
        $parent_id = $to->getId();
        
        try {
          DB::beginWork('Cloning object comments @ ' . __CLASS__);
          
          $comments_batch = new DBBatchInsert($comments_table, array('parent_type', 'parent_id', 'type', 'source', 'body', 'ip_address', 'state', 'original_state', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email'));
          $attachments_batch = new DBBatchInsert($attachments_table, array('parent_type', 'parent_id', 'type', 'state', 'original_state', 'name', 'mime_type', 'size', 'location', 'md5', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
          
          //$now = DateTimeValue::now()->toMySQL();
          
          foreach($rows as $row) {
            $comment_attachments = DB::execute("SELECT type, state, original_state, name, mime_type, size, location, md5, created_on, created_by_id, created_by_name, created_by_email FROM $attachments_table WHERE parent_type = ? AND parent_id = ? AND state >= ?", $row['type'], $row['id'], $row['state']);
            
            // If we have attachments, we'll need new comment ID so we need to do the insert now
            if($comment_attachments) {
              DB::execute("INSERT INTO $comments_table (parent_type, parent_id, type, source, body, ip_address, state, original_state, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                $parent_type, $parent_id, $row['type'], $row['source'], $row['body'], $row['ip_address'], $row['state'], $row['original_state'], $row['created_on'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email'], $row['updated_on'], $row['updated_by_id'], $row['updated_by_name'], $row['updated_by_email']);
                
              $new_comment_id = DB::lastInsertId();
              foreach($comment_attachments as $comment_attachment) {
                $attachments_batch->insert($row['type'], $new_comment_id, $comment_attachment['type'], $comment_attachment['state'], $comment_attachment['original_state'], $comment_attachment['name'], $comment_attachment['mime_type'], $comment_attachment['size'], $comment_attachment['location'], $comment_attachment['md5'], $comment_attachment['created_on'], $comment_attachment['created_by_id'], $comment_attachment['created_by_name'], $comment_attachment['created_by_email']);
              } // foreach
              
            // No attachments? Add comment to batch
            } else {
              $comments_batch->insert($parent_type, $parent_id, $row['type'], $row['source'], $row['body'], $row['ip_address'], $row['state'], $row['original_state'], $row['created_on'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email'], $row['updated_on'], $row['updated_by_id'], $row['updated_by_name'], $row['updated_by_email']);
            } // if
          } // foreach
          
          $comments_batch->done();
          $attachments_batch->done();
          
          DB::commit('Object comments cloned @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to clone object comments @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // cloneTo
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can post a comment to this object
     *
     * @param IUser $user
     * @return boolean
     */
    function canComment(IUser $user) {
      if($this->isLocked() || ($this->object instanceof IState && $this->object->getState() < STATE_VISIBLE))  {
        return false;
      } // if
      
      return $this->object->canView($user);
    } // canComment
    
    /**
     * Returns true if user can change locked state of the object
     *
     * @param IUser $user
     * @return boolean
     */
    function canChangeLockedState(IUser $user) {
      return $this->object->canEdit($user);
    } // canChangeLockedState
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return post comment URL
     *
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_comments', $this->object->getRoutingContextParams());
    } // getUrl
    
    /**
     * Return post comment URL
     *
     * @return string
     */
    function getPostUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_comments_add', $this->object->getRoutingContextParams());
    } // getPostUrl
    
    /**
     * Return post comment via portal URL
     *
     * @param Portal $portal
     * @return string
     */
    function getPostViaPortalUrl($portal) {
      return Router::assemble($this->object->getRoutingContext() . '_portal_comments_add', $this->object->getRoutingContextParams());
    } // getPostViaPortalUrl
    
    /**
     * Return lock URL
     *
     * @return string
     */
    function getLockUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_comments_lock', $this->object->getRoutingContextParams());
    } // getLockUrl
    
    /**
     * Return unlock URL
     *
     * @return string
     */
    function getUnlockUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_comments_unlock', $this->object->getRoutingContextParams());
    } // getLockUrl
    
  }