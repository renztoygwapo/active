<?php

  /**
   * Project assets manager class
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class ProjectAssets extends ProjectObjects {
    
    // Sharing contexts
    const FILES_SHARING_CONTEXT = 'files';
    const YOUTUBE_SHARING_CONTEXT = 'youtube';
    const DOCUMENTS_SHARING_CONTEXT = 'text-documents';
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access assets section in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'file', ($check_tab ? 'files' : null));
    } // canAccess
    
    /**
     * Returns true if $user can add assets to $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'file', ($check_tab ? 'files' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage assets in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'file', ($check_tab ? 'files' : null));
    } // canManage
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
  	
  	/**
  	 * Cached array of detailed types
  	 * 
  	 * @var array
  	 */
  	private static $asset_types_detailed = false;
    
    /**
     * Cached array of asset types
     *
     * @var array
     */
    private static $asset_types = false;
    
    /**
     * Get asset types with details
     * 
     * @return array
     */
		static function getAssetTypesDetailed() {
			if (self::$asset_types_detailed === false) {
	      $asset_types = array(
	      	'File' => array(
	      		'title' => lang('Files'),
	      		'icon' => AngieApplication::getImageUrl('file-types/16x16/default.png', ENVIRONMENT_FRAMEWORK)
	      	),
	      	'TextDocument' => array(
	      		'title' => lang('Text Documents'),
	      		'icon' => AngieApplication::getImageUrl('icons/16x16/text-document.png', FILES_MODULE)
	      	)
	     	);
	      EventsManager::trigger('on_asset_types', array(&$asset_types));
	      self::$asset_types_detailed = $asset_types;
			} // if
			
			return self::$asset_types_detailed;
		} // getAssetTypesDetailed
    
    /**
     * Return asset type name map
     *
     * @return array
     */
    static function getTypeNameMap() {
			$map = array();
			
			foreach (ProjectAssets::getAssetTypesDetailed() as $type => $details) {
				$map[$type] = array_var($details, 'title');
			} // foreach
			
			return $map;
    } // getTypeNameMap
    
    /**
     * Get asset types
     * 
     * @return array;
     */
    static function getAssetTypes() {
			if (self::$asset_types === false) {
				$type_map = ProjectAssets::getAssetTypesDetailed();
				self::$asset_types = array_keys($type_map);
			} // if
			return self::$asset_types;
    } // getAssetTypes

    /**
     * Return asset created on dates map
     *
     * @param Project $project
     * @param IUser $user
     * @param integer $state
     * @return array
     */
    static function getCreatedOnDatesMap(Project $project, IUser $user, $state = STATE_VISIBLE) {
      $created_on_dates_map = array(
        'today' => 'Today',
        'yesterday' => 'Yesterday'
      );

      $project_assets = self::findByProject($project, $state, $user->getMinVisibility());
      if(is_foreachable($project_assets)) {
        foreach($project_assets as $project_asset) {
          $created_on_date = DateValue::makeFromTimestamp($project_asset->getCreatedOn()->getTimestamp());

          if(!$created_on_date->isToday() && !$created_on_date->isYesterday()) {
            $created_on_dates_map[$created_on_date->toMySQL()] = $created_on_date->toMySQL();
          } // if
        } // foreach
      } // if

      return $created_on_dates_map;
    } // getCreatedOnDatesMap

    /**
     * Return asset updated on dates map
     *
     * @param Project $project
     * @param IUser $user
     * @param integer $state
     * @return array
     */
    static function getUpdatedOnDatesMap(Project $project, IUser $user, $state = STATE_VISIBLE) {
      $updated_on_dates_map = array(
        'today' => 'Today',
        'yesterday' => 'Yesterday'
      );

      $project_assets = self::findByProject($project, $state, $user->getMinVisibility());
      if(is_foreachable($project_assets)) {
        foreach($project_assets as $project_asset) {
          $updated_on = $project_asset->getUpdatedOn();

          if($updated_on instanceof DateTimeValue) {
            $updated_on_date = DateValue::makeFromTimestamp($updated_on->getTimestamp());

            if(!$updated_on_date->isToday() && !$updated_on_date->isYesterday()) {
              $updated_on_dates_map[$updated_on_date->toMySQL()] = $updated_on_date->toMySQL();
            } // if
          } // if
        } // foreach
      } // if

      return $updated_on_dates_map;
    } // getUpdatedOnDatesMap
    
    /**
     * Make sure that $name is unique in $project
     * 
     * This function will return unique name for a given project, based on the 
     * name provided as first parameter. If original name is already unique, it 
     * will not be modified
     * 
     * @param string $name
     * @param Project $project
     * @return string
     */
    static function checkNameUniqueness($name, $project) {
      $project_id = $project instanceof Project ? $project->getId() : $project;
      $asset_types = self::getAssetTypes();
      
      $counter = 1;
      
      $check_name = $name;
      while(DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND name = ? AND type IN (?) AND state >= ?', $project_id, $check_name, $asset_types, STATE_TRASHED) > 0) {
        $dot_pos = strpos_utf($name, '.');
        
        if($dot_pos === false || $dot_pos == 0) {
          $check_name = $name . '-' . $counter++;
        } else {
          $check_name = substr_utf($name, 0, $dot_pos) . '-' . ($counter++) . substr_utf($name, $dot_pos);
        } // if
      } // while
      
      return $check_name;
    } // checkNameUniqueness
    
    /**
     * Return assets by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::findByTypeAndProject($project, self::getAssetTypes(), $min_state, $min_visibility);
    } // findByProject
    
    /**
     * Return assets by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::find(array(
        'conditions' => array('milestone_id = ? AND project_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), self::getAssetTypes(), $min_state, $min_visibility), // Milestone ID + Project ID (integrity issue from activeCollab 2)
        'order' => 'created_on DESC',
      ));
    } // findByMilestone
    
    /**
     * Return assets from a given category
     * 
     * @param AssetCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findByCategory(AssetCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::find(array(
        'conditions' => array('category_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $category->getId(), self::getAssetTypes(), $min_state, $min_visibility),
        'order' => 'created_on DESC',
      ));
    } // findByCategory
    
    /**
     * Return number of assets from a given category
     * 
     * @param AssetCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    static function countByCategory(AssetCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::count(array('category_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $category->getId(), self::getAssetTypes(), $min_state, $min_visibility));
    } // countByCategory
    
    /**
     * Return assets by type in a given project
     * 
     * $type can be a single type name or array of types
     * 
     * @param Project $project
     * @param string $type
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findByTypeAndProject(Project $project, $type, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::find(array(
        'conditions' => array('project_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $project->getId(), $type, $min_state, $min_visibility),
        'order' => 'created_on DESC',
      ));
    } // findByTypeAndProject
    
    /**
     * Return recent assets by type in a given project
     * 
     * @param Project $project
     * @param string $type
     * @param integer $recent_assets_num
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findRecentByTypeAndProject(Project $project, $type, $recent_assets_num = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::find(array(
        'conditions' => array('project_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $project->getId(), $type, $min_state, $min_visibility),
        'order' => 'created_on DESC',
        'limit' => $recent_assets_num
      ));
    } // findRecentByTypeAndProject
    
    /**
     * Return archived assets by type in a given project
     *
     * @param Project $project
     * @param string $type
     * @param integer $state
     * @param integer $min_visibility
     * @return array
     */
    static function findArchivedByTypeAndProject(Project $project, $type, $state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectAssets::find(array(
        'conditions' => array('project_id = ? AND type IN (?) AND state = ? AND visibility >= ?', $project->getId(), $type, $state, $min_visibility),
        'order' => 'created_on DESC'
      ));
    } // findArchivedByProject

    /**
     * Return active assets by type in a given project
     *
     * $type can be a single type name or array of types
     *
     * @param Milestone $milestone
     * @param string $type
     * @param User $user
     * @return array
     */
    static function findActiveByMilestoneAndType(Milestone $milestone, $type, User $user) {
      return ProjectAssets::find(array(
        'conditions' => array('milestone_id = ? AND project_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), $type, STATE_VISIBLE, $user->getMinVisibility()),
        'order' => 'created_on DESC'
      ));
    } // findActiveByMilestoneAndType
        
    /**
     * Find assets for objects list
     * 
     * @param Project $project
     * @param IUser $user
     * @param int $state
     * @return array
     */
    static function findForObjectsList(Project $project, IUser $user, $state = STATE_VISIBLE) {
      $result = array();
      
			$asset_types = ProjectAssets::getAssetTypes();
			
			if(is_foreachable($asset_types)) {
			  $assets = DB::execute("SELECT id, type, name, LOWER(SUBSTRING(name, 1, 1)) AS first_letter, category_id, milestone_id, integer_field_1 AS version_num, state, visibility, created_on, updated_on FROM " . TABLE_PREFIX . "project_objects WHERE type IN (?) AND project_id = ? AND state = ? AND visibility >= ? ORDER BY name", $asset_types, $project->getId(), $state, $user->getMinVisibility());
      
      	if ($assets instanceof DBResult) {
      	  $assets->setCasting(array(
      	    'id' => DBResult::CAST_INT, 
      	    'category_id' => DBResult::CAST_INT, 
      	    'milestone_id' => DBResult::CAST_INT, 
      	    'version_num' => DBResult::CAST_INT
      	  ));

          $asset_types_detailed = ProjectAssets::getAssetTypesDetailed();

          $icons = array();
          $asset_urls = array();

          foreach ($asset_types as $type) {
            $type_lower = strtolower($type);
            $asset_urls[$type_lower] = Router::assemble('project_assets_' . Inflector::underscore($type), array('project_slug' => $project->getSlug(), 'asset_id' => '--ASSETID--'));
            $icons[$type_lower] = $asset_types_detailed[$type]['icon'];
          } // foreach
      	  
          foreach ($assets as $asset) {
            $created_on_date_val = dateval($asset['created_on']);
            $created_on_date = $created_on_date_val->toMySQL();

            if($created_on_date_val->isToday()) {
              $created_on_date = 'today';
            } elseif($created_on_date_val->isYesterday()) {
              $created_on_date = 'yesterday';
            } // if

            $updated_on_date_val = dateval($asset['updated_on']);
            $updated_on_date = '';

            if($updated_on_date_val instanceof DateValue) {
              $updated_on_date = $updated_on_date_val->toMySQL();

              if($updated_on_date_val->isToday()) {
                $updated_on_date = 'today';
              } elseif($updated_on_date_val->isYesterday()) {
                $updated_on_date = 'yesterday';
              } // if
            } // if

            $lowercase_type = strtolower($asset['type']);

            $result[] = array(
              'id'              => $asset['id'],
              'name'            => $asset['name'],
              'category_id'     => $asset['category_id'],
              'milestone_id'    => $asset['milestone_id'],
              'first_letter'    => Inflector::transliterate(strtolower_utf($asset['first_letter'])),
              'created_on_date' => $created_on_date,
              'updated_on_date' => $updated_on_date,
              'type'            => $asset['type'],
              'icon'            => $lowercase_type == 'file' ? get_file_icon_url($asset['name'], '16x16') : (isset($icons[$lowercase_type]) ? $icons[$lowercase_type] : ''),
              'permalink'       => str_replace('--ASSETID--', $asset['id'], $asset_urls[$lowercase_type]),
              'is_archived'     => $asset['state'] == STATE_ARCHIVED ? 1 : 0,
              'is_favorite'     => Favorites::isFavorite(array($asset['type'], $asset['id']), $user),
              'visibility'      => $asset['visibility']
            );
          } // foreach
      	} // if
			} // if
    	
      return $result;
    } // findForObjectsList

    /**
     * Find all tasks in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param int $changes_since
     * @param string $types
     * @return array
     */
    static function findForExport(Project $project, User $user, &$parents_map, $changes_since, $types = null) {
      $result = array();

      if(ProjectAssets::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        if(is_null($types)) {
          $asset_types = ProjectAssets::getAssetTypes();
        } else {
          $asset_types = $types;
        } // if

        if(is_foreachable($asset_types)) {
          $additional_condition = '';
          if(!is_null($changes_since)) {
            $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
            $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
          } // if

          $assets = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', project_id, milestone_id, category_id, state, visibility, priority, created_by_id, created_on, updated_by_id, updated_on, is_locked, varchar_field_1 AS mime_type, varchar_field_2 AS location, varchar_field_3 AS md5, integer_field_1 AS version_num, integer_field_2 AS size, version FROM $project_objects_table WHERE type IN (?) AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY name", $asset_types, $project->getId(), STATE_ARCHIVED, $user->getMinVisibility());

          if($assets instanceof DBResult) {
            $assets->setCasting(array(
              'id' => DBResult::CAST_INT,
              'body_formatted' => function($in) {
                return HTML::toRichText($in);
              },
              'version_num' => DBResult::CAST_INT,
              'size' => DBResult::CAST_INT,
              'project_id' => DBResult::CAST_INT,
              'milestone_id' => DBResult::CAST_INT,
              'category_id' => DBResult::CAST_INT,
              'created_by_id' => DBResult::CAST_INT,
              'updated_by_id' => DBResult::CAST_INT
            ));

            $asset_types_detailed = ProjectAssets::getAssetTypesDetailed();

            $icons = array();
            $asset_urls = array();

            foreach ($asset_types as $type) {
              $type_lower = strtolower($type);
              $asset_urls[$type_lower] = Router::assemble('project_assets_' . Inflector::underscore($type), array('project_slug' => $project->getSlug(), 'asset_id' => '--ASSETID--'));
              $icons[$type_lower] = $asset_types_detailed[$type]['icon'];
            } // foreach

            foreach($assets as $asset) {
              $lowercase_type = strtolower($asset['type']);

              $result[] = array(
                'id'              => $asset['id'],
                'name'            => $asset['name'],
                'body'            => $asset['body'],
                'body_formatted'  => $asset['body_formatted'],
                'type'            => $asset['type'],
                'icon'            => strtolower($asset['type']) == 'file' ? get_file_icon_url($asset['name'], '16x16') : (isset($icons[$lowercase_type]) ? $icons[$lowercase_type] : ''),
                'version_num'     => $asset['version_num'],
                'location'        => $asset['location'],
                'size'            => $asset['size'],
                'mime_type'       => $asset['mime_type'],
                'md5'             => $asset['md5'],
                'project_id'      => $asset['project_id'],
                'milestone_id'    => $asset['milestone_id'],
                'category_id'     => $asset['category_id'],
                'state'           => $asset['state'],
                'visibility'      => $asset['visibility'],
                'priority'        => $asset['priority'],
                'created_by_id'   => $asset['created_by_id'],
                'created_on'      => $asset['created_on'],
                'updated_by_id'   => $asset['updated_by_id'],
                'updated_on'      => $asset['updated_on'],
                'is_locked'       => $asset['is_locked'],
                'permalink'       => str_replace('--ASSETID--', $asset['id'], $asset_urls[$lowercase_type]),
                'version'         => $asset['version']
              );

              $parents_map[$asset['type']][] = $asset['id'];
            } // foreach
          } // if
        } // if
      } // if
      
      return $result;
    } // findForExport

    /**
     * Find all project assets in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param string $output_file
     * @param array $parents_map
     * @param int $changes_since
     * @param string $types
     * @return array
     * @throws Error
     */
    static function exportToFileByProject(Project $project, User $user, $output_file, &$parents_map, $changes_since, $types = null) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // Open json array
      fwrite($output_handle, '[');

      $count = 0;
      if(ProjectAssets::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        if(is_null($types)) {
          $asset_types = ProjectAssets::getAssetTypes();
        } else {
          $asset_types = $types;
        } // if

        if(is_foreachable($asset_types)) {
          $additional_condition = '';
          if(!is_null($changes_since)) {
            $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
            $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
          } // if

          $assets = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', project_id, milestone_id, category_id, state, visibility, priority, created_by_id, created_on, updated_by_id, updated_on, is_locked, varchar_field_1, varchar_field_2, varchar_field_3, integer_field_1, integer_field_2, datetime_field_1, version FROM $project_objects_table WHERE type IN (?) AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY name", $asset_types, $project->getId(), (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED, $user->getMinVisibility());

          if($assets instanceof DBResult) {
            $assets->setCasting(array(
              'id' => DBResult::CAST_INT,
              'body_formatted' => function($in) {
                return HTML::toRichText($in);
              },
              'version_num' => DBResult::CAST_INT,
              'size' => DBResult::CAST_INT,
              'project_id' => DBResult::CAST_INT,
              'milestone_id' => DBResult::CAST_INT,
              'category_id' => DBResult::CAST_INT,
              'created_by_id' => DBResult::CAST_INT,
              'updated_by_id' => DBResult::CAST_INT
            ));

            $asset_types_detailed = ProjectAssets::getAssetTypesDetailed();

            $icons = array();
            $asset_urls = array();

            foreach ($asset_types as $type) {
              $type_lower = strtolower($type);
              $asset_urls[$type_lower] = Router::assemble('project_assets_' . Inflector::underscore($type), array('project_slug' => $project->getSlug(), 'asset_id' => '--ASSETID--'));
              $icons[$type_lower] = $asset_types_detailed[$type]['icon'];
            } // foreach

            $buffer = '';
            foreach($assets as $asset) {
              if($count > 0) $buffer .= ',';

              $record = array(
                'id'              => $asset['id'],
                'name'            => $asset['name'],
                'body'            => $asset['body'],
                'body_formatted'  => $asset['body_formatted'],
                'type'            => $asset['type'],
                'icon'            => strtolower($asset['type']) == 'file' ? get_file_icon_url($asset['name'], '16x16') : array_var($icons, strtolower($asset['type']), ''),
                'project_id'      => $asset['project_id'],
                'milestone_id'    => $asset['milestone_id'],
                'category_id'     => $asset['category_id'],
                'state'           => $asset['state'],
                'visibility'      => $asset['visibility'],
                'priority'        => $asset['priority'],
                'created_by_id'   => $asset['created_by_id'],
                'created_on'      => $asset['created_on'],
                'updated_by_id'   => $asset['updated_by_id'],
                'updated_on'      => $asset['updated_on'],
                'is_locked'       => $asset['is_locked'],
                'permalink'       => str_replace('--ASSETID--', $asset['id'], array_var($asset_urls, strtolower($asset['type']))),
                'version'         => $asset['version'],
                'is_favorite'     => Favorites::isFavorite(array($asset['type'], $asset['id']), $user),
              );

              switch($asset['type']) {
                case 'File':
                  $record['large_url'] = AngieApplication::getFileIconUrl($asset['name'], '48x48');

                  $record['mime_type'] = $asset['varchar_field_1'];
                  $record['location'] = $asset['varchar_field_1'];
                  $record['md5'] = $asset['varchar_field_3'];

                  $record['version_num'] = $asset['integer_field_1'];
                  $record['size'] = $asset['integer_field_2'];
                  $record['last_version_by_id'] = $asset['integer_field_3'];

                  $record['last_version_on'] = $asset['datetime_field_1'];

                  break;

                case 'TextDocument':
                  $record['version_num'] = $asset['integer_field_1'];
                  $record['last_version_by_id'] = $asset['integer_field_2'];
                  $record['last_version_on'] = $asset['datetime_field_1'];
                  break;
              } // switch

              $buffer .= JSON::encode($record);

              if($count % 15 == 0 && $count > 0) {
                fwrite($output_handle, $buffer);
                $buffer = '';
              } // if

              $parents_map[$asset['type']][] = $asset['id'];
              $count++;
            } // foreach

            if($buffer) {
              fwrite($output_handle, $buffer);
            } // if
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
     * Find assets for printing by grouping and filtering criteria
     * 
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @param string $group_by
     * @param array $filter_by
     * @return DBResult
     */
    public function findForPrint(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $group_by = null, $filter_by = null) {
      $conditions = array(
      	DB::prepare('(project_id = ? AND type in ("File","TextDocument") AND state = ? AND visibility >= ?)', $project->getId(), $min_state, $min_visibility),
      );
      
      if (!in_array($group_by, array('milestone_id', 'category_id'))) {
      	$group_by = null;
      } // if
                
      // filter by completion status
      $filter_is_completed = array_var($filter_by, 'is_archived', null);
      if ($filter_is_completed === '0') {
        $conditions[] = DB::prepare('(state = ?)', STATE_VISIBLE);
      } else if ($filter_is_completed === '1') {
      	$conditions[] = DB::prepare('(state = ?)', STATE_ARCHIVED);
      } // if
      
      // filter by completion status
      $filter_type = array_var($filter_by, 'type', null);
      
      if ($filter_type != '') {
        $conditions[] = DB::prepare('(type = ?)', $filter_type);
      }//if
      
      // do find assets
      $assets = ProjectAssets::find(array(
      	'conditions' => implode(' AND ', $conditions),
      	'order' => ($group_by ? $group_by . ', ' : '') . 'name'
      ));
    	
    	return $assets;
    } // findForPrint

    /**
     * Get all items from result and describes array for paged list
     *
     * @param DBResult $result
     * @param Project $active_project
     * @param User $logged_user
     * @param int $items_limit
     * @return Array
     */
    static function getDescribedFileArray(DBResult $result, Project $active_project, User $logged_user, $items_limit = null) {
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

          $file = array();
          // File Details
          $file['id'] = $row['id'];
          $file['name'] = clean($row['name']);
          $file['is_favorite'] = Favorites::isFavorite(array('Discussion', $file['id']), $logged_user);

          // Favorite
          $favorite_params = $logged_user->getRoutingContextParams();
          $favorite_params['object_type'] = $row['type'];
          $favorite_params['object_id'] = $row['id'];

          // Urls
          $file['urls']['remove_from_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_remove_from_favorites', $favorite_params);
          $file['urls']['add_to_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_add_to_favorites', $favorite_params);
          $file['urls']['view'] = Router::assemble('project_assets_file', array('project_slug' => $active_project->getSlug(), 'asset_id' => $row['id']));
          $file['urls']['edit'] = Router::assemble('project_assets_file_edit', array('project_slug' => $active_project->getSlug(), 'asset_id' => $row['id']));
          $file['urls']['trash'] = Router::assemble('project_assets_file_trash', array('project_slug' => $active_project->getSlug(), 'asset_id' => $row['id']));

          // CRUD

          $file['permissions']['can_edit'] = ProjectAssets::canManage($logged_user, $active_project);
          $file['permissions']['can_trash'] = ProjectAssets::canManage($logged_user, $active_project);

          // User & datetime details

          $file['created_on'] = datetimeval($row['created_on']);
          $file['last_commented_on'] = datetimeval($row['datetime_field_1']);

          if($row['created_by_id']) {
            $file['created_by'] = $users_array[$row['created_by_id']];
          } elseif($row['created_by_email']) {
            $file['created_by'] = new AnonymousUser($row['created_by_name'], $row['created_by_email']);
          } else {
            $file['created_by'] = null;
          } // if
          $return_value[] = $file;

          if (count($return_value) === $items_limit) {
            break;
          } //if
        } // foreach
      } //if
      return $return_value;
    } // getDescribedDiscussionArray

    /**
     * Count files by project
     *
     * @param Project $project
     * @param Category $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return number
     */
    static function countFilesByProject(Project $project, $category = null, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      if ($category instanceof TaskCategory) {
        return ProjectAssets::count(array('project_id = ? AND type = ? AND category_id = ? AND state >= ? AND visibility >= ?', $project->getId(), 'File', $category->getId(), $min_state, $min_visibility));
      } else {
        return ProjectAssets::count(array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'File', $min_state, $min_visibility));
      } // if
    } // countByProject
    
  }