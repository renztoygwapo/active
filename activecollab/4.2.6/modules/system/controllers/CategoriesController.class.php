<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_categories', CATEGORIES_FRAMEWORK);

  /**
   * Categories controller implementation
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CategoriesController extends FwCategoriesController {
    
  }