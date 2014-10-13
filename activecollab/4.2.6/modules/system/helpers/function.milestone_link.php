<?php

  /**
   * milestone_link helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render milestone link
   * 
   * Parameters:
   * 
   * - object - Milestone instance that need to be linked
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_milestone_link($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ProjectObject');
    
    if($object->fieldExists('milestone_id')) {
      return milestone_link($object->getMilestone());
    } else {
      throw new InvalidParamError('object', $object, '$object does not support milestones');
    } // if
  } // smarty_function_milestone_link