<?php

  /**
   * Notebooks manager class
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class Notebooks extends ProjectObjects {

    // Sharing context
    const SHARING_CONTEXT = 'notebook';
    
    /**
     * Returns true if $user can access notebooks section of $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'notebook', ($check_tab ? 'notebooks' : null));
    } // canAccess
    
    /**
     * Returns true if $user can add notebooks to $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'notebook', ($check_tab ? 'notebooks' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage notebooks in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'notebook', ($check_tab ? 'notebooks' : null));
    } // canManage
    
    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------
    
    /**
     * Cached tree info data
     *
     * @var array
     */
    static private $tree_info_cache = array();
    
    /**
     * Return tree information for a given page
     * 
     * This function will return array where first element is notebook ID and 
     * second element is depth at which provided notebook page is
     * 
     * @param NotebookPage $page
     * @return array
     */
    static function getTreeInfoByPage(NotebookPage $page) {
      $page_id = $page->getId();
      
      if(!isset(self::$tree_info_cache[$page_id])) {
        $pages = array($page_id);
        
        $current_page_id = $page_id;
        do {
          $row = DB::executeFirstRow('SELECT parent_type, parent_id FROM ' . TABLE_PREFIX . 'notebook_pages WHERE id = ?', $current_page_id);
          
          if($row['parent_type'] == 'NotebookPage') {
            $current_page_id = (integer) $row['parent_id'];
            
            $pages[] = $current_page_id;
          } // if
        } while($row['parent_type'] == 'NotebookPage');
        
        if($row && $row['parent_type'] == 'Notebook' && $row['parent_id']) {
          $notebook_id = (integer) $row['parent_id'];
        } else {
          $notebook_id = 0;
        } // if
        
        if(count($pages) > 1) {
          $pages = array_reverse($pages);
        } // if
        
        // Now that we have a stack of parent pages, lets cache it
        $counter = 0;
        
        foreach($pages as $parent_page_id) {
          self::$tree_info_cache[$parent_page_id] = array($notebook_id, $counter);
          
          $counter++;
        } // foreach
      } // if
      
      return self::$tree_info_cache[$page_id];
    } // getTreeInfoByPage
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
  	
  	/**
     * Find notebooks by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Notebook', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position ASC'
      ));
    } // findByProject
    
    /**
     * Find archived notebooks by project
     *
     * @param Project $project
     * @param integer $state
     * @param integer $min_visibility
     * @return array
     */
    static function findArchivedByProject(Project $project, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state = ? AND visibility >= ?', $project->getId(), 'Notebook', STATE_ARCHIVED, $min_visibility),
        'order' => 'ISNULL(position) ASC, position ASC'
      ));
    } // findArchivedByProject
    
    /**
     * Count notebooks by project
     * 
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return number
     */
    static function countByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Notebooks::count(array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Notebook', $min_state, $min_visibility));
    } // countByProject
    
    /**
     * Return notebooks by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult|Notebook[]
     */
    static function findByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Notebooks::find(array(
        'conditions' => array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'Notebook', $min_state, $min_visibility), // Milestone ID + Project ID (integrity issue from activeCollab 2)
        'order' => 'ISNULL(position) ASC, position ASC'
      ));
    } // findOpenByMilestone

    /**
     * Find all noteboook in project, and prepare them for objects list
     *
     * @param Project $project
     * @param User $user
     * @param integer $state
     * @return array
     */
    static function findForObjectsList(Project $project, $user, $state = STATE_VISIBLE) {
      $result = array();

      $notebooks = DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = ? AND state = ? AND visibility >= ? ORDER BY ISNULL(position) ASC, position ASC", $project->getId(), 'Notebook', $state, $user->getMinVisibility());
      if (is_foreachable($notebooks)) {
        $notebook_url = Router::assemble('project_notebook', array('project_slug' => $project->getSlug(), 'notebook_id' => '--NOTEBOOKID--'));
        $default_avatar_url  = ROOT_URL . '/notebook_covers/default.145x145.png';

        foreach ($notebooks as $notebook) {
          $notebook_avatar_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/' . '/notebook_covers/' . $notebook['id'] . '.145x145.png';
          $notebook_avatar_url = ROOT_URL . '/notebook_covers/' . $notebook['id'] . '.145x145.png';

          $result[] = array(
            'id' => $notebook['id'],
            'name' => $notebook['name'],
            'permalink' => str_replace('--NOTEBOOKID--', $notebook['id'], $notebook_url),
            'avatar' => array(
              'large' => is_file($notebook_avatar_path) ? $notebook_avatar_url : $default_avatar_url
            )
          );
        } // foreach
      } // if

      return $result;
    } // findForObjectsList

    /**
     * Find all notebooks in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function findForExport(Project $project, User $user, &$parents_map, $changes_since) {
      $result = array();

      if(Notebooks::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $notebooks = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', milestone_id, state, visibility, created_by_id, created_on, updated_by_id, updated_on FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY ISNULL(position) ASC, position ASC", 'Notebook', $project->getId(), STATE_ARCHIVED, $user->getMinVisibility());

        if($notebooks instanceof DBResult) {
          $notebooks->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'milestone_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT
          ));

          $notebook_url = Router::assemble('project_notebook', array('project_slug' => $project->getSlug(), 'notebook_id' => '--NOTEBOOKID--'));
          $default_avatar_url  = ROOT_URL . '/notebook_covers/default.145x145.png';

          foreach($notebooks as $notebook) {
            $notebook_avatar_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/' . '/notebook_covers/' . $notebook['id'] . '.145x145.png';
            $notebook_avatar_url = ROOT_URL . '/notebook_covers/' . $notebook['id'] . '.145x145.png';

            $result[] = array(
              'id'              => $notebook['id'],
              'type'            => $notebook['type'],
              'name'            => $notebook['name'],
              'body'            => $notebook['body'],
              'body_formatted'  => $notebook['body_formatted'],
              'milestone_id'    => $notebook['milestone_id'],
              'state'           => $notebook['state'],
              'visibility'      => $notebook['visibility'],
              'created_by_id'   => $notebook['created_by_id'],
              'created_on'      => $notebook['created_on'],
              'updated_by_id'   => $notebook['updated_by_id'],
              'updated_on'      => $notebook['updated_on'],
              'avatar'          => is_file($notebook_avatar_path) ? base64_encode(file_get_contents($notebook_avatar_url)) : base64_encode(file_get_contents($default_avatar_url)),
              'permalink'       => str_replace('--NOTEBOOKID--', $notebook['id'], $notebook_url)
            );

            $parents_map[$notebook['type']][] = $notebook['id'];
          } // foreach
        } // if
      } // if

      return $result;
    } // findForExport

    /**
     * Find all notebooks in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param string $output_file
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function exportToFileByProject(Project $project, User $user, $output_file, &$parents_map, $changes_since) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // Open json array
      fwrite($output_handle, '[');

      $count = 0;
      if(Notebooks::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $notebooks = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', milestone_id, state, visibility, created_by_id, created_on, updated_by_id, updated_on FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY ISNULL(position) ASC, position ASC", 'Notebook', $project->getId(), (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED, $user->getMinVisibility());

        if($notebooks instanceof DBResult) {
          $notebooks->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'milestone_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT
          ));

          $notebook_url = Router::assemble('project_notebook', array('project_slug' => $project->getSlug(), 'notebook_id' => '--NOTEBOOKID--'));
          $default_avatar_url  = ROOT_URL . '/notebook_covers/default.145x145.png';

          $buffer = '';
          foreach($notebooks as $notebook) {
            $notebook_avatar_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/' . '/notebook_covers/' . $notebook['id'] . '.145x145.png';
            $notebook_avatar_url = ROOT_URL . '/notebook_covers/' . $notebook['id'] . '.145x145.png';

            if($count > 0) $buffer .= ',';

            $buffer .= JSON::encode(array(
              'id'              => $notebook['id'],
              'type'            => $notebook['type'],
              'name'            => $notebook['name'],
              'body'            => $notebook['body'],
              'body_formatted'  => $notebook['body_formatted'],
              'milestone_id'    => $notebook['milestone_id'],
              'state'           => $notebook['state'],
              'visibility'      => $notebook['visibility'],
              'created_by_id'   => $notebook['created_by_id'],
              'created_on'      => $notebook['created_on'],
              'updated_by_id'   => $notebook['updated_by_id'],
              'updated_on'      => $notebook['updated_on'],
              'avatar'          => is_file($notebook_avatar_path) ? base64_encode(file_get_contents($notebook_avatar_url)) : base64_encode(file_get_contents($default_avatar_url)),
              'permalink'       => str_replace('--NOTEBOOKID--', $notebook['id'], $notebook_url)
            ));

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $parents_map[$notebook['type']][] = $notebook['id'];
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
     * Get all items from result and describes array for paged list
     *
     * @param DBResult $result
     * @param Project $active_project
     * @param User $logged_user
     * @param int $items_limit
     * @return Array
     */
    static function getDescribedNotebookArray(DBResult $result, Project $active_project, User $logged_user, $items_limit = null) {
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
          $notebook = array();

          // Notebook Details
          $notebook['id'] = $row['id'];
          $notebook['name'] = clean($row['name']);
          $notebook['is_favorite'] = Favorites::isFavorite(array('Notebook', $notebook['id']), $logged_user);
          $notebook['is_completed'] = (datetimeval($row['completed_on']) instanceof DateTimeValue) ? 1 : 0;

          // Favorite
          $favorite_params = $logged_user->getRoutingContextParams();
          $favorite_params['object_type'] = $row['type'];
          $favorite_params['object_id'] = $row['id'];

          // Urls
          $notebook['urls']['remove_from_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_remove_from_favorites', $favorite_params);
          $notebook['urls']['add_to_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_add_to_favorites', $favorite_params);
          $notebook['urls']['view'] = Router::assemble('project_notebook', array('project_slug' => $active_project->getSlug(), 'notebook_id' => $row['id']));
          $notebook['urls']['edit'] = Router::assemble('project_notebook_edit', array('project_slug' => $active_project->getSlug(), 'notebook_id' => $row['id']));
          $notebook['urls']['trash'] = Router::assemble('project_notebook_trash', array('project_slug' => $active_project->getSlug(), 'notebook_id' => $row['id']));

          // CRUD

          $notebook['permissions']['can_edit'] = Notebooks::canManage($logged_user, $active_project);
          $notebook['permissions']['can_trash'] = Notebooks::canManage($logged_user, $active_project);

          // User & datetime details
          $notebook['created_on'] = datetimeval($row['created_on']);

          if($row['created_by_id']) {
            $notebook['created_by'] = $users_array[$row['created_by_id']];
          } elseif($row['created_by_email']) {
            $notebook['created_by'] = new AnonymousUser($row['created_by_name'], $row['created_by_email']);
          } else {
            $notebook['created_by'] = null;
          } // if
          $return_value[] = $notebook;

          if (count($return_value) === $items_limit) {
            break;
          } // if
        } // foreach
      } // if

      return $return_value;
    } //getDescribedNotebookArray
    
  }