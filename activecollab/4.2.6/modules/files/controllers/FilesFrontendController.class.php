<?php

AngieApplication::useController('frontend', SYSTEM_MODULE);

/**
 * Notebook Sharing settings controller
 *
 * @package activeCollab.modules.files
 * @subpackage controllers
 */
class FilesFrontendController extends FrontendController {

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

    if($this->getControllerName() == 'files_frontend') {
      $_GET['sharing_context'] = ProjectAssets::FILES_SHARING_CONTEXT;
      $this->shared_object_delegate = $this->__delegate('files_shared_object', FILES_MODULE, 'default');
    } // if
  } // __construct
}