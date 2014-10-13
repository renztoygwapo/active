<?php

  /**
   * ProjectObjectTemplates class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  class ProjectObjectTemplates extends BaseProjectObjectTemplates {

    /**
     * Check if user can add object
     *
     * @param User $user
     * @return bool
     */
    static function canAdd(User $user) {
      return self::canManage($user);
    } // canAdd

    /**
     * Can manage object
     *
     * @param User $user
     * @return bool
     */
    static function canManage(User $user) {
      return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
    } // canManage

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Find project object templates by template id
     *
     * @param ProjectTemplate $template
     * @param null $type
     * @return mixed
     */
    static function findByType(ProjectTemplate $template, $type = null) {
      return self::findBySQL("SELECT * FROM " . TABLE_PREFIX . "project_object_templates WHERE template_id = ? AND type = ?", $template->getId(), $type);
    } // findByTemplateId

    /**
     * Find all object templates for outline
     *
     * @param ProjectTemplate $template
     * @param ProjectObjectTemplate $parent
     * @return array
     */
    static function findObjectsForOutline(ProjectTemplate $template, $parent = null) {
      $results = array();

      if ($parent instanceof ProjectObjectTemplate) {
        switch($parent->getType()) {
          case "Milestone":
            $type = "Task";
            break;
          case "Task":
            $type = "Subtask";
            break;
          default:
            $type = null;
            break;
        } // switch

        $parent_id = $parent->getId() ? $parent->getId() : 0;
        $object_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_object_templates WHERE template_id = ? AND type = ? AND parent_id = ?', $template->getId(), $type, $parent_id);
      } else {
        $object_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_object_templates WHERE template_id = ? AND type = ?', $template->getId(), "Milestone");
      } // if

      if (!$object_ids) {
        return array();
      }

      $objects = DB::execute('SELECT id, type, template_id, parent_id, value, position FROM ' . TABLE_PREFIX . 'project_object_templates WHERE id IN (?) ORDER BY position', $object_ids);

      if (is_foreachable($objects)) {
        // casting
        $objects->setCasting(array(
          'due_on'        => DBResult::CAST_DATE,
          'start_on'      => DBResult::CAST_DATE
        ));

        // urls
        $object_id_prefix_pattern = '--OBJECT-ID--';
        $object_type_prefix_pattern = '--OBJECT-TYPE--';
        $object_url_params = array(
          'template_id' => $template->getId(),
          'object_type' => $object_type_prefix_pattern,
          'object_id' => $object_id_prefix_pattern
        );
        $view_object_url_pattern = Router::assemble('project_object_template', $object_url_params);
        $edit_object_url_pattern = Router::assemble('project_object_template_edit', $object_url_params);
        $delete_object_url_pattern = Router::assemble('project_object_template_delete', $object_url_params);

        foreach($objects as $subobject) {
          $subobject_id = $subobject['id'];
          $subobject_type_lowercase = strtolower($subobject['type']);

          $subobject = array_merge($subobject, unserialize($subobject['value']));

          $other_assignees = null;
          $raw_other_assignee_ids = array_var($subobject, 'other_assignees', null);
          if (is_foreachable($raw_other_assignee_ids)) {
            foreach ($raw_other_assignee_ids as $row_assignee_id) {
              $other_assignees[] = array('id' => $row_assignee_id);
            }
          }

          $data = array(
            'id'                  => $subobject_id,
            'name'                => array_var($subobject, 'name', array_var($subobject, 'body')),
            'body'                => $subobject['body'],
            'priority'            => $subobject['priority'],
            'class'               => $subobject['type'],
            'type'                => $subobject_type_lowercase,
            'start_on'            => $subobject['start_on'],
            'due_on'              => $subobject['due_on'],
            'assignee_id'         => $subobject['assignee_id'],
            'other_assignees'     => $other_assignees,
            'label_id'            => !empty($subobject['label_id']) ? $subobject['label_id'] : null,
            'user_is_subscribed'  => false,
            'estimate'            => null,
            'urls'                => array(
              'view'                => str_replace('--OBJECT-ID--', $subobject_id, str_replace('--OBJECT-TYPE--', $subobject_type_lowercase, $view_object_url_pattern)),
              'edit'                => str_replace('--OBJECT-ID--', $subobject_id, str_replace('--OBJECT-TYPE--', $subobject_type_lowercase, $edit_object_url_pattern)),
              'delete'              => str_replace('--OBJECT-ID--', $subobject_id, str_replace('--OBJECT-TYPE--', $subobject_type_lowercase, $delete_object_url_pattern))
            ),
            'permissions'         => array(
              'can_edit'            => true,
              'can_delete'          => true
            ),
            'name_suffix'         => ''
          );

          switch ($subobject['type']) {
            case "Milestone":
              $start_on = $subobject['start_on'];
              $due_on = $subobject['due_on'];
              if ($start_on) {
                if ($due_on) {
                  $data['name_suffix'] = " " . lang("(:start_on. - :due_on. day)", array('start_on'=>$start_on,'due_on'=>$due_on));
                } // if
              } else {
                if ($due_on) {
                  $data['name_suffix'] = " " . lang("(0. - :due_on. day)", array('start_on'=>$start_on,'due_on'=>$due_on));
                } // if
              } // if

              $data = array_merge($data, array(
                'event_names'     => array(
                  'updated'         => 'milestone_template_updated'
                ),
              ));
              break;
            case "Task":
              $data = array_merge($data, array(
                'category_id'     => $subobject['category_id'],
                'visibility'      => $subobject['visibility'],
                'milestone_id'    => $subobject['parent_id'],
                'event_names'     => array(
                  'updated'         => 'task_template_updated'
                ),
                'estimate'        => array(
                  'value'           => (float) $subobject['estimate_value'],
                  'job_type_id'     => $subobject['estimate_job_type_id'],
                ),
              ));

              break;
            case 'Subtask':
              $data = array_merge($data, array(
                'event_names'     => array(
                  'updated'         => 'subtask_template_updated'
                )
              ));
              $subtask_parent_id = $subobject['parent_id'];
              if ($subtask_parent_id) {
                $data = array_merge($data, array(
                  'parent'          => array(
                    'class'           => 'task',
                    'id'              => $subtask_parent_id
                  )
                ));
              } // if
              break;
          }

          $results[] = $data;
        } // foreach
      } // if

      return $results;
    } // findObjectsForOutline

    /**
     * Find all position templates
     *
     * @param ProjectTemplate $template
     * @return array
     */
    static function findPositionsForList(ProjectTemplate $template) {
      $results = array();

      $positions = DB::execute('SELECT id, value, position FROM ' . TABLE_PREFIX . 'project_object_templates WHERE template_id = ? AND type = ?', $template->getId(), 'Position');

      if (is_foreachable($positions)) {
        // urls
        $position_id_prefix_pattern = '--POSITION-ID--';
        $position_url_params = array('template_id' => $template->getId(), 'object_type' => 'position', 'object_id' => $position_id_prefix_pattern);
        $edit_position_url_pattern = Router::assemble('project_object_template_edit', $position_url_params);
        $delete_position_url_pattern = Router::assemble('project_object_template_delete', $position_url_params);

        foreach ($positions as $subobject) {
          $position_id = $subobject['id'];
          $position_data = unserialize($subobject['value']);
          $assigned = Users::findById($position_data['user_id']);

          $data = array(
            'id'          => $position_id,
            'type'        => "Position",
            'company_id'  => 0,
            'urls'        => array(
              'edit'        => str_replace('--POSITION-ID--', $position_id, $edit_position_url_pattern),
              'delete'      => str_replace('--POSITION-ID--', $position_id, $delete_position_url_pattern)
            ),
            'assigned' => $assigned instanceof User ?
              array(
                'id'      => $assigned->getId(),
                'name'    => $assigned->getName(),
                'avatar'  => $assigned->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)
              ) : false,
            'permissions' => array(
              'can_assign'  => true,
              'can_edit'    => true,
              'can_delete'  => true
            )
          );

          $results[] = array_merge($data, $position_data);
        } // foreach
      } // if

      return $results;
    } // findPositionsForList

    /**
     * Find all task category templates
     *
     * @param ProjectTemplate $template
     * @param null $category_type
     * @return array
     */
    static function findCategoriesForList(ProjectTemplate $template, $category_type = null) {
      $result = array();
      $categories = DB::execute("SELECT id, value, position FROM " . TABLE_PREFIX . "project_object_templates WHERE template_id = ? AND type = ? AND subtype = ?", $template->getId(), "Category", $category_type);
      if (is_foreachable($categories)) {

        // urls
        $category_id_prefix_pattern = '--CATEGORY-ID--';
        $category_url_params = array('template_id' => $template->getId(), 'object_type' => 'category', 'object_id' => $category_id_prefix_pattern);
        $edit_category_url_pattern = Router::assemble('project_object_template_edit', $category_url_params);
        $delete_category_url_pattern = Router::assemble('project_object_template_delete', $category_url_params);

        foreach ($categories as $subobject) {
          $category_id = $subobject['id'];
          $category_data = unserialize($subobject['value']);

          $data = array(
            'id'          => $category_id,
            'type'        => "Category",
            'subtype'     => $subobject['subtype'],
            'urls'        => array(
              'edit'        => str_replace('--CATEGORY-ID--', $category_id, $edit_category_url_pattern),
              'delete'      => str_replace('--CATEGORY-ID--', $category_id, $delete_category_url_pattern)
            ),
            'permissions' => array(
              'can_edit'    => true,
              'can_delete'  => true
            )
          );

          $result[] = array_merge($data, $category_data);
        } // foreach
      } // if

      return $result;
    } // findCategoriesForList

    /**
     * Find all files for template
     *
     * @param ProjectTemplate $template
     * @return array
     */
    static function findFilesForList(ProjectTemplate $template) {
      $result = array();

      $files = DB::execute("SELECT id, value, file_size FROM " . TABLE_PREFIX . "project_object_templates WHERE template_id = ? AND type = ?", $template->getId(), "File");

      if (is_foreachable($files)) {
        // urls
        $file_id_prefix_pattern = '--FILE-ID--';
        $file_url_params = array('template_id' => $template->getId(), 'object_type' => 'file', 'object_id' => $file_id_prefix_pattern);
        // $edit_file_url_pattern = Router::assemble('project_object_template_edit', $file_url_params);
        $delete_file_url_pattern = Router::assemble('project_object_template_delete', $file_url_params);

        foreach ($files as $subobject) {
          $file_id = $subobject['id'];
          $file_data = unserialize($subobject['value']);

          $data = array(
            'id'          => $file_id,
            'type'        => 'File',
            'file_size'   => format_file_size($subobject['file_size']),
            'icon'        => get_file_icon_url(array_var($file_data, "name"), "48x48"),
            'urls'        => array(
              //'edit'        => str_replace('--FILE-ID--', $file_id, $edit_file_url_pattern),
              'delete'      => str_replace('--FILE-ID--', $file_id, $delete_file_url_pattern)
            ),
            'permissions' => array(
              'can_edit'    => true,
              'can_delete'  => true
            )
          );

          $result[] = array_merge($data, $file_data);
        } // foreach
      }

      return $result;
    } // findFilesForList

    /**
     * Get total of of scheduled objects by template
     *
     * @param ProjectTemplate|integer $template
     * @return int
     */
    static function getTotalOfScheduledObjectsByTemplate($template) {
      $template_id = $template instanceof ProjectTemplate ? $template->getId() : $template;

      return (int) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'project_object_templates WHERE template_id = ? AND type IN (?) AND value LIKE ?', $template_id, array('Milestone', 'Task', 'Subtask'), '%specify%');
    } // getTotalOfScheduled

    /**
     * Delete children by parent
     *
     * @param ProjectObjectTemplate $parent
     * @return bool
     * @throws Exception
     */
    static function deleteChildrenByParent(ProjectObjectTemplate $parent) {
      $type = ucfirst(strtolower($parent->getType()));

      if ($type == "Milestone" || $type == "Task") {
        try {
          DB::beginWork('Deleting '.$type.' @ ' . __CLASS__);

          switch($type) {
            case "Milestone":
              // remember task templates we have to delete
              $task_ids = DB::executeFirstColumn("SELECT id FROM " . TABLE_PREFIX . "project_object_templates WHERE parent_id = ? AND type = ?", $parent->getId(), "Task");

              // find all subtask templates
              $subtask_ids = DB::executeFirstColumn("SELECT id FROM " . TABLE_PREFIX . "project_object_templates WHERE parent_id IN (?) AND type = ?", $task_ids, "Subtask");

              // delete subtask templates
              DB::execute("DELETE FROM " . TABLE_PREFIX . "project_object_templates WHERE id IN (?)", $subtask_ids);

              // delete task templates themselves
              DB::execute("DELETE FROM " . TABLE_PREFIX . "project_object_templates WHERE id IN (?)", $task_ids);
              break;
            case "Task":
              // delete subtask templates
              DB::execute("DELETE FROM " . TABLE_PREFIX . "project_object_templates WHERE parent_id = ? AND type = ?", $parent->getId(), "Subtask");
              break;
          } // switch

          DB::commit($type.' deleted @ ' . __CLASS__);
        } catch (Exception $e) {
          DB::rollback('Failed to delete '.$type.' @ ' . __CLASS__);

          throw $e;
        } // try
      } //if

      return true;
    } // deleteByParent
  }