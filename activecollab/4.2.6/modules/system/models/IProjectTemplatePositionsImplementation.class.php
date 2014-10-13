<?php

    /**
     * ProjectTemplate positions helper implementation
     *
     * @package activeCollab.modules.system
     * @subpackage models
     */
    class IProjectTemplatePositionsImplementation {

      /**
       * ProjectTemplate instance
       *
       * @var ProjectTemplate
       */
      protected $object;

      /**
       * Construct project_template positions implementation
       *
       * @param ProjectTemplate $object
       * @throws InvalidInstanceError
       */
      function __construct(ProjectTemplate $object) {
        if($object instanceof ProjectTemplate) {
          $this->object = $object;
        } else {
          throw new InvalidInstanceError('object', $object, 'ProjectTemplate');
        } // if
      } // __construct

      /**
       * Positions on template
       *
       * @var bool
       */
      protected $positions = false;

      /**
       * Return positions
       *
       * @return ProjectObjectTemplate[]
       */
      function get() {
        return ProjectObjectTemplates::findBySQL("SELECT * FROM " . TABLE_PREFIX . "project_object_templates WHERE template_id = ? AND type = ?", $this->object->getId(), "Position");
      } // get

    }