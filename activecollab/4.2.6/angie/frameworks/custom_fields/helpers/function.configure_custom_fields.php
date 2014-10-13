<?php

  /**
   * configure_custom_fields_by_type helper implementation
   *
   * @package angie.frameworks.custom_fields
   * @subpackage helpers
   */

  /**
   * Render set of controls that make managemnet of custom fields easy
   *
   * @param string $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_configure_custom_fields($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $type = array_required_var($params, 'type');

    $id = isset($params['id']) && $params['id'] ? $params['id'] : null;

    if(empty($id)) {
      $id = HTML::uniqueId('configure_custom_fields');
    } // if

    $result = '<div class="configure_custom_fields" id="' . $id . '"><table class="common" cellspacing="0"><thead>
      <tr>
        <th class="is_enabled">' . lang('Enabled') . '</th>
        <th class="field_label">' . lang('Label') . '</th>
      </tr>
    </thead>';

    foreach(CustomFields::getCustomFieldsByType($type) as $field_name => $details) {
      $result .= '<tr class="configure_custom_field" field_name="' . $field_name . '">
        <td class="is_enabled">' . HTML::checkbox($name . '[' . $field_name . '][is_enabled]', $details['is_enabled'], array('value' => 1)) . '</td>
        <td class="field_label">' . HTML::input($name . '[' . $field_name . '][label]', $details['label']) . '</td>
      </tr>';
    } // foreach

    AngieApplication::useWidget('configure_custom_fields', CUSTOM_FIELDS_FRAMEWORK);
    return $result . '</table></div><script type="text/javascript">$("#' . $id . '").configureCustomFields();</script>';
  } // smarty_function_configure_custom_fields