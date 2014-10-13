<?php

AngieApplication::useController('frontend', SYSTEM_MODULE);

/**
 * Notebook Sharing settings controller
 *
 * @package activeCollab.modules.notebook
 * @subpackage controllers
 */
class NotebooksFrontendController extends FrontendController {

  /**
   * Shared object controller delegate
   *
   * @var SharedObjectController
   */
  protected $shared_object_delegate;

  /**
   * Construct controller
   *
   * @param Request $parent
   * @param mixed $context
   */
  function __construct($parent, $context = null) {
    parent::__construct($parent, $context);

    if($this->getControllerName() == 'notebooks_frontend') {
      $this->shared_object_delegate = $this->__delegate('notebooks_shared_object', NOTEBOOKS_MODULE, 'default');
    } // if
  } // __construct

  /**
   * Do the stuff before
   */
  function __before() {
    $_GET['sharing_context'] = 'notebook';
    parent::__before();
  } // __before
}