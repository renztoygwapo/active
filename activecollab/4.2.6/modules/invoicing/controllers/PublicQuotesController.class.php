<?php

  // Extend public controller
  AngieApplication::useController('frontend', SYSTEM_MODULE);

  /**
   * Project quotes public controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class PublicQuotesController extends FrontendController {

    /**
     * Selected quote
     *
     * @var Quote
     */
    protected $active_quote;

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $quote_public_id = $this->request->get('quote_public_id');
      if($quote_public_id) {
        $this->active_quote = Quotes::findByPublicId($quote_public_id);
      } else {
        $this->response->notFound();
      } // if

      if($this->active_quote instanceof Quote) {
        $this->smarty->assign(array(
          'active_quote' => $this->active_quote,
          'is_frontend' => true
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __constructor

    /**
     * View requested quote
     */
    function view() {
      if($this->active_quote->isLoaded()) {
        if((empty($this->logged_user) || !Quotes::canManage($this->logged_user)) && $this->active_quote->isPublicPageExpired()) {
          $this->response->notFound();
        } else {
          $this->smarty->assign('quote_expired', $this->active_quote->isPublicPageExpired());
        } // if

        $comment_data = $this->request->post('comment');
        $this->smarty->assign(array(
          'comment_data' => $comment_data,
          'invoice_template' => new InvoiceTemplate()
        ));

        if($this->request->isSubmitted()) {
          try {
            $comment_body = isset($comment_data['body']) && $comment_data['body'] ? nl2br(trim($comment_data['body'])) : null;
          
            if($this->logged_user instanceof IUser) {
              $comment_by = $this->logged_user;
            } else {
              $errors = new ValidationErrors();
              
              $by_name = isset($comment_data['created_by_name']) && $comment_data['created_by_name'] ? trim($comment_data['created_by_name']) : null;
              $by_email = isset($comment_data['created_by_email']) && $comment_data['created_by_email'] ? trim($comment_data['created_by_email']) : null;
              
              if(empty($by_name)) {
                $errors->addError(lang('Your name is required'), 'created_by_name');
              } // if
              
              if($by_email) {
                if(!is_valid_email($by_email)) {
                  $errors->addError(lang('Valid email address is required'), 'created_by_email');
                } // if
              } else {
                $errors->addError(lang('Your email address is required'), 'created_by_email');
              } // if
              
              if(empty($comment_body)) {
                $errors->addError(lang('Your comment is required'), 'body');
              } // if
              
              if($errors->hasErrors()) {
                throw $errors;
              } else {
                $comment_by = Users::findByEmail($by_email, true);
                
                if(empty($comment_by)) {
                  $comment_by = new AnonymousUser($by_name, $by_email);
                } // if
              } // if
            } // if
            
            $this->active_quote->comments()->submit($comment_body, $comment_by, array(
              'set_source' => Comment::SOURCE_WEB, 
              'set_visibility' => VISIBILITY_PUBLIC,
              'comment_attributes' => $comment_data
            ));
            
            if($comment_by instanceof AnonymousUser) {
              Authentication::setVisitorName($comment_by->getName());
              Authentication::setVisitorEmail($comment_by->getEmail());
            } // if

            $this->flash->success('Thank you for the comment');
            $this->response->redirectToUrl($this->active_quote->getPublicUrl());
          } catch(Exception $e) {
            $this->smarty->assign('errors', $e);
          } // try
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view

    /**
     * Show PDF version of the quote
     */
    function pdf() {
      if($this->active_quote->isLoaded()) {
        if(($this->active_quote->isPublicPageExpired() && !($this->logged_user instanceof User && Quotes::canManage($this->logged_user)))) {
          $this->response->notFound();
        } // if

        require_once INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php';
        InvoicePDFGenerator::download($this->active_quote, lang(':quote_id.pdf', array('quote_id' => $this->active_quote->getName())));
        die();
        
      } else {
        $this->response->notFound();
      } // if
    } // pdf

  }