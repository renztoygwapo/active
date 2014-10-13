<?php

  /**
   * Select label helper implementation
   *
   * @package angie.frameworks.labels
   * @subpackage helpers
   */

  /**
   * Select label helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_label($params, &$smarty) {
    $user = array_required_var($params, 'user', true);
    $type = array_required_var($params, 'type', true);

    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    $optional = array_var($params, 'optional', true, true);
    $value = array_var($params, 'value', null, true);
    
    $label_type = strtolower(array_var($params, 'label_type', null, true));
    if ($label_type == 'inner') {
      $control_label = array_var($params, 'label', null, true);
    } else {
      $control_label = null;
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_label');
    } // if

    $options = array();

    $labels = Labels::findByType($type, true);
    if($labels) {
      foreach($labels as $label) {
        $selected = (is_null($value)) ? $label->getIsDefault() : ($value == $label->getId());
        $options[] = HTML::optionForSelect($label->getName(), $label->getId(), $selected, array(
          'class' => 'object_option',
        ));
      } // foreach
    } // if
    
    if ($control_label) {
      if ($optional) {
        $options = array_merge(array(
          HTML::optionForSelect(lang('No Label')),
          HTML::optionForSelect(''),
        ), $options);
      } // if

      return HTML::select($params['name'], HTML::optionGroup($control_label, $options, array('class' => 'centered')), $params);
    } else {
      return $optional ?
        HTML::optionalSelect($params['name'], $options, $params, lang('No Label')) : 
        HTML::select($params['name'], $options, $params);
    } // if
  } // smarty_function_select_label