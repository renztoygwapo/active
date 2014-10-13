<?php

  /**
   * Custom fields helper implementation
   *
   * @package angie.frameworks.custom_fields
   * @subpackage models
   */
  abstract class ICustomFieldsImplementation {

    /**
     * Parent object
     *
     * @var ICustomFields
     */
    protected $object;

    /**
     * Construct custom fields helper implementation
     *
     * @param ICustomFields $object
     */
    function __construct(ICustomFields $object) {
      $this->object = $object;
    } // __construct

    /**
     * Return field value
     *
     * @param string $field_name
     * @return mixed
     */
    function getValue($field_name) {
      return $this->object->getFieldValue($field_name);
    } // getValue

    /**
     * Set value of the given field
     *
     * @param string $field_name
     * @param mixed $value
     * @return mixed
     */
    function setValue($field_name, $value) {
      return $this->object->setFieldValue($field_name, $value);
    } // setValue

    /**
     * Return value map for given field
     *
     * @param $field_name
     */
    abstract function getValueMap($field_name);

    /**
     * Return list of values that we can use to aid the user (offered for auto completion)
     *
     * @param string $field_name
     * @return array
     */
    function getValueAid($field_name) {
      return null;
    } // getValueAid

    /**
     * Return field name if object has this label
     *
     * @param $label
     * @return mixed
     */
    function getFieldNameForLabel($label) {
      foreach(CustomFields::getEnabledCustomFieldsByType(get_class($this->object)) as $field_name => $details) {
        if(is_foreachable($label)) {
          $label_array = array_map('strtolower', $label);
          if(in_array(strtolower($details['label']), $label_array) && $details['is_enabled']) {
            return $field_name;
          } // if
        } else {
          if(strtolower($label) == strtolower($details['label']) && $details['is_enabled']) {
            return $field_name;
          } // if
        } // if
      } // foreach
      return false;
    } // getFieldNameForLabel

    /**
     * Describe custom field data of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['custom_fields'] = array();

      foreach(CustomFields::getEnabledCustomFieldsByType(get_class($this->object)) as $field_name => $details) {
        $result['custom_fields'][$field_name] = array(
          'label' => $details['label'],
          'value' => $this->getValue($field_name),
        );
      } // foreach
    } // describe

    /**
     * Describe custom field data of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['custom_fields'] = array();

      foreach(CustomFields::getEnabledCustomFieldsByType(get_class($this->object)) as $field_name => $details) {
        $result['custom_fields'][$field_name] = array(
          'label' => $details['label'],
          'value' => $this->getValue($field_name),
        );
      } // foreach
    } // describeForApi

  }