<?php

  /**
   * custom_fields_prepare_objects_list helper implementation
   *
   * @package angie.frameworks.custom_fields
   * @subpackage helpers
   */

  /**
   * Prepare objects list to group by custom fields for specific parent type
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_custom_fields_prepare_objects_list($params, &$smarty) {
    $type = array_required_var($params, 'type');
    $variable = array_required_var($params, 'grouping_variable');
    $sample = array_required_var($params, 'sample', false, 'ICustomFields');

    $field_names = array();
    $result = '';

    foreach(CustomFields::getEnabledCustomFieldsByType($type) as $field_name => $details) {
      $field_names[] = $field_name;

      // Manually create JSON object because we need map as one of the properties
      $result .= "{$variable}.push({
        'label' : " . JSON::encode(lang('By :custom_field', array(
          'custom_field' => $details['label'] ? $details['label'] : CustomFields::getSafeFieldLabel($field_name),
        ))) . ",
        'property' : " . JSON::encode($field_name) . ",
        'map' : new App.Map(" . JSON::map($sample->customFields()->getValueMap($field_name)) . "),
        'icon' : " . JSON::encode(AngieApplication::getImageUrl('objects-list/group-by-custom-field.png', CUSTOM_FIELDS_FRAMEWORK)) . ",
        'uncategorized_label' : " . JSON::encode(lang('Not Set')) . "
      });\n";
    } // foreach

    if(count($field_names)) {
      $events = array($sample->getCreatedEventName(), $sample->getUpdatedEventName());

      foreach($events as $event_name) {
        $result .= "App.Wireframe.Events.bind('{$event_name}.content', function (event, response) {";

        foreach($field_names as $field_name) {
          $result .= "wrapper.objectsList('grouping_map_add_item', '$field_name', response['custom_fields']['$field_name']['value'], response['custom_fields']['$field_name']['value']);\n";
        } // foreach

        $result .= "});\n";
      } // foreach
    } // if

    return $result;
  } // smarty_function_custom_fields_prepare_objects_list