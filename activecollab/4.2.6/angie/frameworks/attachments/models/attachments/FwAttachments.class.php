<?php

  /**
   * Framework level attachments manager implementation
   *
   * @package angie.frameworks.attachments
   * @subpackage models
   */
  abstract class FwAttachments extends BaseAttachments {
    
    /**
     * Return attachments by parent object
     *
     * @param IAttachments $parent
     * @return Attachment[]
     */
    static function findByParent(IAttachments $parent) {
      return self::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ? AND state >= ?', get_class($parent), $parent->getId(), ($parent instanceof IState ? $parent->getState() : STATE_VISIBLE)),
        'order' => 'created_on',
      ));
    } // findByParent
    
    /**
     * Return number of attachments for a given object
     *
     * @param IAttachments $parent
     * @param IUser $user
     * @param boolean $use_cache
     * @return integer
     */
    static function countByParent(IAttachments $parent, $user = null, $use_cache = true) {
      return AngieApplication::cache()->getByObject($parent, 'attachments_count', function() use ($parent) {
        return (integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'attachments WHERE parent_type = ? AND parent_id = ? AND state >= ?', get_class($parent), $parent->getId(), STATE_ARCHIVED);
      }, !$use_cache);
    } // countByParent

    /**
     * Return disk space usage by parents
     *
     * $parents is an array where key is parent type and value is array of
     * object ID-s of that particular parent
     *
     * @param array $parents
     * @param integer $project_disk_space_usage
     */
    static function getDiscSpaceUsageByParents($parents, &$project_disk_space_usage) {
      if(is_foreachable($parents)) {
        $attachments_table = TABLE_PREFIX . 'attachments';

        $conditions = array();
        foreach($parents as $parent_type => $parent_ids) {
          $conditions[] = DB::prepareConditions(array('(parent_type = ? AND parent_id IN (?))', $parent_type, $parent_ids));
        } // foreach
        $conditions = implode(' OR ', $conditions);

        $attachment_ids = DB::executeFirstColumn("SELECT id FROM $attachments_table WHERE $conditions");

        if($attachment_ids) {
          $project_disk_space_usage += DB::executeFirstCell("SELECT SUM(size) FROM $attachments_table WHERE id IN (?)", $attachment_ids);
        } // if
      } // if
    } // getDiscSpaceUsageByParents
    
    /**
     * Delete records from attachments table that match given $conditions
     *
     * This function also deletes all files from /upload folder so this function
     * is not 100% transaction safe
     *
     * @param mixed $conditions
     * @return boolean
     * @throws Exception
     */
    static function delete($conditions = null) {
      $attachments_table = TABLE_PREFIX . 'attachments';
      $object_contexts_table = TABLE_PREFIX . 'object_contexts';
      
      try {
        DB::beginWork('Deleting attachments @ ' . __CLASS__);
        
        $perpared_conditions = DB::prepareConditions($conditions);
        $where_string = trim($perpared_conditions) == '' ? '' : "WHERE $perpared_conditions";
  
        $rows = DB::execute("SELECT id, location FROM $attachments_table $where_string");
        if(is_foreachable($rows)) {

          // create id => location map
          $attachments = array();
          foreach($rows as $row) {
            $attachments[(integer) $row['id']] = $row['location'];
          } // foreach

          // get attachment ids
          $attachment_ids = array_keys($attachments);

          // delete object contexts for these attachments
          DB::execute("DELETE FROM $object_contexts_table WHERE parent_type = ? AND parent_id IN (?)", 'Attachment', $attachment_ids);

          // delete attachments themselves
          DB::execute("DELETE FROM $attachments_table WHERE id IN (?)", $attachment_ids);

          // delete attachments from disk
          foreach($attachments as $location) {
            @unlink(UPLOAD_PATH . '/' . $location);
          } // foreach
        } // if
        
        DB::commit('Attachments deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete attachments @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return true;
    } // delete
    
    /**
     * Clone attachments
     *
     * To remove all attachments attached to the $destination object
     *
     * @param IAttachments $original
     * @param IAttachments $destination
     * @return boolean
     * @throws Exception
     */
    static function cloneAttachments(IAttachments $original, IAttachments $destination) {
      try {
        DB::beginWork('Cloning attachments @ ' . __CLASS__);
        
        $new_files = array();
        
        $attachments = self::findByParent($original);
        if(is_foreachable($attachments)) {
          $to_insert = array();
          
          foreach($attachments as $attachment) {
            $source_file = $attachment->getFilePath();
            $target_file = AngieApplication::getAvailableUploadsFileName();
            
            if(copy($source_file, $target_file)) {
              $new_files[] = $target_file;
              $to_insert[] = DB::prepare("(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", get_class($destination), $destination->getId(), $attachment->getName(), $attachment->getMimeType(), $attachment->getSize(), basename($target_file), $attachment->getAttachmentType(), $attachment->getCreatedOn(), $attachment->getCreatedById(), $attachment->getCreatedByName(), $attachment->getCreatedByEmail());
            } // if
          } // foreach
          
          if(is_foreachable($to_insert)) {
            DB::execute("INSERT INTO " . TABLE_PREFIX . 'attachments (parent_type, parent_id, name, mime_type, size, location, attachment_type, created_on, created_by_id, created_by_name, created_by_email) VALUES ' . implode(', ', $to_insert));
          } // if
        } // if
        
        DB::commit('Attachments cloned @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to clone attachments @ ' . __CLASS__);
        
        if(is_foreachable($new_files)) {
          foreach($new_files as $new_file) {
            @unlink($new_file);
          } // if
        } // if
        
        throw $e;
      } // try
      
      return true;
    } // cloneAttachments
    
    /**
     * Clean up oprhaned attachments
     *
     * This function deletes all attachments that are older than 2 days and have
     * no parent set
     *
     * @param integer $older_than_days
     * @return boolean
     * @throws Exception
     */
    static function cleanUp($older_than_days = 2) {
      $attachments_table = TABLE_PREFIX . 'attachments';
      $object_contexts_table = TABLE_PREFIX . 'object_contexts';

      $attachment_ids = DB::executeFirstColumn("SELECT id FROM $attachments_table WHERE (parent_id IS NULL OR parent_id = 0) AND created_on < ?", ($older_than_days ? new DateTimeValue('-' . $older_than_days . ' days') : new DateTimeValue()));

      if ($attachment_ids) {
        try {
          DB::beginWork('Attachment cleanup started @ ' . __CLASS__);

          DB::execute("DELETE FROM $attachments_table WHERE id IN (?)", $attachment_ids);
          DB::execute("DELETE FROM $object_contexts_table WHERE parent_type = 'Attachment' AND parent_id IN (?)", $attachment_ids);

          DB::commit('Attachment cleanup finished @ ' . __CLASS__);
        } catch (Exception $e) {
          DB::rollback('Attachment cleanup failed @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // cleanUp
    
    // ---------------------------------------------------
    //  State
    // ---------------------------------------------------
    
    /**
     * Archive subtasks attached to a given parent object
     *
     * @param IAttachments $parent
     * @throws InvalidInstanceError
     */
    static function archiveByParent(IAttachments &$parent) {
      if($parent instanceof IState) {
        $parent->state()->archiveSubitems(TABLE_PREFIX . 'attachments', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()));
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // archiveByParent
    
    /**
     * Unarchive subtasks attached to a given parent object
     *
     * @param IAttachments $parent
     * @throws InvalidInstanceError
     */
    static function unarchiveByParent(IAttachments &$parent) {
      if($parent instanceof IState) {
        $parent->state()->unarchiveSubitems(TABLE_PREFIX . 'attachments', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()));
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // unarchiveByParent
    
    /**
     * Trash subtasks attached to a given parent object
     *
     * @param IAttachments $parent
     * @throws InvalidInstanceError
     */
    static function trashByParent(IAttachments &$parent) {
      if($parent instanceof IState) {
        $parent->state()->trashSubitems(TABLE_PREFIX . 'attachments', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()));
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // trashByParent
    
    /**
     * Restore from trash subtasks attached to a given parent object
     *
     * @param IAttachments $parent
     * @throws InvalidInstanceError
     */
    static function untrashByParent(IAttachments &$parent) {
      if($parent instanceof IState) {
        $parent->state()->untrashSubitems(TABLE_PREFIX . 'attachments', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()));
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // untrashByParent
    
    /**
     * Trash subtasks attached to a given parent object
     *
     * @param IAttachments $parent
     * @param boolean $soft
     * @throws Exception
     */
    static function deleteByParent(IAttachments &$parent, $soft = true) {
      try {
        // remember files we have to delete
        $attachment_files = DB::executeFirstColumn("SELECT location FROM " . TABLE_PREFIX . "attachments WHERE parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId());

        // depending of which mode is selected, perform deletion from database
        if($soft && $parent instanceof IState) {
          $parent->state()->deleteSubitems(TABLE_PREFIX . 'attachments', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()));
        } else {
          self::delete(array("parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId()));
        } // if

        if (is_foreachable($attachment_files)) {
          foreach ($attachment_files as $attachment_file) {
            @unlink(UPLOAD_PATH . '/' . $attachment_file); // remove file from filesystem
          } // foreach
        } // if
      } catch (Exception $e) {
        throw $e;
      } // try

    } // deleteByParent
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     */
    static function deleteByParents($parents) {
      if (is_foreachable($parents)) {
        $conditions = array();
        foreach($parents as $parent_type => $parent_ids) {
          $conditions[] = DB::prepareConditions(array('(parent_type = ? AND parent_id IN (?))', $parent_type, $parent_ids));
        } // foreach
        $conditions = implode(' OR ', $conditions);

        self::delete(array($conditions));
      } // if
    } // deleteByParents
    
    /**
     * Remove attachments by parent types
     * 
     * @param array $types
     */
    static function deleteByParentTypes($types) {
      self::delete(array('parent_type IN (?)', $types));
    } // deleteByParentTypes
    
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
    	$trashed_attachments = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'attachments WHERE state = ?', STATE_TRASHED);
    	    	
    	if (!is_foreachable($trashed_attachments)) {
    		return null;
    	} // if
    	
    	$result = array();
    	
    	foreach ($trashed_attachments as $trashed_attachment) {
    		$type = strtolower($trashed_attachment['type']);
    		
    		if (!isset($result[$type])) {
    			$result[$type] = array();
    		} // if 
    		
    		$result[$type][] = $trashed_attachment['id'];
    	} // foreach
    	
    	return $result;
    } // getTrashedMap
    
    /**
     * Find trashed attachments
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$query = Trash::getParentQuery($map);    	
    	if ($query) {
	    	$trashed_attachments = DB::execute('SELECT id, name, type, parent_id, parent_type FROM ' . TABLE_PREFIX . 'attachments WHERE state = ? AND ' . $query . ' ORDER BY created_on DESC', STATE_TRASHED);
    	} else {
    		$trashed_attachments = DB::execute('SELECT id, name, type, parent_id, parent_type FROM ' . TABLE_PREFIX . 'attachments WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
    	} // if
    	
    	if (!is_foreachable($trashed_attachments)) {
    		return null;
    	} // if
    	
    	$items = array();
    	foreach ($trashed_attachments as $attachment) {
    		$items[] = array(
    			'id' => $attachment['id'],
    			'name' => $attachment['name'],
    			'type' => $attachment['type'],
    		);   		
    	} // foreach
    	
    	return $items;
    } // findTrashed
    
    /**
     * Delete trashed attachments
     * 
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$attachments = Attachments::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($attachments)) {
    		foreach ($attachments as $attachment) {
    			$attachment->state()->delete();
    		} // foreach
    	} // if
    	
    	return true;
    } // deleteTrashed


    /**
     * Does attachment / file has preview
     *
     * @param mixed $item
     * @return boolean
     */
    static function hasPreview($item) {
      return (boolean) self::getPreviewType($item);
    } // hasPreview

    /**
     * Get Preview type
     *
     * @param mixed $item
     * @return string
     */
    static function getPreviewType($item) {
      list($name, $path, $mime) = Attachments::getFileMeta($item);

      // try to determine preview type by mime type
      if (in_array($mime, array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png'))) {
        return DOWNLOAD_PREVIEW_IMAGE;
      } // if

      $file_extension = strtolower(get_file_extension($name));

      // determine preview type by extension
      switch ($file_extension) {
        case 'flv':
        //case 'mp4':
        case 'm4v':
        //case 'f4v':
        //case 'mov':
        //case 'webm':
          return DOWNLOAD_PREVIEW_VIDEO; break;
        case 'mp3':
        //case 'aac':
        case 'm4a':
        //case 'f4a':
        //case 'ogg':
        //case 'oga':
          return DOWNLOAD_PREVIEW_AUDIO; break;
        case 'swf':
          return DOWNLOAD_PREVIEW_FLASH; break;
      } // switch

      return false;
    } // getPreviewType

    /**
     * Get File meta
     *
     * @param mixed $item
     * @return array
     */
    static function getFileMeta($item) {
      if (is_int($item) || ($item instanceof Attachment) || ($item instanceof FileVersion) || ($item instanceof File)) {
        $attachment = is_int($item) ? Attachments::findById($item) : $item;
        $name = $attachment->getName();
        $path = $attachment->getLocation();
        $mime = $attachment->getMimeType();
      } else if (is_array($item)) {
        $name = $item[0];
        $path = $item[1];
        $mime = get_mime_type($path, $name);
      } else if (is_file($item)) {
        $path_info = pathinfo($item);
        $name = $path_info['basename'];
        $path = $item;
        $mime = get_mime_type($path, $name);
      } else {
        $name = null;
        $path = null;
        $mime = null;
      } // if

      return array($name, $path, $mime);
    } // getFileMeta

  }