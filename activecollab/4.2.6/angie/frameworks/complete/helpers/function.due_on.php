<?php

  /**
   * due helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Print due on string (due in, due today or late) for a given object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   * @throws InvalidInstanceError
   */
  function smarty_function_due_on($params, &$smarty) {
    $due_date = isset($params['date']) ? $params['date'] : null;

    if(empty($due_date)) {
      $object = array_required_var($params, 'object', false, 'ApplicationObject');

      if($object instanceof IComplete && $object->complete()->isCompleted()) {
        return lang('Completed');
      } // if

      if($object->fieldExists('due_on')) {
        $due_date = $object->getDueOn();
      } else {
        return '--';
      } // if
    } // if
    
    if($due_date instanceof DateValue) {
      AngieApplication::useWidget('due_on', COMPLETE_FRAMEWORK);

      $id = array_var($params, 'id');

      if(empty($id)) {
        $id = HTML::uniqueId('due_on');
      } // if

      return HTML::openTag('time', array(
        'datetime' => $due_date->toMySQL(),
        'id' => $id,
        'title' => $due_date->formatDateForUser(),
      ), $due_date->formatDateForUser()) . '<script type="text/javascript">$("#' . $id . '").dueOn();</script>';
    } else {
      return lang('No Due Date');
    } // if
  } // smarty_function_due_on