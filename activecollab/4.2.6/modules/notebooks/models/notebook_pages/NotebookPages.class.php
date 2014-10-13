<?php

  /**
   * NotebookPages class
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class NotebookPages extends BaseNotebookPages {
  	
    /**
     * Find notebook pages by list of ID-s
     *
     * @param array $ids
     * @param integer $min_state
     * @return NotebookPage[]
     */
    static function findByIds($ids, $min_state = STATE_VISIBLE) {
      return NotebookPages::find(array(
        'conditions' => array('id IN (?) AND state >= ?', $ids, $min_state),
        'order' => 'created_on DESC',
      ));
    } // findByIds

    /**
     * Load notebook pages by $notebook
     *
     * @param Notebook $notebook
     * @param integer $min_state
     * @return NotebookPage[]
     */
    static function findByNotebook(Notebook $notebook, $min_state = STATE_VISIBLE) {
      return NotebookPages::find(array(
        'conditions' =>  array('parent_type = ? AND parent_id = ? AND state >= ?', 'Notebook', $notebook->getId(), $min_state),
        'order' => 'ISNULL(position) ASC, position'
      ));
    } // findByNotebook
    
    /**
     * Find archived notebook pages by given notebook
     *
     * @param Notebook $notebook
     * @return NotebookPage[]
     */
    static function findArchivedByNotebook(Notebook $notebook) {
      return NotebookPages::find(array(
        'conditions' =>  array('parent_type = ? AND parent_id = ? AND state = ?', 'Notebook', $notebook->getId(), STATE_ARCHIVED),
        'order' => 'ISNULL(position) ASC, position'
      ));
    } // findArchivedByNotebook

    /**
     * Return subpages
     *
     * @param NotebookPage $notebook_page
     * @param integer $min_state
     * @return NotebookPage[]
     */
    static function findSubpages(NotebookPage $notebook_page, $min_state = STATE_VISIBLE) {
      return NotebookPages::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ? AND state >= ?', 'NotebookPage', $notebook_page->getId(), $min_state),
        'order' => 'ISNULL(position) ASC, position'
      ));
    } // findSubpages
    
    /**
     * Find children tree - for notebooks listing
     * 
     * @param Notebook|NotebookPage $parent
     * @param IUser $user
     * @param integer $min_state
     * @param boolean $excerpt
     * @param integer $level
     * @return array|null
     */
    static function findForObjectsList($parent, IUser $user, $min_state = STATE_ARCHIVED, $excerpt = false, $level = -1) {
    	if ($parent instanceof Notebook) {
    		$objects = NotebookPages::findByNotebook($parent, $min_state);
    		$notebook_id = $parent->getId();	
    	} else if ($parent instanceof NotebookPage) {
    		$objects = NotebookPages::findSubpages($parent, $min_state);
    		$notebook_id = $parent->getNotebook()->getId();
    	} else {
    		return null;
    	} // if
    	
    	$level ++;
    	
    	if (is_foreachable($objects)) {
    		$return = array();
    		foreach ($objects as $object) {
    			if ($excerpt) {
						$return[] = array(
							'id' => $object->getId(),
							'name' => $object->getName(),
							'parent_id' => $object->getParentId(),
							'notebook_id' => $notebook_id,
							'revision_num' => $object->getVersion(),
							'depth' => $level,
							'is_archived' => $object->getState() == STATE_ARCHIVED ? 1 : 0,
							'permalink'	=> $object->getViewUrl(), 
						  'is_favorite' => Favorites::isFavorite($object, $user), 
						);
    			} else {
	    			$object->depth_level = $level;
	    			$return[] = $object;
    			} // if
    			$children = NotebookPages::findForObjectsList($object, $user, $min_state, $excerpt, $level);
    			if (is_foreachable($children)) {
    				$return = array_merge($return, $children);
    			} // if
    		} // foreach

				return $return;    		
    	} // if
    	
    	return null;
    } // findSubpagesRecursively

    /**
     * Find all notebook pages in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function findForExport(Project $project, User $user, &$parents_map, $changes_since) {
      $result = array();

      if(isset($parents_map['Notebook']) && count($parents_map['Notebook'])) {
        foreach($parents_map['Notebook'] as $notebook_id) {
          $notebook_page_ids = NotebookPages::getAllIdsByNotebook($notebook_id);

          if($notebook_page_ids && is_foreachable($notebook_page_ids)) {
            $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';

            $conditions = array(DB::prepare('(id IN (?))', $notebook_page_ids));
            $conditions = implode(' OR ', $conditions);

            $additional_condition = '';
            if(!is_null($changes_since)) {
              $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
              $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
            } // if

            $notebook_pages = DB::execute("SELECT id, parent_type, parent_id, name, body, body AS 'body_formatted', state, created_by_id, created_on, updated_by_id, updated_on, position, version FROM $notebook_pages_table WHERE ($conditions) AND state >= ? $additional_condition ORDER BY ISNULL(position) ASC, position ASC", STATE_ARCHIVED);

            if($notebook_pages instanceof DBResult) {
              $notebook_pages->setCasting(array(
                'id' => DBResult::CAST_INT,
                'parent_id' => DBResult::CAST_INT,
                'body_formatted' => function($in) {
                  return HTML::toRichText($in);
                },
                'created_by_id' => DBResult::CAST_INT,
                'updated_by_id' => DBResult::CAST_INT
              ));

              $notebook_page_url = Router::assemble('project_notebook_page', array('project_slug' => $project->getSlug(), 'notebook_id' => $notebook_id, 'notebook_page_id' => '--NOTEBOOKPAGEID--'));

              foreach($notebook_pages as $notebook_page) {
                $result[] = array(
                  'id'              => $notebook_page['id'],
                  'parent_type'     => $notebook_page['parent_type'],
                  'parent_id'       => $notebook_page['parent_id'],
                  'name'            => $notebook_page['name'],
                  'body'            => $notebook_page['body'],
                  'body_formatted'  => $notebook_page['body_formatted'],
                  'state'           => $notebook_page['state'],
                  'created_by_id'   => $notebook_page['created_by_id'],
                  'created_on'      => $notebook_page['created_on'],
                  'updated_by_id'   => $notebook_page['updated_by_id'],
                  'updated_on'      => $notebook_page['updated_on'],
                  'position'        => $notebook_page['position'],
                  'version'         => $notebook_page['version'],
                  'permalink'       => str_replace('--NOTEBOOKPAGEID--', $notebook_page['id'], $notebook_page_url)
                );

                $parents_map['NotebookPage'][] = $notebook_page['id'];
              } // foreach
            } // if
          } // if
        } // foreach
      } // if

      return $result;
    } // findForExport

    /**
     * Find all notebook pages in project and prepare them for export
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
      if(isset($parents_map['Notebook']) && count($parents_map['Notebook'])) {
        foreach($parents_map['Notebook'] as $notebook_id) {
          $notebook_page_ids = NotebookPages::getAllIdsByNotebook($notebook_id);

          if($notebook_page_ids && is_foreachable($notebook_page_ids)) {
            $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';

            $conditions = array(DB::prepare('(id IN (?))', $notebook_page_ids));
            $conditions = implode(' OR ', $conditions);

            $additional_condition = '';
            if(!is_null($changes_since)) {
              $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
              $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
            } // if

            $notebook_pages = DB::execute("SELECT id, parent_type, parent_id, name, body, body AS 'body_formatted', state, created_by_id, created_on, updated_by_id, updated_on, position, version FROM $notebook_pages_table WHERE ($conditions) AND state >= ? $additional_condition ORDER BY ISNULL(position) ASC, position ASC", (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED);

            if($notebook_pages instanceof DBResult) {
              $notebook_pages->setCasting(array(
                'id' => DBResult::CAST_INT,
                'parent_id' => DBResult::CAST_INT,
                'body_formatted' => function($in) {
                  return HTML::toRichText($in);
                },
                'created_by_id' => DBResult::CAST_INT,
                'updated_by_id' => DBResult::CAST_INT
              ));

              $notebook_page_url = Router::assemble('project_notebook_page', array('project_slug' => $project->getSlug(), 'notebook_id' => $notebook_id, 'notebook_page_id' => '--NOTEBOOKPAGEID--'));

              $buffer = '';
              foreach($notebook_pages as $notebook_page) {
                if($count > 0) $buffer .= ',';

                $buffer .= JSON::encode(array(
                  'id'              => $notebook_page['id'],
                  'parent_type'     => $notebook_page['parent_type'],
                  'parent_id'       => $notebook_page['parent_id'],
                  'name'            => $notebook_page['name'],
                  'body'            => $notebook_page['body'],
                  'body_formatted'  => $notebook_page['body_formatted'],
                  'state'           => $notebook_page['state'],
                  'created_by_id'   => $notebook_page['created_by_id'],
                  'created_on'      => $notebook_page['created_on'],
                  'updated_by_id'   => $notebook_page['updated_by_id'],
                  'updated_on'      => $notebook_page['updated_on'],
                  'position'        => $notebook_page['position'],
                  'version'         => $notebook_page['version'],
                  'permalink'       => str_replace('--NOTEBOOKPAGEID--', $notebook_page['id'], $notebook_page_url)
                ));

                if($count % 15 == 0 && $count > 0) {
                  fwrite($output_handle, $buffer);
                  $buffer = '';
                } // if

                $parents_map['NotebookPage'][] = $notebook_page['id'];
                $count++;
              } // foreach

              if($buffer) {
                fwrite($output_handle, $buffer);
              } // if
            } // if
          } // if
        } // foreach
      } // if

      // Close json array
      fwrite($output_handle, ']');

      // Close the handle and set correct permissions
      fclose($output_handle);
      @chmod($output_file, 0777);

      return $count;
    } // exportToFileByProject
    
    /**
     * Return ID-s of all subpages, regardless of stricture by notebook
     * 
     * This function is used when we need to fetch all pages while ignoring 
     * structure (search index rebuild etc)
     * 
     * $notebook can be instance of Notebook class or notebook ID
     * 
     * @param Notebook $notebook
     * @return array
     */
    static function getAllIdsByNotebook($notebook) {
      $result = array();
      
      $notebook_id = $notebook instanceof Notebook ? $notebook->getId() : $notebook;
      
      $page_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . "notebook_pages WHERE parent_type = 'Notebook' AND parent_id = ?", $notebook_id);
      if($page_ids) {
        foreach($page_ids as $page_id) {
          $result[] = (integer) $page_id;
          
          $subpage_ids = self::getAllSubpageIds($page_id);
          if(is_foreachable($subpage_ids)) {
            $result = array_merge($result, $subpage_ids);
          } // if
        } // foreach
      } // if
      
      return count($result) ? $result : null;
    } // getAllIdsByNotebook
    
    /**
     * Return all subpage ID-s based on page ID
     * 
     * @param integer $page_id
     * @return array
     */
    static public function getAllSubpageIds($page_id) {
      $result = array();
      
      $subpage_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . "notebook_pages WHERE parent_type = 'NotebookPage' AND parent_id = ?", $page_id);
      if($subpage_ids) {
        foreach($subpage_ids as $subpage_id) {
          $result[] = (integer) $subpage_id;
          
          $sub_subpage_ids = self::getAllSubpageIds($subpage_id);
          if(is_foreachable($sub_subpage_ids)) {
            $result = array_merge($result, $sub_subpage_ids);
          } // if
        } // foreach
      } // if
      
      return $result;
    } // getAllSubpageIds
    
    /**
     * Clone pages from one notebook to another
     * 
     * @param Notebook $from
     * @param Notebook $to
     * @throws Exception
     */
    static function cloneToNotebook(Notebook $from, Notebook $to) {
      try {
        $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
        
        DB::beginWork('Cloning first level pages @ ' . __CLASS__);
        
        $pages = DB::execute("SELECT id, name, body, state, is_locked, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email, position, version FROM $notebook_pages_table WHERE parent_type = ? AND parent_id = ? AND state >= ?", 'Notebook', $from->getId(), STATE_ARCHIVED);
        if($pages) {
          $parent_id = DB::escape($to->getId());
          
          foreach($pages as $page) {
            DB::execute("INSERT INTO $notebook_pages_table (parent_type, parent_id, name, body, state, is_locked, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email, position, version) VALUES ('Notebook', $parent_id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
              $page['name'], $page['body'], $page['state'], $page['is_locked'], $page['created_on'], $page['created_by_id'], $page['created_by_name'], $page['created_by_email'], $page['updated_on'], $page['updated_by_id'], $page['updated_by_name'], $page['updated_by_email'], $page['position'], $page['version'] 
            );
            
            self::clonePages($page['id'], DB::lastInsertId());
          } // foreach
        } // if
        
        DB::commit('Notebook first level pages @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to clone first level pages @ ' . __CLASS__);
        throw $e;
      } // try
    } // cloneToNotebook
    
    /**
     * Clone notebook pages from one page to another page
     * 
     * @param integer $from_page_id
     * @param integer $to_page_id
     * @throws Exception
     */
    static private function clonePages($from_page_id, $to_page_id) {
      try {
        $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
        
        DB::beginWork('Cloning subpages @ ' . __CLASS__);
        
        $pages = DB::execute("SELECT id, name, body, state, is_locked, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email, position, version FROM $notebook_pages_table WHERE parent_type = ? AND parent_id = ? AND state >= ?", 'NotebookPage', $from_page_id, STATE_ARCHIVED);
        if($pages) {
          $parent_id = DB::escape($to_page_id);
          
          foreach($pages as $page) {
            DB::execute("INSERT INTO $notebook_pages_table (parent_type, parent_id, name, body, state, is_locked, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email, position, version) VALUES ('NotebookPage', $parent_id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
              $page['name'], $page['body'], $page['state'], $page['is_locked'], $page['created_on'], $page['created_by_id'], $page['created_by_name'], $page['created_by_email'], $page['updated_on'], $page['updated_by_id'], $page['updated_by_name'], $page['updated_by_email'], $page['position'], $page['version'] 
            );
            
            self::clonePages($page['id'], DB::lastInsertId());
          } // foreach
        } // if
        
        DB::commit('Subpages cloned @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to clone subpages @ ' . __CLASS__);
        throw $e;
      } // try
    } // cloneNotebookPages

    /**
     * Prepare comments for search index
     *
     * @param array $page_ids
     * @param integer $min_state
     * @return array
     */
    static function getCommentsForSearch($page_ids, $min_state = STATE_ARCHIVED) {
      $comments = array();

      if($page_ids) {
        $rows = DB::execute('SELECT parent_id, body FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND parent_id IN (?) AND state >= ? ORDER BY created_on', 'NotebookPage', $page_ids, $min_state);
        if($rows) {
          $rows->setCasting('parent_id', DBResult::CAST_INT);

          foreach($rows as $row) {
            if(isset($comments[$row['parent_id']])) {
              $comments[$row['parent_id']] .= ' ' . $row['body'];
            } else {
              $comments[$row['parent_id']] = $row['body'];
            } // if
          } // foreach
        } // if
      } // if

      return $comments;
    } // getCommentsForSearch

    // ---------------------------------------------------
    //  Trash
    // ---------------------------------------------------
      
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
      return array(
        'notebookpage' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'notebook_pages WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED)
      );
    } // getTrashedMap
    
    /**
     * Find trashed notebook pages
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$query = Trash::getParentQuery($map);

    	if ($query) {
	    	$trashed_notebook_pages = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'notebook_pages WHERE state = ? AND ' . $query . ' ORDER BY created_on DESC', STATE_TRASHED);
    	} else {
	    	$trashed_notebook_pages = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'notebook_pages WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
    	} // if

      if($trashed_notebook_pages) {
        $items = array();

        foreach ($trashed_notebook_pages as $trashed_notebook_page) {
          $items[] = array(
            'id' => $trashed_notebook_page['id'],
            'name' => $trashed_notebook_page['name'],
            'type' => 'NotebookPage',
          );
        } // foreach

        return $items;
      } else {
        return null;
      } // if
    } // findTrashed
    
    /**
     * Delete trashed NotebookPages
     */
    static function deleteTrashed() {
      $notebook_pages = NotebookPages::find(array(
        'conditions' => array('state = ?', STATE_TRASHED)
      ));

      if ($notebook_pages) {
        try {
          DB::beginWork('Deleting trashed notebook pages @ ' . __CLASS__);

          foreach ($notebook_pages as $notebook_page) {
            $notebook_page->state()->delete();
          } // foreach

          DB::commit('Trashed notebook pages have been deleted @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to delete trashed notebook pages @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteTrashed

    /**
     * Force delete notebook pages by notebook IDs
     *
     * @param array $parent_ids
     * @throws Exception
     */
    static function forceDeleteByParents($parent_ids) {
      $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
      $notebook_page_versions_table = TABLE_PREFIX . 'notebook_page_versions';

      try {
        DB::beginWork('Removing notebook pages by parent IDs @ ' . __CLASS__);

        if(is_foreachable($parent_ids)) {
          foreach($parent_ids as $parent_id) {
            $notebook_page_ids = NotebookPages::getAllIdsByNotebook($parent_id);

            if(is_foreachable($notebook_page_ids)) {
              $notebook_pages = array();

              foreach($notebook_page_ids as $notebook_page_id) {
                if (isset($notebook_pages['NotebookPage'])) {
                  $notebook_pages['NotebookPage'][] = (integer) $notebook_page_id;
                } else {
                  $notebook_pages['NotebookPage'] = array((integer) $notebook_page_id);
                } // if
              } // foreach

              DB::execute("DELETE FROM $notebook_pages_table WHERE id IN (?)", $notebook_page_ids);
              DB::execute("DELETE FROM $notebook_page_versions_table WHERE notebook_page_id IN (?)", $notebook_page_ids);

              Comments::deleteByParents($notebook_pages);
              Attachments::deleteByParents($notebook_pages);
              ActivityLogs::deleteByParents($notebook_pages);
              Subscriptions::deleteByParents($notebook_pages);
              Favorites::deleteByParents($notebook_pages);
              ModificationLogs::deleteByParents($notebook_pages);
            } // if
          } // foreach
        } // if

        DB::commit('Notebook pages removed by parent IDs @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove notebook pages by parent IDs @ ' . __CLASS__);
        throw $e;
      } // try
    } // forceDeleteByParents

  }