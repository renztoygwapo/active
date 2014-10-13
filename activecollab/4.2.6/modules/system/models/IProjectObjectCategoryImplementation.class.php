<?php

  /**
   * Project object category implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class IProjectObjectCategoryImplementation extends ICategoryImplementation {

    /**
     * Name of the add category route
     *
     * @var string
     */
    protected $add_category_route;

    /**
     * Return new category URL
     *
     * @return string
     */
    function getAddCategoryUrl() {
      if($this->add_category_route) {
        return Router::assemble($this->add_category_route, array('project_slug' => $this->object->getProject()->getSlug()));
      } else {
        return '#';
      } // if
    } // getAddCategoryUrl

  }