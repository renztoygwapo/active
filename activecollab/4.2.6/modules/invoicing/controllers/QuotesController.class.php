<?php

  // Inherit projects controller
  AngieApplication::useController('projects', SYSTEM_MODULE);

  /**
   * Main quotes controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controller
   */
  class QuotesController extends ProjectsController {

    /**
     * Active quote
     *
     * @var Quote
     */
    protected $active_quote;

    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;

    /**
     * Cached comments delegate instance
     *
     * @var CommentsController
     */
    protected $comments_delegate;

    /**
     * Subscriptions delegate
     *
     * @var SubscriptionsController
     */
    protected $subscriptions_delegate;

    /**
     * Invoice controller delegate
     * 
     * @var InvoicesController
    */
    protected $invoice_delegate;

	  /**
	   * Access log controller delegate
	   *
	   * @var AccessLogsController
	   */
	  protected $access_logs_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;

    /**
     * Status map
     *
     * @var array
     */
    protected $status_map;
    
    /**
     * Construct quotes controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);

      if($this->getControllerName() == 'quotes') {
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'quote');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'quote');
        $this->invoice_delegate = $this->__delegate('invoice_based_on', INVOICING_MODULE, 'quote');
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'quote');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'quote');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'quote');
	      } // if
      } // if
    } // __construct

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      // very nasty solution to enable comment and subscription actions from company quotes section
      $actions_for_company_quotes = array('quote_add_comment', 'quote_edit_comment', 'quote_comment_state_trash', 'quote_subscribe', 'quote_unsubscribe');
      $can_execute_all_actions = Quotes::canManage($this->logged_user);

      if (!in_array($this->request->getAction(), $actions_for_company_quotes)) {
        $can_execute_action = $can_execute_all_actions;
      } else {
        $can_execute_action = true; // temporary, need quote object to check further permissions
      } // if

      if($can_execute_action) {
        $this->wireframe->actions->clear();
        $this->wireframe->breadcrumbs->add('quotes', lang('Quotes'), Router::assemble('quotes'));

        if($this->request->isWebBrowser() && $this->wireframe->tabs->exists('quotes')) {
          $this->wireframe->tabs->setCurrentTab('quotes');
        } // if

        $this->status_map = Quotes::getStatusMap();

        $quote_id = $this->request->getId('quote_id');
        if($quote_id) {
          $this->active_quote = Quotes::findById($quote_id);
        } // if

        if($this->active_quote instanceof Quote) {
          // now check if the action is a "special" one for company quote and then make sure that user has access to it
          if (in_array($this->request->getAction(), $actions_for_company_quotes) && !$can_execute_all_actions && !(Quotes::canManageClientCompanyFinances($this->active_quote->getCompany(), $this->logged_user))) {
            $this->response->forbidden();
          } // if

          if ($this->active_quote->getState() == STATE_ARCHIVED) {
            $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('invoices_archive'));
          } // if

          $this->wireframe->breadcrumbs->add('quote', $this->active_quote->getName(), $this->active_quote->getViewUrl());
        } else {
          $this->active_quote = new Quote();
        } // if

        $this->response->assign(array(
          'active_quote' => $this->active_quote,
          'is_frontend' => false
        ));

        if ($this->request->isWebBrowser() && in_array($this->request->getAction(), array('index', 'view')) && Quotes::canAdd($this->logged_user)) {
          $this->wireframe->actions->add('new_quote', lang('New Quote'), Router::assemble('quotes_add'), array (
            'onclick' => new FlyoutFormCallback('quote_created'),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          ));
        } // if

        if($this->state_delegate instanceof StateController) {
          $this->state_delegate->__setProperties(array(
            'active_object' => &$this->active_quote
          ));
        } // i

        if($this->comments_delegate instanceof CommentsController) {
          $this->comments_delegate->__setProperties(array(
            'active_object' => &$this->active_quote,
          ));
        } // if

        if($this->subscriptions_delegate instanceof SubscriptionsController) {
          $this->subscriptions_delegate->__setProperties(array(
            'active_object' => &$this->active_quote,
          ));
        } // if

        if($this->invoice_delegate instanceof InvoiceBasedOnController) {
          $this->invoice_delegate->__setProperties(array(
            'active_object' => &$this->active_quote
          ));
        } // if

	      if ($this->access_logs_delegate instanceof AccessLogsController) {
		      $this->access_logs_delegate->__setProperties(array(
			      'active_object' => &$this->active_quote
		      ));
	      } // if

	      if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		      $this->history_of_changes_delegate->__setProperties(array(
			      'active_object' => &$this->active_quote
		      ));
	      } // if

        // there is no state delete
        if ($this->request->getAction() == 'quote_state_delete') {
          $this->__forward('delete', 'delete');
        } // if
      } else {
        $this->response->forbidden();
      } // if
    } // __constructor

    /**
     * Show quotes dashboard
     */
    function index() {

      // Regular request made by web browser
      if($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();

        $this->response->assign(array(
          'quotes' => Quotes::findForObjectsList($this->logged_user),
          'state_map' => $this->status_map,
          'companies_map' => Quotes::getCompaniesIdNameMap($this->logged_user),
          'in_archive' => false,
          'print_url' => Router::assemble('quotes', array('print' => 1))
        ));

      // Phone request
      } elseif($this->request->isPhone()) {
        $this->response->assign('formatted_quotes', Quotes::findForPhoneList($this->logged_user));

      // Tablet device
      } elseif($this->request->isTablet()) {
        throw new NotImplementedError(__METHOD__);

      // Print interface
      } elseif($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_completion = array_var($filter_by, 'status', null); 
        if ($filter_by_completion === '0') {
          $page_title = lang('Drafts quotes');
        } else if ($filter_by_completion === '1') {
          $page_title = lang('Sent quotes');
        } else if ($filter_by_completion === '2') {
          $page_title = lang('Won quotes');
        } else if ($filter_by_completion === '3') {
          $page_title = lang('Lost quotes');
        } else {
          $page_title = lang('All quotes');
        } // if

        // find invoices
        $quotes = Quotes::findForPrint($this->logged_user, null, $group_by, $filter_by);
        
        // maps
        if ($group_by == 'company_id') {
          $map = Quotes::getCompaniesIdNameMap($this->logged_user);

          if(empty($map)) {
            $map = array();
          } // if

          $map[0] = lang('Unknown Client');
          $getter = 'getCompanyId';
          $page_title.= ' ' . lang('Grouped by Client');

        } else if ($group_by == 'status') {
          $getter = 'getStatus';
          $page_title.= ' ' . lang('Grouped by Status');
        } // if       
               
        $this->smarty->assignByRef('quotes', $quotes);
        $this->smarty->assignByRef('map', $this->status_map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => isset($getter) ? $getter : null
        ));
      } //if
    } // index

    /**
     * Archive page
     */
    function archive() {
      if ($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('invoices_archive'));

        $this->response->assign(array(
          'quotes' => Quotes::findForObjectsList($this->logged_user, null, STATE_ARCHIVED),
          'state_map' => $this->status_map,
          'companies_map' => Quotes::getCompaniesIdNameMap($this->logged_user),
          'in_archive' => true,
          'print_url' => Router::assemble('quotes_archive', array('print' => 1))
        ));

      } else if ($this->request->isMobileDevice()) {

      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);

        // page title
        $filter_by_completion = array_var($filter_by, 'status', null);
        if ($filter_by_completion === '0') {
          $page_title = lang('Archived Drafts quotes');
        } else if ($filter_by_completion === '1') {
          $page_title = lang('Archived Sent quotes');
        } else if ($filter_by_completion === '2') {
          $page_title = lang('Archived Won quotes');
        } else if ($filter_by_completion === '3') {
          $page_title = lang('Archived Lost quotes');
        } else {
          $page_title = lang('All archived quotes');
        } // if

        // find invoices
        $quotes = Quotes::findForPrint($this->logged_user, null, $group_by, $filter_by, STATE_ARCHIVED);

        // maps
        if ($group_by == 'company_id') {
          $map = Quotes::getCompaniesIdNameMap($this->logged_user);

          if(empty($map)) {
            $map = array();
          } // if

          $map[0] = lang('Unknown Client');
          $getter = 'getCompanyId';
          $page_title.= ' ' . lang('Grouped by Client');

        } else if ($group_by == 'status') {
          $getter = 'getStatus';
          $page_title.= ' ' . lang('Grouped by Status');
        } // if

        $this->smarty->assignByRef('quotes', $quotes);
        $this->smarty->assignByRef('map', $this->status_map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));
      } // if
    } // archive

    /**
     * Show quote details
     */
    function view() {
      if($this->active_quote->isLoaded()) {
        if($this->active_quote->canView($this->logged_user)) {

          // API call
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_quote, array(
              'as' => 'quote',
              'detailed' => true,
            ));

          // Mobile interface
          } elseif($this->request->isMobileDevice()) {
            $this->response->assign('invoice_template', new InvoiceTemplate());

          // Default web browser request
          } elseif($this->request->isWebBrowser()) {
            if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
              $this->response->assign(array(
                'invoice_template' => new InvoiceTemplate(),
                'save_client_available' => $this->active_quote->getCompany() instanceof Company
              ));
              $this->wireframe->setPageObject($this->active_quote, $this->logged_user);

	            // access log
	            $this->active_quote->accessLog()->log($this->logged_user);

              $this->render();
            } else {
              if ($this->active_quote->getState() == STATE_ARCHIVED) {
                $this->__forward('archive', 'archive');
              } else {
                $this->__forward('index', 'index');
              } // if
            } // if
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view

    /**
     * Show PDF file
     */
    function pdf() {
      if($this->active_quote->isLoaded()) {
        if($this->active_quote->canView($this->logged_user)) {
          require_once INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php';
          InvoicePDFGenerator::download($this->active_quote, lang(':quote_id.pdf', array('quote_id' => $this->active_quote->getName())));
          die();
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // pdf

    /**
     * Create a new quote
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Quotes::canAdd($this->logged_user)) {
          $default_currency = Currencies::getDefault();
          if(!($default_currency instanceof Currency)) {
            $this->response->operationFailed();
          } // if
          
          $quote_data = $this->request->post('quote', array(
            'currency_id' => $default_currency->getId(),
            'second_tax_is_compound' => $this->active_quote->getSecondTaxIsCompound()
          ));

          $new_client = null;

          $project_request_id = $this->request->getId('project_request_id');
          $duplicate_quote_id = $this->request->getId('duplicate_quote_id');

          if($project_request_id) { // if based on project request
            $project_request = ProjectRequests::findById($project_request_id);

            if($project_request instanceof ProjectRequest) {
              $quote_data['project_request_id'] = $project_request_id;
              $quote_data['name'] = $project_request->getName();
              $quote_data['note'] = HTML::toPlainText($project_request->getBody());

              if ($project_request->getCompany() instanceof Company) {
                $quote_data['company_id'] = $project_request->getCompany()->getId();
                $quote_data['recipient_id'] = $project_request->getCreatedBy()->getId();
                $quote_data['company_address'] = $project_request->getCompanyAddress();
              } else {
                $new_client = array(
                  'company_name'    => $project_request->getCreatedByCompanyName(),
                  'company_address' => $project_request->getCreatedByCompanyAddress(),
                  'recipient_name'  => $project_request->getCreatedByName(),
                  'recipient_email' => $project_request->getCreatedByEmail()
                );
              } // if
            } else {
              $this->response->notFound();
            } // if

          } elseif ($duplicate_quote_id) { // if based on an existing quote
            $quote = Quotes::findById($duplicate_quote_id);
            if ($quote instanceof Quote) {
              // basic quote properties
              $quote_data = array(
                'name'                    => $quote->getName(),
                'note'                    => $quote->getNote(),
                'private_note'            => $quote->getPrivateNote(),
                'currency_id'             => $quote->getCurrencyId(),
                'language_id'             => $quote->getLanguageId(),
                'company_address'         => $quote->getCompanyAddress(),
                'second_tax_is_compound'  => $quote->getSecondTaxIsCompound()
              );

              // quote items
              if(is_foreachable($quote->getItems())) {
                $quote_data['items'] = array();
                foreach($quote->getItems() as $item) {
                  $quote_data['items'][] = array(
                    'description'         => $item->getDescription(),
                    'unit_cost'           => $item->getUnitCost(),
                    'quantity'            => $item->getQuantity(),
                    'first_tax_rate_id'   => $item->getFirstTaxRateId(),
                    'second_tax_rate_id'  => $item->getSecondTaxRateId(),
                    'total'               => $item->getTotal(),
                    'subtotal'            => $item->getSubtotal()
                  );
                } // foreach
              } // if

              // company info
              if (!$quote->getCompanyId()) {
                $new_client = array(
                  'company_name'    => $quote->getCompanyName(),
                  'company_address' => $quote->getCompanyAddress(),
                  'recipient_name'  => $quote->getRecipientName(),
                  'recipient_email' => $quote->getRecipientEmail()
                );
              } else {
                $quote_data['company_id'] = $quote->getCompanyId();
                $quote_data['recipient_id'] = $quote->getRecipientId();
              } // if
            } // if
          } // if
          
          $this->response->assign(array('quote_data' => $quote_data, 'new_client' => $new_client));
          $this->response->assign(Quotes::getSettingsForInvoiceForm($this->active_quote));
          
          if($this->request->isSubmitted()) {
            try {
              $quote_items = array_var($this->request->post('invoice'), 'items', array());
              if (!is_foreachable($quote_items)) {
                throw new ValidationErrors(array('quotes' => lang('Quotes items data is not valid. All descriptions are required and there need to be at least one unit with cost set per item!')));
              } // if

              DB::beginWork('Creating quote @ ' . __CLASS__);
              
              $this->active_quote->setAttributes($quote_data);
              $this->active_quote->setCreatedBy($this->logged_user);
              $this->active_quote->setClientInfo($this->request->post('client_type'), $this->request->post('client'), $this->request->post('new_client'));

              // based on
              $project_request = isset($quote_data['project_request_id']) && (integer) $quote_data['project_request_id'] ? ProjectRequests::findById($quote_data['project_request_id']) : null;
              if ($project_request instanceof ProjectRequest) {
                $this->active_quote->setBasedOnType(get_class($project_request));
                $this->active_quote->setBasedOnId($project_request->getId());
              } // if

              if ($this->active_quote->getSecondTaxIsEnabled()) {
                $this->active_quote->setSecondTaxIsCompound(array_var($quote_data, 'second_tax_is_compound', false));
              } // if

              $this->active_quote->setItems($quote_items);
              $this->active_quote->setState(STATE_VISIBLE);
              $this->active_quote->save();

              // close project request
              if ($project_request instanceof ProjectRequest) {
                $project_request->close($this->logged_user);
              } // if

              // set subscriptions
              $this->active_quote->subscriptions()->set(array($this->logged_user, $this->active_quote->getRecipient()));

              DB::commit('Quote created @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_quote, array(
                'as' => 'quote',
                'detailed' => true,
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to create quote @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add

    /**
     * Edit quote
     */
    function edit() {
      $this->wireframe->hidePrintButton();

      if($this->active_quote->isNew()) {
        $this->response->notFound();
      } // if

      if(!$this->active_quote->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if

      $quote_data = $this->request->post('quote');
      if(!is_array($quote_data)) {
        $quote_data = array(
          'currency_id'               => $this->active_quote->getCurrencyId(),
          'language_id'               => $this->active_quote->getLanguageId(),
          'name'                      => $this->active_quote->getName(),
          'company_id'                => $this->active_quote->getCompanyId(),
          'recipient_id'              => $this->active_quote->getRecipientId(),
          'company_address'           => $this->active_quote->getCompanyAddress(),
          'note'                      => $this->active_quote->getNote(),
          'private_note'              => $this->active_quote->getPrivateNote(),
          'second_tax_is_compound'    => $this->active_quote->getSecondTaxIsCompound()
        );

        if(is_foreachable($this->active_quote->getItems())) {
          $quote_data['items'] = array();
          foreach($this->active_quote->getItems() as $item) {
            $quote_data['items'][] = array(
              'id'                  => $item->getId(),
              'description'         => $item->getDescription(),
              'unit_cost'           => $item->getUnitCost(),
              'quantity'            => $item->getQuantity(),
              'first_tax_rate_id'   => $item->getFirstTaxRateId(),
              'second_tax_rate_id'  => $item->getSecondTaxRateId(),
              'total'               => $item->getTotal(),
              'subtotal'            => $item->getSubtotal()
            );
          } // foreach
        } // if
      } // if

      $new_client_data = $this->request->post('new_client');
      if (!is_array($new_client_data) && !($this->active_quote->getCompany() instanceof Company)) {
        $new_client_data = array(
          'company_name' => $this->active_quote->getCompanyName(),
          'company_address' => $this->active_quote->getCompanyAddress(),
          'recipient_name' => $this->active_quote->getRecipientName(),
          'recipient_email' => $this->active_quote->getRecipientEmail(),
        );
      } // if

      $this->response->assign(array('quote_data' => $quote_data, 'new_client' => $new_client_data));
      $this->response->assign(Quotes::getSettingsForInvoiceForm($this->active_quote));

      if($this->request->isSubmitted()) {
        try {
          $quote_items = array_var($this->request->post('invoice'), 'items', array());
          if (!is_foreachable($quote_items)) {
            throw new ValidationErrors(array('quotes' => lang('Quotes items data is not valid. All descriptions are required and there has to be at least one unit with cost set per item!')));
          } // if

          DB::beginWork('Updating quote @ ' . __CLASS__);

          $this->active_quote->setAttributes($quote_data);
          $this->active_quote->setClientInfo($this->request->post('client_type'), $this->request->post('client'), $new_client_data);
          if ($this->active_quote->getSecondTaxIsEnabled()) {
            $this->active_quote->setSecondTaxIsCompound(array_var($quote_data, 'second_tax_is_compound', false));
          } // if

          $this->active_quote->setItems($quote_items);
          $this->active_quote->save();

          DB::commit('Quote updated @ ' . __CLASS__);

          // Send notification to the client about the changes
          if ($this->active_quote->isSent() && !$this->request->post('quote_skip_notification')) {
            AngieApplication::notifications()
              ->notifyAbout('invoicing/quote_updated', $this->active_quote, $this->logged_user)
              ->sendToUsers($this->active_quote->getRecipient());
          } // if

          $this->response->respondWithData($this->active_quote, array(
            'as' => 'quote',
            'detailed' => true,
          ));

        } catch(Exception $e) {
          DB::rollback('Failed to update quote @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } // if
    } // edit

    /**
     * Send quote to client
     */
    function send() {
      $this->wireframe->hidePrintButton();

      if($this->active_quote->isNew()) {
        $this->response->notFound();
      } // if

      if(!$this->active_quote->canSend($this->logged_user)) {
        $this->response->forbidden();
      } // if

      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Sending quote @ ' . __CLASS__);
          $recipient = $this->active_quote->getRecipient();

          if ($this->active_quote->isDraft()) {
            $this->active_quote->setStatus(QUOTE_STATUS_SENT);
            $this->active_quote->setSentOn(new DateTimeValue());
            $this->active_quote->setSentById($this->logged_user->getId());
            $this->active_quote->setSentByName($this->logged_user->getName());
            $this->active_quote->setSentByEmail($this->logged_user->getEmail());

            // set public id only if it's not set already
            if (!$this->active_quote->getPublicId()) {
              $this->active_quote->setPublicId(md5($this->active_quote->getId().time()));
            } // if

            $this->active_quote->setSentToId($recipient->getId());
            $this->active_quote->setSentToName($recipient->getName());
            $this->active_quote->setSentToEmail($recipient->getEmail());

            $this->active_quote->save();
          } // if

          $existing_user = $recipient instanceof User;

          AngieApplication::notifications()
            ->notifyAbout('invoicing/quote_sent', $this->active_quote, $this->logged_user)
            ->sendToUsers($recipient);

          DB::commit('Quote sent @ ' . __CLASS__);

          $this->response->respondWithData($this->active_quote, array(
            'as' => 'quote',
            'detailed' => true,
          ));
        } catch(Exception $e) {
          DB::rollback('Failed to send quote @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } // if
    } // send

    /**
     * Woohoo, we have won
     */
    function won() {
      if($this->active_quote->isNew()) {
        $this->response->notFound();
      } // if

      if(!$this->active_quote->canWon($this->logged_user)) {
        $this->response->forbidden();
      } // if

      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Marking quote as won @ ' . __CLASS__);

          $this->active_quote->markAsWon($this->logged_user);

          $subscribers = $this->active_quote->subscriptions()->get();
          if(is_foreachable($subscribers)) {
            // exclude a user who have won the quote
            foreach($subscribers as $k => $subscriber) {
              if($subscriber->getId() == $this->logged_user->getId()) {
                unset($subscribers[$k]);
              } // if
            } // foreach
          } // if

          DB::commit('Quote marked as won @ ' . __CLASS__);

          $this->response->respondWithData($this->active_quote, array(
            'as' => 'quote',
            'detailed' => true,
          ));
        } catch(Exception $e) {
          DB::rollback('Failed to mark quote as won @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // won

    /**
     * Unfortunately, we lost
     */
    function lost() {
      if($this->active_quote->isNew()) {
        $this->response->notFound();
      } // if

      if(!$this->active_quote->canLost($this->logged_user)) {
        $this->response->forbidden();
      } // if

      if($this->request->isSubmitted()) {
        try {
          $this->active_quote->markAsLost($this->logged_user);

          $this->response->respondWithData($this->active_quote, array(
            'as' => 'quote',
            'detailed' => true,
          ));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // lost

    /**
     * Delete quote
     */
    function delete() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        if(!$this->active_quote->isNew()) {
          if($this->active_quote->canDelete($this->logged_user)) {
            try {
              $this->active_quote->delete();

              $this->response->respondWithData(array(
                'id' => $this->active_quote->getId(),
              ), array(
                'as' => 'quote',
                'detailed' => true,
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
    } // delete

    /**
     * Save client data from a quote that has been created by new user
     */
    function save_client() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if($this->logged_user->isPeopleManager()) {
          $company_data = $this->request->post('company_data');
          $user_data = $this->request->post('user_data');

          if (!is_foreachable($company_data)) {
            $company_data = array(
              'company_name' => $this->active_quote->getCompanyName(),
              'company_address' => $this->active_quote->getCompanyAddress()
            );
          } // if

          if (!is_foreachable($user_data)) {
            $user_info = explode(" ", $this->active_quote->getRecipientName());
            if (count($user_info) > 1) {
              $first_name = $user_info['0'];
              $last_name = substr($this->active_quote->getRecipientName(), strpos($this->active_quote->getRecipientName(), " "), strlen($this->active_quote->getRecipientName()));
            } else {
              $first_name = $this->active_quote->getRecipientName();
              $last_name = "";
            } // if

            $user_data = array(
              'email' => $this->active_quote->getRecipientEmail(),
              'first_name' => $first_name,
              'last_name' => $last_name
            );
          } // if

          $this->smarty->assign(array(
            'user_data' => $user_data,
            'company_data' => $company_data,
          ));

          if ($this->request->isSubmitted()) {
            try {
              DB::beginWork('Adding company and user @ ' . __CLASS__);

              $errors = new ValidationErrors();

              $company_name = isset($company_data['company_name']) && $company_data['company_name'] ? trim($company_data['company_name']) : null;
              if (!$company_name) {
                $errors->addError(lang('Company Name is required'), 'company_name');
              } // if

              if (Companies::findByName($company_name) instanceof Company) {
                $errors->addError(lang('Company with that name ":name" already exists', array('name' => $company_name)), 'company_name');
              } // if

              $company_address = isset($company_data['company_address']) && $company_data['company_address'] ? trim($company_data['company_address']) : null;
              if (!$company_address) {
                $errors->addError(lang('Company Address is required'), 'company_address');
              } // if

              $user_email = isset($user_data['email']) && $user_data['email'] ? trim($user_data['email']) : null;
              if (!$user_email || !is_valid_email($user_email)) {
                $errors->addError(lang("Client's Email is required"), 'email');
              } elseif (Users::findByEmail($user_email, true) instanceof User) {
                $errors->addError(lang('User with email address ":email" already exists', array('email' => $user_email)), 'email');
              } // if

              if ($errors->hasErrors()) {
                throw $errors;
              } else {
                // save company
                $company = new Company();
                $company->setName($company_name);
                $company->setState(STATE_VISIBLE);
                $company->setIsOwner(false);
                $company->save();

                ConfigOptions::setValueFor('office_address', $company, $company_address);

                // save user
                $user = new Client();
                $user->setEmail($user_email);
                $user->setFirstName($user_data['first_name']);
                $user->setLastName($user_data['last_name']);
                $user->setCompany($company);
                $user->setState(STATE_VISIBLE);
                $user->setSystemPermissions(array(
                  'can_request_project',
                  'can_manage_client_finances'
                ));

                $password = Authentication::getPasswordPolicy()->generatePassword();
                $user->setPassword($password);

                $user->save();

                EventsManager::trigger("on_client_saved", array("object" => $this->active_quote, "user"=> $user, "company" => $company));

                DB::commit('Company and user added @ ' . __CLASS__);

                // send welcome email to the client
                if ($this->request->post('notify_client')) {
                  AngieApplication::notifications()
                    ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/welcome', $user, $this->logged_user)
                    ->setPassword($password)
                    ->sendToUsers($user);
                } // if

                $this->response->respondWithData(Quotes::findById($this->active_quote->getId()), array(
                  'as' => 'quote',
                  'detailed' => true,
                ));
              } // if
            } catch (Exception $e) {
              DB::rollback('Failed to add company and user @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // save_client

    /**
     * Change language
     */
    function change_language() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      if ($this->active_quote->isNew()) {
        $this->response->notFound();
      } // if

      if (!$this->active_quote->canChangeLanguage($this->logged_user)) {
        $this->response->forbidden();
      } // if

      $quote_data = $this->request->post('quote');
      if(!is_array($quote_data)) {
        $quote_data = array(
          'language_id' => $this->active_quote->getLanguageId(),
        );
      } // if

      $this->response->assign(array(
        'quote_data' => $quote_data
      ));

      if ($this->request->isSubmitted()) {
        try {
          $this->active_quote->setLanguageId(array_var($quote_data, 'language_id'));
          $this->active_quote->save();

          $this->response->respondWithData($this->active_quote, array(
            'as' => 'quote',
            'detailed' => true,
          ));
        } catch (Exception $e) {
          $this->response->exception($e);
        } // try
      } // if
    } // change_language

  }