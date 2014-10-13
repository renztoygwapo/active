<?php

  // Build on top backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Announcements administration controller implementation
   *
   * @package angie.frameworks.announcements
   * @subpackage controller
   */
  abstract class FwAnnouncementsController extends BackendController {
    
    /**
     * Selected announcement instance
     *
     * @var Announcement
     */
    protected $active_announcement;
    
    /**
     * Execute before any of the controller actions
     */
    function __before() {
      parent::__before();
      
      $announcement_id = $this->request->getId('announcement_id');
      if($announcement_id) {
        $this->active_announcement = Announcements::findById($announcement_id);
      } // if
      
      if(!($this->active_announcement instanceof Announcement)) {
        $this->active_announcement = new Announcement();
      } // if
      
      $this->response->assign('active_announcement', $this->active_announcement);
    } // __before

    /**
     * Dismiss existing announcement
     */
    function dismiss() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_announcement->isLoaded()) {
          if($this->active_announcement->canDismiss($this->logged_user)) {
            try {
              $this->active_announcement->dismiss($this->logged_user);

              $this->response->respondWithData($this->active_announcement, array(
                'as' => 'announcement',
                'detailed' => true
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // dismiss
    
  }