<?php

  /**
   * with_successive_milestones helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * with_successive_milestones helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_with_successive_milestones($params, &$smarty) {
    static $ids = array();
    
    $name = array_var($params, 'name');
    if(empty($name)) {
      return new InvalidParamError('name', $name, '$name value is required', true);
    } // if
    
    $milestone = array_var($params, 'milestone');
    if(!($milestone instanceof Milestone)) {
      return new InvalidParamError('milestone', $milestone, '$milestone value is expected to be an instance of Milestone class', true);
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      do {
        $id = 'with_successive_milestones_' . $counter++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    
    $value = array_var($params, 'value');
    
    $action = array_var($value, 'action', 'dont_move');
    $milestones = array_var($value, 'milestones');
    if(!is_array($milestones)) {
      $milestones = array();
    } // if
    
    $successive_milestones = array_var($params, 'successive_milestones');
    if(empty($successive_milestones)) {
      return '<p class="details">' . lang('This milestone does not have any successive milestones') . '</p>';
    } // if
    
    $result = "<div class=\"with_successive_milestones\">\n<div class=\"options\">\n";
    $options = array(
      'dont_move' => lang("Don't change anything"),
      'move_all' => lang('Adjust all successive milestone by the same number of days'),
      'move_selected' => lang('Adjust only selected successive milestones by the same number of days')
    );
    
    foreach($options as $k => $v) {
      $radio = radio_field($name . '[action]', $k == $action, array('value' => $k, 'id' => $id . '_' . $k));
      $label = label_tag($v, $id . '_' . $k, false, array('class' => 'inline'), '');
      
      $result .= '<span class="block">' . $radio . ' ' . $label . "</span>\n";
    } // if
    
    $result .= "</div>\n<div class=\"successive_milestones\" style=\"display: none\">";
    
    foreach($successive_milestones as $successive_milestone) {
      $input_attributes = array(
        'name' => $name . '[milestones][]',
        'id' => $id . '_milestones_' . $successive_milestone->getId(),
        'type' => 'checkbox',
        'value' => $successive_milestone->getId(), 
        'class' => 'auto'
      );
      
      if(in_array($successive_milestone->getId(), $milestones)) {
        $input_attributes['checked'] = true;
      } // if
      
      $input = open_html_tag('input', $input_attributes, true);
      $label = label_tag($successive_milestone->getName(), $id . '_milestones_' . $successive_milestone->getId(), false, array('class' => 'inline'), '');
      
      if($successive_milestone->getCompletedOn() instanceof DateTimeValue) {
        $label = "<del>$label</del>";
      } // if
      
      $result .= '<span class="block">' . $input . ' ' . $label . "</span>\n";
    } // foreach
    
    $result .= "</div>\n</div>\n";
    
    return $result;
  } // smarty_function_with_successive_milestones

?>