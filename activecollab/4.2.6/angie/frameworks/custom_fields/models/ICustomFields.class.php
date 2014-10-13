<?php

  /**
   * Custom fields interface
   *
   * @package angie.frameworks.custom_fields
   * @subpackage models
   */
  interface ICustomFields {

    /**
     * Return custom fields helper implementation
     *
     * @return ICustomFieldsImplementation
     */
    function customFields();

  }