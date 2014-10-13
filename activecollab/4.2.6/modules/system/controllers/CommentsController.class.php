<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_comments', COMMENTS_FRAMEWORK);
  
  /**
   * Commnents controller delegate implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class CommentsController extends FwCommentsController {
    
  }