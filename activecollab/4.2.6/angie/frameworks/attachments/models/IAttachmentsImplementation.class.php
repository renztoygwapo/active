<?php

  /**
   * Object attachments implementation
   *
   * @package angie.framework.attachments
   * @subpackage models
   */
  class IAttachmentsImplementation {
    
    /**
     * Parent instance
     *
     * @var IAttachments
     */
    protected $object;
    
    /**
     * Construct attachments implementaiton and set parent object
     *
     * @param IAttachments $object
     */
    function __construct(IAttachments $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Array of files that are panding to be attached to this object
     *
     * Pending files are attached on save(), not before even if we have loaded
     * object (object ID is known)
     *
     * @var array
     */
    private $pending_upload = array();
    
    /**
     * List of attachments that are pending to have their parent type / ID set
     *
     * @var array
     */
    private $pending_parent = array();
    
    /**
     * List of attachment ID-s that are pending deletion
     *
     * @var array
     */
    private $pending_deletion = array();
    
    /**
     * Is pending files cleanup function registered
     *
     * @var boolean
     */
    private $pending_cleanup_registered = false;
    
    /**
     * Returns true if there are files attached to this object
     *
     * @param IUser $user
     * @return boolean
     */
    function has($user = null) {
      return (boolean) Attachments::countByParent($this->object, $user);
    } // has
    
    /**
     * Return file attachments
     *
     * @param IUser $user
     * @return Attachment[]
     */
    function get(IUser $user) {
      return Attachments::findByParent($this->object);
    } // get
    
    /**
     * Return public attachments
     * 
     * @return Attachment[]
     */
    function getPublic() {
      return Attachments::findByParent($this->object);
    } // getPublic
    
    /**
     * Return number of files attached to parent object
     * 
     * @param IUser $user
     * @param boolean $use_cache
     * @return integer
     */
    function count($user = null, $use_cache = true) {
      return Attachments::countByParent($this->object, $user, $use_cache);
    } // count
    
    /**
     * Create a new attachment instance
     *
     * @return Attachment
     */
    function newAttachment() {
      return new Attachment();
    } // newAttachment
    
    /**
     * Describe attachment related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['attachments_url'] = $this->getUrl();
      
      if($detailed) {
        $result['attachments'] = array();
          
        if($this->get($user)) {
          foreach($this->get($user) as $attachment) {
            $result['attachments'][] = $attachment->describe($user);
          } // foreach
        } // if
        
        $result['attachments_count'] = count($result['attachments']);
      } else {
        $result['attachments_count'] = $this->count($user);
      } // if
    } // describe

    /**
     * Describe attachment related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['attachments_url'] = $this->getUrl();

      if($detailed || $this->object->additionallyDescribeInBriefApiResponse('attachments')) {
        $result['attachments'] = array();

        if($this->get($user)) {
          foreach($this->get($user) as $attachment) {
            $result['attachments'][] = $attachment->describeForApi($user);
          } // foreach
        } // if

        $result['attachments_count'] = count($result['attachments']);
      } else {
        $result['attachments_count'] = $this->count($user);
      } // if
    } // describeForApi
    
    // ---------------------------------------------------
    //  Attach file(s)
    // ---------------------------------------------------
    
    /**
     * Attach file from file system
     *
     * If $name and/or $type are missing they will be extracted from real file
     *
     * If $commit is TRUE, pending files will be commited
     *
     * @param string $path
     * @param string $name
     * @param string $type
     * @param User $user
     * @param boolean $commit
     * @return boolean
     * @throws Error
     */
    function attachFile($path, $name, $type, $user = null, $commit = false) {
      if(is_file($path)) {
       
        $destination_file = AngieApplication::getAvailableUploadsFileName();
        if(copy($path, $destination_file)) {
          $this->addPendingFile($destination_file, $name, $type, filesize($path), $user);
        } else {
          throw new Error('Failed to copy file to uploads folder');
        } // if
        
        if($commit) {
          $this->commitPending();
        } // if
      } // if
    } // attachFile

    /**
     * Attach uploaded file
     *
     * $file is a single element of $_FILES auto global array
     *
     * If $commit is TRUE, pending files will be commited
     *
     * @param $file
     * @param $user
     * @param bool $commit
     * @throws Error
     * @throws UploadError
     */
    function attachUploadedFile($file, $user, $commit = false) {
      if(is_array($file)) {
        if(isset($file['error']) && $file['error'] > 0) {
          throw new UploadError($file['error']);
        } // if
        
        $destination_file = AngieApplication::getAvailableUploadsFileName();
        if(move_uploaded_file($file['tmp_name'], $destination_file)) {
          $this->addPendingFile($destination_file, array_var($file, 'name'), array_var($file, 'type'), array_var($file, 'size'), $user);
        } else {
          throw new Error('Failed to move uploaded file');
        } // if
        
        if($commit) {
          $this->commitPending();
        } // if
      } // if
    } // attachUploadedFile
    
    /**
     * Attach all uploaded files
     *
     * If $commit is set to TRUE, pending files will be commited to database
     *
     * @param User $user
     * @param boolean $commit
     * @return integer
     */
    function attachUploadedFiles($user, $commit = false) {
      $attached = 0;
    
      if(is_foreachable($_FILES)) {
        foreach($_FILES as $file) {
          if(isset($file['error']) && $file['error'] == UPLOAD_ERR_NO_FILE) {
            continue; // No file selected...
          } // if
          
          $this->attachUploadedFile($file, $user);
          $attached++;
        } // foreach
      } // if
      
      if($attached && $commit) {
        $this->commitPending();
      } // if
      
      return $attached;
    } // attachUploadedFiles
    
    /**
     * Attach files from array
     *
     * $from keys are:
     *
     * - path
     * - filename
     * - type
     *
     * If $commit is TRUE, pending files will be commited to database
     *
     * @param array $from
     * @param bool $commit
     * @return int
     */
    function attachFromArray($from, $commit = false) {
      $attached = 0;
      if(is_foreachable($from)) {
        foreach($from as $file) {
         $this->attachFile($file['path'], $file['filename'], $file['type']);
         $attached++;
        } // foreach
      } // if
      if($attached && $commit) {
        $this->commitPending();
      } // if0
      
      return $attached;
    } // attachFromArray
    
    // ---------------------------------------------------
    //  Pending files handling
    // ---------------------------------------------------
    
    /**
     * Add pending file to the list of pending files
     *
     * @param string $location
     * @param string $name
     * @param string $type
     * @param integer $size
     * @param User $user
     */
    private function addPendingFile($location, $name, $type, $size, $user = null) {
      $this->pending_upload[] = array(
        'location' => $location,
        'name' => $name,
        'type' => $type,
        'size' => $size,
        'created_by' => $user,
      ); // array
      
      if(!$this->pending_cleanup_registered) {
        register_shutdown_function(array(&$this, 'clearPending'));
        $this->pending_cleanup_registered = true;
      } // if
    } // addPendingFile
    
    /**
     * Set list of attachment ID-s that will be attached to parent object on
     * commit
     *
     * @param array $ids
     */
    function addPendingParent($ids) {
      if(is_foreachable($ids)) {
        foreach($ids as $id) {
          $this->pending_parent[] = (integer) $id;
        } // foreach
      } elseif($ids) {
        $this->pending_parent[] = $ids;
      } // if
    } // addPendingParent
    
    /**
     * Add attachment ID-s that will be deleted on commit
     *
     * @param array $ids
     */
    function addPendingDeletion($ids) {
      if(is_foreachable($ids)) {
        foreach($ids as $id) {
          $this->pending_deletion[] = (integer) $id;
        } // foreach
      } elseif($ids) {
        $this->pending_deletion[] = $ids;
      } // if
    } // addPendingDeletion

    /**
     * Attach pending files to the object
     */
    function commitPending() {
      try {
        DB::beginWork('Commiting pending files @ ' . __CLASS__);
        
        // Pending upload
        if(is_foreachable($this->pending_upload)) {
          foreach($this->pending_upload as $pending_file) {
            $attachment = $this->newAttachment();
            
            $attachment->setParent($this->object);
            
            if(isset($pending_file['created_by']) && ($pending_file['created_by'] instanceof User || $pending_file['created_by'] instanceof AnonymousUser)) {
              $attachment->setCreatedBy($pending_file['created_by']);
            } else {
              $attachment->setCreatedBy($this->object->getCreatedBy());
            } // if

            $attachment_size = $pending_file['size'];
            
            $attachment->setName($pending_file['name']);
            $attachment->setLocation(basename($pending_file['location']));
            $attachment->setMimeType($pending_file['type']);
            $attachment->setSize($attachment_size);

            if ($attachment_size < (10 * 1024 * 1024)) {
              $attachment->setMd5(md5_file($pending_file['location']));
            } // if

            if ($this->object instanceof IState) {
            	$attachment->setState($this->object->getState());
            	$attachment->setOriginalState(null);
            } else {
              $attachment->setState(STATE_VISIBLE);
            } // if
            
            $attachment->save();
          } // foreach
          $this->pending_upload = array(); // no more pending files
        } // if
        
        // Pending parent
        if(!empty($this->pending_parent)) {
          if ($this->object instanceof IState) {
        		DB::execute('UPDATE ' . TABLE_PREFIX . 'attachments SET type = ?, parent_type = ?, parent_id = ?, state = ?, original_state = ? WHERE id IN (?)', get_class($this->newAttachment()), get_class($this->object), $this->object->getId(), $this->object->getState(), null, $this->pending_parent);
        	} else {
        		DB::execute('UPDATE ' . TABLE_PREFIX . 'attachments SET type = ?, parent_type = ?, parent_id = ? WHERE id IN (?)', get_class($this->newAttachment()), get_class($this->object), $this->object->getId(), $this->pending_parent);
        	} // if
        } // if
        
        // Pending deletion
        if(!empty($this->pending_deletion)) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'attachments WHERE id IN (?)', $this->pending_deletion);
        } // if

        AngieApplication::cache()->removeByModel('attachments');
        
        DB::commit('Commited pending files @ ' . __CLASS__);

        AngieApplication::cache()->removeByObject($this->object, 'attachments_count');
      } catch(Exception $e) {
        DB::rollback('Failed to commit pending files @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // commitPending
    
    /**
     * Clean up pending files
     */
    function clearPending() {
      if(is_foreachable($this->pending_upload)) {
        foreach($this->pending_upload as $pending_file) {
          unlink($pending_file['location']);
        } // foreach
      } // if
      
      $this->pending_upload = array(); // and reset
    } // clearPending
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Clone all attachments from parent object to $to object
     * 
     * @param IAttachments $to
     */
    function cloneTo(IAttachments $to) {
      $rows = DB::execute('SELECT type, state, original_state, name, mime_type, size, location, md5, created_on, created_by_id, created_by_name, created_by_email, raw_additional_properties FROM ' . TABLE_PREFIX . 'attachments WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
      
      if($rows) {
        $to_insert = array();
          
        $parent_type = DB::escape(get_class($to));
        $parent_id = DB::escape($to->getId());
        
        foreach($rows as $row) {
          $location = UPLOAD_PATH . '/' . $row['location'];
          $new_location = AngieApplication::getAvailableUploadsFileName();
          
          if(is_file($location)) {
            copy($location, $new_location);
          } // if
          
          $to_insert[] = DB::prepare("($parent_type, $parent_id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $row['type'], $row['state'], $row['original_state'], $row['name'], $row['mime_type'], $row['size'], basename($location), $row['md5'], $row['created_on'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email'], $row['raw_additional_properties']);
        } // foreach
        
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'attachments (parent_type, parent_id, type, state, original_state, name, mime_type, size, location, md5, created_on, created_by_id, created_by_name, created_by_email, raw_additional_properties) VALUES ' . implode(', ', $to_insert));
      } // if
    } // cloneTo
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return parent attachments URL
     * 
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_attachments', $this->object->getRoutingContextParams());
    } // getUrl
    
  }