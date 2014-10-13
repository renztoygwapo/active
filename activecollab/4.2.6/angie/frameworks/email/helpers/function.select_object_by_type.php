<?php

  /**
   * Render select object by type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_object_by_type($params, &$smarty) {
    $project = array_required_var($params, 'project', true, 'Project');
    $user = array_required_var($params, 'user', true, 'IUser');

    $type_name = array_var($params, 'type_name', null, true);
    $type_value = array_var($params, 'type_value', IncomingMailCommentAction::ADD_ON_TASK, true);

    $type_id = array_var($params, 'type_id', null, true);
    if(!$type_id) {
      $type_id = $params['id'] = HTML::uniqueId('select_object_type');
    } // if

    $object_id = array_var($params, 'object_id', null, true);
    $name = array_var($params, 'name', null, true);

    $tasks = Tasks::findForSelectBoxByProject($project, 'Task', STATE_VISIBLE, $user->getMinVisibility());
    $discussions = Discussions::findForSelectBoxByProject($project, 'Discussion');
    $text_documents = TextDocuments::findForSelectBoxByProject($project, 'TextDocument');

    if($tasks) {
      $types[IncomingMailCommentAction::ADD_ON_TASK] = lang('Task');
    }
    if($discussions) {
      $types[IncomingMailCommentAction::ADD_ON_DISCUSSION] = lang('Discussion');
    }
    if($text_documents) {
      $types[IncomingMailCommentAction::ADD_ON_TEXT_DOCUMENT] = lang('Text Document');
    }
    $options = array();
    foreach($types as $key => $type) {
      $options[] = HTML::optionForSelect($type, $key, $key == $type_value);
    } // foreach

    $content = HTML::select($type_name, $options, $params);
    unset($params['label']);
    unset($params['id']);

    switch ($type_value) {
      case IncomingMailCommentAction::ADD_ON_TASK:
        $objects = $tasks;
        break;
      case IncomingMailCommentAction::ADD_ON_DISCUSSION:
        $objects = $discussions;
        break;
      case IncomingMailCommentAction::ADD_ON_TEXT_DOCUMENT:
        $objects = $text_documents;
        break;
    }//switch

    if(is_foreachable($objects)) {
      $options_objects = array();
      foreach($objects as $obj_id => $object_name) {
        $options_objects[] = HTML::optionForSelect($object_name, $obj_id, $obj_id == $object_id);
      } //foreach
    } //if

    $select_object_id = $params['id'] = HTML::uniqueId('select_object');
    $params['class'] = 'project_object_select';
    $content .= HTML::select($name, $options_objects, $params);

    $js_params = array(
      'select_type_id' => $type_id,
      'select_object_id' => $select_object_id,
      'tasks' => $tasks,
      'discussions' => $discussions,
      'text_documents' => $text_documents
    );

    $content.= '<script type="text/javascript">App.widgets.SelectProjectObjectByType.init(' . JSON::encode($js_params) . ')</script>';


    return $content;
  } // smarty_function_select_object_by_type
