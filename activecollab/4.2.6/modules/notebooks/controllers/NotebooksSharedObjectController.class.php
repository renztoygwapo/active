<?php

  // Build on top of base sharing controller
  AngieApplication::useController('shared_object', SYSTEM_MODULE);

  /**
   * Notebooks Shared object controller delegate
   *
   * @package activeCollab.modules.notebooks
   * @subpackage controllers
   */
  class NotebooksSharedObjectController extends SharedObjectController {

    /**
     * Shared object
     *
     * @var NotebookPage
     */
    protected $active_shared_notebook_page;

    /**
     * Do the stuff before
     */
    function __before() {
      parent::__before();

      $notebook_page_id = $this->request->get('notebook_page_id');
      $this->active_shared_notebook_page = NotebookPages::findById($notebook_page_id);

      if ($this->active_shared_notebook_page instanceof NotebookPage) {
        $notebook = $this->active_shared_notebook_page->getNotebook();

        if($notebook instanceof Notebook && $notebook->getId() == $this->active_shared_object->getId()) {
          $this->wireframe->breadcrumbs->add('shared_notebook_page', $this->active_shared_notebook_page->getName(), $this->active_shared_object->sharing()->getPageUrl($this->active_shared_notebook_page));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->active_shared_notebook_page = new NotebookPage();
      } // if
    } // __before

    /**
     * Notebook page action
     */
    function notebook_page() {
      if ($this->active_shared_notebook_page->isNew() || ($this->active_shared_notebook_page->getState() < STATE_ARCHIVED)) {
        $this->response->notFound();
      } // if

      $this->response->assign('active_shared_notebook_page', $this->active_shared_notebook_page);

      if($this->active_shared_object->sharing()->areCommentsEnabled()) {
        $comment_data = $this->request->post('comment');
        $sharing_code = $this->request->get('sharing_code');
        $cookie_name = 'activecollab_public_task_' . $sharing_code;
        $show_instructions = false;
        if (isset($_COOKIE[$cookie_name])) {
          $show_instructions = true;
        } // if

        $this->response->assign(array(
          'cookie_name' => $cookie_name,
          'comment_data' => $comment_data,
          'show_instructions' => $show_instructions
        ));

        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Posting a comment @ ' . __CLASS__);

            $errors = new ValidationErrors();

            if($this->logged_user instanceof User) {
              $by = $this->logged_user;
            } else {
              $by_name = trim(array_var($comment_data, 'created_by_name'));
              $by_email = trim(array_var($comment_data, 'created_by_email'));

              if(empty($by_name)) {
                $errors->addError(lang('Your name is required'), 'created_by_name');
              } // if

              if(empty($by_email)) {
                $errors->addError(lang('Please provide a valid email address'), 'created_by_email');
              } else {
                if(is_valid_email($by_email)) {
                  $by = Users::findByEmail($by_email, true);

                  if(empty($by)) {
                    $by = new AnonymousUser($by_name, $by_email);
                  } // if
                } else {
                  $errors->addError(lang('Please provide a valid email address'), 'created_by_email');
                } // if
              } // if
            } // if

            $body = array_var($comment_data, 'body');
            $body = nl2br_pre($body); //preserve formatting

            if(trim(strip_tags($body)) == '') {
              $errors->addError(lang('Please insert comment text'), 'body');
            } // if

            if($errors->hasErrors()) {
              throw $errors;
            } // if

            $this->active_shared_notebook_page->comments()->submit($body, $by, array(
              'set_source' => Comment::SOURCE_SHARED_PAGE,
              'set_visibility' => VISIBILITY_PUBLIC,
              'comment_attributes' => $comment_data,
              'attach_uploaded_files' => $this->active_shared_object->sharing()->getSharingProfile()->getAdditionalProperty('attachments_enabled'),
              'exclude_to_notify' => $by
            ));

            DB::commit('Comment posted @ ' . __CLASS__);

            $this->response->redirectToUrl($this->active_shared_object->sharing()->getPageUrl($this->active_shared_notebook_page));
          } catch(Exception $e) {
            DB::rollback('Failed to post a comment @ ' . __CLASS__);
            $this->response->assign('errors', $e);
          } // try
        } // if
      } // if
    } // notebook_page

  }