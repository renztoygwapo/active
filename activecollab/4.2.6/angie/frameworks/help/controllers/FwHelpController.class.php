<?php

  // Build on top of backend framework
  AngieApplication::useController('backend', SYSTEM_MODULE);

  /**
   * Framework level help controller
   *
   * @package angie.frameworks.help
   * @subpackage controllers
   */
  abstract class FwHelpController extends BackendController {

    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();

      if(AngieApplication::help()->isHelpUser($this->logged_user)) {
        $this->wireframe->tabs->add('help', lang('Help'), Router::assemble('help'), null, true);
        $this->wireframe->tabs->add('whats_new', lang("What's New?"), Router::assemble('help_whats_new'));
        $this->wireframe->tabs->add('books', lang('Books'), Router::assemble('help_books'));
        $this->wireframe->tabs->add('videos', lang('Videos'), Router::assemble('help_videos'));

        $this->wireframe->breadcrumbs->add('help', lang('Help'), Router::assemble('help'));
      } else {
        $this->response->notFound();
      } // if
    } // __before

    /**
     * Show help index
     */
    function index() {
      $this->response->assign('common_questions', AngieApplication::help()->getCommonQuestions($this->logged_user));
    } // index

    /**
     * Show help popup
     */
    function popup() {
      $showing_common_questions = 7;
      $common_questions = AngieApplication::help()->getCommonQuestions($this->logged_user);
      $total_common_questions = is_array($common_questions) ? count($common_questions) : 0;

      if(is_array($common_questions) && $total_common_questions > $showing_common_questions) {
        $common_questions = array_slice($common_questions, 0, $showing_common_questions);
      } // if

      $this->response->assign(array(
        'common_questions' => $common_questions,
        'showing_common_questions' => $showing_common_questions,
        'total_common_questions' => $total_common_questions,
        'contact_options' => AngieApplication::help()->canContactSupport($this->logged_user) ? AngieApplication::help()->getContactOptions($this->logged_user) : null,
      ));

      if(AngieApplication::behaviour()->isTrackingEnabled()) {
        if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this->logged_user)) {
          $tags = array('account_owner');
        } else {
          $tags = array(Inflector::underscore(get_class($this->logged_user)));
        } // if

        AngieApplication::behaviour()->record('help_popup_opened', $tags);
      } // if
    } // popup

    /**
     * Search help
     */
    function search() {
      if($this->request->isAsyncCall()) {
        $search_for = trim($this->request->get('q'));

        if($search_for) {
          $result = Search::queryPaginated($this->logged_user, 'help', $search_for, null, 1, 30);
        } else {
          $result = array();
        } // if

        $this->response->respondWithData($result, array('as' => 'search_results'));
      } else {
        $this->response->badRequest();
      } // if
    } // search

  }