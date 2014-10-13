<?php

  /**
   * custom_fields helper implementation
   *
   * @package angie.frameworks.custom_fields
   * @subpackage helpers
   */

  /**
   * Render custom form block
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_custom_fields($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $object = array_required_var($params, 'object', true, 'ICustomFields');
    $object_data = array_var($params, 'object_data', null, true);

    $fields = CustomFields::getEnabledCustomFieldsByType(get_class($object));
    if($fields) {
      $result = '';

      foreach($fields as $field_name => $details) {
        $value = $object_data && isset($object_data[$field_name]) ? $object_data[$field_name] : '';
        $attributes = array(
          'label' => $details['label'] ? $details['label'] : CustomFields::getSafeFieldLabel($field_name),
        );

        $aid = $object->customFields()->getValueAid($field_name);
        if(count($aid)) {
          $attributes['list'] = HTML::uniqueId("{$field_name}_datalist");
        } // if

        $field_content = HTML::input($name . '[' . $field_name . ']', $value, $attributes);

        if(count($aid)) {
          $field_content .= '<datalist id="' . $attributes['list'] . '">';

          foreach($aid as $aid_value) {
            $field_content .= '<option value="' . clean($aid_value) . '">';
          } // foreach

          $field_content .= '</datalist>';
        } // if

        $dont_repeat = false;

        $result .= smarty_block_wrap(array(
          'field' => $field_name,
        ), $field_content, $smarty, $dont_repeat);
      } // foreach

      return $result;
    } // if
  } // smarty_function_custom_fields