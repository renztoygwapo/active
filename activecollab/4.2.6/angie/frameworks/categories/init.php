<?php

  /**
   * Categories framework initialization file
   *
   * @package angie.framework.categories
   */
  
  const CATEGORIES_FRAMEWORK = 'categories';
  const CATEGORIES_FRAMEWORK_PATH = __DIR__;
  
  defined('CATEGORIES_FRAMEWORK_INJECT_INTO') or define('CATEGORIES_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'FwCategory' => CATEGORIES_FRAMEWORK_PATH . '/models/categories/FwCategory.class.php', 
    'FwCategories' => CATEGORIES_FRAMEWORK_PATH . '/models/categories/FwCategories.class.php', 
    
    'ICategoriesContext' => CATEGORIES_FRAMEWORK_PATH . '/models/ICategoriesContext.class.php', 
    'ICategoriesContextImplementation' => CATEGORIES_FRAMEWORK_PATH . '/models/ICategoriesContextImplementation.class.php', 
    
    'ICategory' => CATEGORIES_FRAMEWORK_PATH . '/models/ICategory.class.php', 
    'ICategoryImplementation' => CATEGORIES_FRAMEWORK_PATH . '/models/ICategoryImplementation.class.php', 
  
  	'CategoryInspectorProperty' => CATEGORIES_FRAMEWORK_PATH . '/models/CategoryInspectorProperty.class.php'
  ));

  DataObjectPool::registerTypeLoader('Category', function($ids) {
    return Categories::findByIds($ids);
  });