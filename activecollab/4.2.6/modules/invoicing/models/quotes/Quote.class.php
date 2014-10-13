<?php

  /**
   * Quote class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Quote extends InvoiceObject implements IState, IHistory, ISubscriptions, IInvoiceBasedOn, IProjectBasedOn, IComments, IAttachments {

    /**
     * Define fields used by this invoice object
     *
     * @var array
     */
    protected $fields = array(
      'id', 'type',
      'based_on_type', 'based_on_id',
      'project_id', 'currency_id', 'language_id',
      'company_id', 'company_name', 'company_address',
      'name', 'private_note', 'note',
      'subtotal', 'tax', 'total', 'balance_due', 'paid_amount',
      'status', 'state', 'original_state',
      'integer_field_1', // is_locked
      'varchar_field_1', // public_id
      'second_tax_is_enabled', 'second_tax_is_compound',
      'closed_on', 'closed_by_id', 'closed_by_name', 'closed_by_email',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'sent_on', 'sent_by_id', 'sent_by_name', 'sent_by_email',
      'integer_field_2', 'varchar_field_2', 'varchar_field_3', // sent_to_id, sent_to_name, sent_to_email
      'recipient_id', 'recipient_name', 'recipient_email',
      'datetime_field_1', // last comment on
      'hash'
    );

    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'last_comment_on' => 'datetime_field_1',
      'public_id' => 'varchar_field_1',
      'is_locked' => 'integer_field_1',
      'sent_to_id' => 'integer_field_2',
      'sent_to_name' => 'varchar_field_2',
      'sent_to_email' => 'varchar_field_3'
    );

    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
    protected $protect = array(
      'status',
      'closed_on',
      'closed_by_id',
      'closed_by_name',
      'closed_by_email',
      'created_on',
      'created_by_id',
      'created_by_name',
      'created_by_email'
    );

    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------

    /**
     * Get Last Comment On
     *
     * @return DateTimeValue
     */
    function getLastCommentOn() {
      return $this->getDatetimeField1();
    } // getLastComment

    /**
     * Set Last Comment On
     *
     * @param DateTimeValue $last_comment_on
     * @return DateTimeValue
     */
    function setLastCommentOn(DateTimeValue $last_comment_on) {
      return $this->setDateTimeField1($last_comment_on);
    } // setLastCommentOn

    /**
     * Get Public ID
     *
     * @return string
     */
    function getPublicId() {
      return $this->getVarcharField1();
    } // getPublicId

    /**
     * Set Public ID
     *
     * @param string $public_id
     * @return string
     */
    function setPublicId($public_id) {
      return $this->setVarcharField1($public_id);
    } // setPublicId

    /**
     * Get is Quote Locked
     *
     * @return int
     */
    function getIsLocked() {
      return $this->getIntegerField1();
    } // getIsLocked

    /**
     * Set is Quote Locked
     *
     * @param boolean $is_locked
     * @return boolean int
     */
    function setIsLocked($is_locked) {
      return $this->setIntegerField1($is_locked);
    } // setIsLocked

    /**
     * Get Sent to ID
     *
     * @return int
     */
    function getSentToId() {
      return $this->getIntegerField2();
    } // getSentToId

    /**
     * Set Sent to ID
     *
     * @param int $sent_to_id
     * @return int
     */
    function setSentToId($sent_to_id) {
      return $this->setIntegerField2($sent_to_id);
    } // setSentToId

    /**
     * Get Sent to Name
     *
     * @return string
     */
    function getSentToName() {
      return $this->getVarcharField2();
    } // getSentToName

    /**
     * Set Sent to Name
     *
     * @param string $sent_to_name
     * @return string
     */
    function setSentToName($sent_to_name) {
      return $this->setVarcharField2($sent_to_name);
    } // setSentToName

    /**
     * Get Sent to Email
     *
     * @return string
     */
    function getSentToEmail() {
      return $this->getVarcharField3();
    } // getSentToEmail

    /**
     * Set Sent to Email
     *
     * @param string $sent_to_email
     * @return string
     */
    function setSentToEmail($sent_to_email) {
      return $this->setVarcharField3($sent_to_email);
    } // setSentToEmail

    /**
     * Get client company name
     *
     * @return string
     */
    function getCompanyName() {
      $company = $this->getCompany();

      if ($company instanceof Company) {
        return $company->getName();
      } else {
        return $this->getFieldValue('company_name');
      } // if
    } // getCompanyName

    /**
     * Get client company address
     *
     * @return string
     */
    function getCompanyAddress() {
      $company = $this->getCompany();

      if ($company instanceof Company) {
        $quote_company_address = parent::getCompanyAddress();
        $company_address = $company->getConfigValue('office_address');

        return !empty($quote_company_address) && $quote_company_address !== $company_address ? $quote_company_address : $company_address;
      } else {
        return parent::getCompanyAddress();
      } // if
    } // getCompanyAddress

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * State helper instance
     *
     * @var IQuoteStateImplementation
     */
    private $state = false;

    /**
     * Return state helper instance
     *
     * @return IQuoteStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IQuoteStateImplementation($this);
      } // if

      return $this->state;
    } // state

    /**
     * Cached history implementation instance
     *
     * @var IHistoryImplementation
     */
    private $history = false;

    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      if($this->history === false) {
        $this->history = new IHistoryImplementation($this, array('company_id', 'company_name', 'company_address', 'currency_id', 'language_id', 'note', 'private_note', 'status'));
      } // if

      return $this->history;
    } // history

    /**
     * Return subscriptions helper for this object
     *
     * @return IQuoteSubscriptionsImplementation
     */
    function &subscriptions() {
      return $this->getDelegateInstance('subscriptions', 'IQuoteSubscriptionsImplementation');
    } // subscriptions

    /**
     * Return invoice implementation
     *
     * @return IInvoiceBasedOnQuoteImplementation
     */
    function &invoice() {
      return $this->getDelegateInstance('invoice', 'IInvoiceBasedOnQuoteImplementation');
    } // invoice

    /**
     * Return quote comments interface instance
     *
     * @return IQuoteCommentsImplementation
     */
    function &comments() {
      return $this->getDelegateInstance('comments', 'IQuoteCommentsImplementation');
    } // comments

    /**
     * Return attachments manager instance for this object
     *
     * @return IQuoteAttachmentsImplementation
     */
    function &attachments() {
      return $this->getDelegateInstance('attachments', 'IQuoteAttachmentsImplementation');
    } // attachments

    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------

    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canView($user) && !$this->isLost()) {
        $options->add('view_pdf', array(
          'text' => $this->getStatus() == QUOTE_STATUS_DRAFT ? lang('Preview PDF') : lang('View PDF'),
          'url' => Quotes::canManage($user) ? $this->getPdfUrl() : $this->getCompanyPdfUrl(),
          'onclick' => new TargetBlankCallback(),
        ), true);
      } // if

      if($this->canSend($user)) {
        $options->add('send_quote', array(
          'text' => $this->isDraft() ? lang('Send') : lang('Resend Quote'),
          'url' => $this->getSendUrl(),
          'important' => true,
          'onclick' => new FlyoutFormCallback('quote_sent', array(
            'width' => 'narrow',
            'success_event' => $this->getUpdatedEventName(),
            'success_message' => lang('Quote has been sent')
          ))
        ), true);
      } // if

      if($this->canWon($user)) {
        $options->add('quote_won', array(
          'text' => lang('Mark as Won'),
          'url' => $this->getWonUrl(),
          'important' => true,
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to mark this request as won?'),
            'success_message' => lang('Quote has been marked as won'),
            'success_event' => $this->getUpdatedEventName()
          ))
        ), true);
      } // if

      if($this->canLost($user)) {
        $options->add('quote_lost', array(
          'text' => lang('Mark as Lost'),
          'url' => $this->getLostUrl(),
          'important' => true,
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to mark this request as lost?'),
            'success_message' => lang('Quote has been marked as lost'),
            'success_event' => $this->getUpdatedEventName()
          ))
        ), true);
      } // if

      if (Quotes::canAdd($user)) {
        $options->add('duplicate_quote', array(
          'url' => Router::assemble('quotes_add', array('duplicate_quote_id' => $this->getId())),
          'text' => lang('Duplicate'),
          'onclick' => new FlyoutFormCallback('quote_created'),
          'icon' => AngieApplication::getImageUrl('icons/12x12/duplicate-invoice.png', INVOICING_MODULE),
        ), true);
      } // if

      if($this->canCreateInvoice($user)) {
        $options->add('make_invoice', array(
          'url' => $this->invoice()->getUrl(),
          'text' => lang('Create Invoice'),
          'onclick' => new FlyoutFormCallback('create_invoice_from_quote', array(
            'focus_first_field' => false,
          )),
          'important' => true
        ));
      } // if

      if($this->canCreateProject($user)) {
        $options->add('create_project', array(
          'text' => lang('Convert to Project'),
          'url' => $this->getCreateProjectUrl(),
	        'onclick' => new FlyoutFormCallback('project_created'),
          'important' => $this->isWon(),
        ), true);
      } // if

      if ($this->canChangeLanguage($user)) {
        $options->add('change_language', array(
          'url' => $this->getChangeLanguageUrl(),
          'text' => lang('Change Language'),
          'onclick' => new FlyoutFormCallback('quote_updated', array(
            'width' => 'narrow'
          )),
          'icon' => AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),
          'important' => true
        ), false);
      } // if

      if($this->canDelete($user)) {
        $options->add('delete', array(
          'text' => lang('Delete'),
          'url' => $this->getDeleteUrl(),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to delete this quote permanently?'),
            'success_message' => lang('Quote has been successfully deleted'),
            'success_event' => $this->getDeletedEventName(),
          ))
        ), true);
      } // if

      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return the link to the form for saving new client info
     *
     * @return string
     */
    function getSaveClientUrl() {
      return Router::assemble('quote_save_client', array(
        'quote_id' => $this->getId()
      ));
    } // getSaveClientUrl

    /**
     * Return quote's public URL
     *
     * @return string
     */
    function getPublicUrl() {
      return Router::assemble('quote_check', array(
        'quote_public_id' => $this->getPublicId(),
      ));
    } // getPublicUrl

    /**
     * Return company view URL
     *
     * @return string
     */
    function getCompanyViewUrl() {
      return Router::assemble('company_quote', array(
        'quote_id' => $this->getId(),
        'company_id' => $this->getCompanyId()
      ));
    } // getCompanyViewUrl

    /**
     * Return PDF document URL
     *
     * @return string
     */
    function getPdfUrl() {
      return Router::assemble('quote_pdf', array_merge((array) $this->getRoutingContextParams(), array(
        'force' => true,
        'disposition' => 'attachment'
      )));
    } // getPdfUrl

    /**
     * URL to quote's publicly available PDF
     *
     * @return string
     */
    function getPublicPdfUrl() {
      return Router::assemble('quote_public_pdf', array(
        'quote_public_id' => $this->getPublicId(),
        'force' => true,
        'disposition' => 'attachment'
      ));
    } // getPublicPdfUrl

    /**
     * Return PDF document URL accessible from company quotes page
     *
     * @return string
     */
    function getCompanyPdfUrl() {
      if ($this->getCompanyId()) {
        return Router::assemble('company_quote_pdf', array(
          'quote_id' => $this->getId(),
          'company_id' => $this->getCompanyId(),
          'force' => true,
          'disposition' => 'attachment'
        ));
      } else {
        return false;
      } // if
    } // getCompanyPdfUrl

    /**
     * Return send quote URL
     *
     * @return string
     */
    function getSendUrl() {
      return Router::assemble('quote_send', $this->getRoutingContextParams());
    } // getSendUrl

    /**
     * Return quote won URL
     *
     * @return string
     */
    function getWonUrl() {
      return Router::assemble('quote_won', $this->getRoutingContextParams());
    } // getWonUrl

    /**
     * Return company quote won URL
     *
     * @return string
     */
    function getCompanyWonUrl() {
      return Router::assemble('company_quote_won', array(
        'quote_id' => $this->getId(),
        'company_id' => $this->getCompanyId()
      ));
    } // getCompanyViewUrl

    /**
     * Get Change Language Url
     *
     * @return string
     */
    function getChangeLanguageUrl() {
      return Router::assemble('quote_change_language', array('quote_id' => $this->getId()));
    } // getChangeLanguageUrl

    /**
     * Copy quote comments as estimate discussion
     *
     * @param Project $to
     * @param User $by
     * @throws Exception
     */
    function copyComments(Project $to, User $by) {
      $comments = Comments::findByObject($this);

      if($comments) {
        try {
          DB::beginWork('Copy quote comments to a project @ ' . __CLASS__);
  
          $new_discussion = new Discussion();
  
          $new_discussion->setProjectId($to->getId());
          $new_discussion->setName('Estimate Discussion');
          $new_discussion->setBody($this->getName());
          $new_discussion->setState(STATE_VISIBLE);
          $new_discussion->setVisibility(VISIBILITY_NORMAL);
          $new_discussion->setCreatedBy($by);
          $new_discussion->save();
  
          foreach($comments as $comment) {
            if($comment instanceof QuoteComment) {
              $new_comment = new DiscussionComment();

              $new_comment->setParentType('Discussion');
              $new_comment->setParentId($new_discussion->getId());
              $new_comment->setBody($comment->getBody());
              $new_comment->setState(STATE_VISIBLE);
              $new_comment->setCreatedBy($comment->getCreatedBy());
              $new_comment->save();
            } // if
          } // foreach
  
          DB::commit('Quote comments copied to a project @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to copy quote comments to a project @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // copyComments

    /**
     * Create Milestones based on Quote Items
     *
     * @param Project $project
     * @param User $by
     * @throws Exception
     */
    function createMilestones(Project &$project, User $by) {
      $items = $this->getItems();

      if(is_foreachable($items)) {
        try {
          DB::beginWork('Create Milestones based on Quote Items in project @ ' . __CLASS__);

          foreach($items as $item) {
            if($item instanceof QuoteItem) {
              $new_milestone = new Milestone();
              $new_milestone->setProjectId($project->getId());
              $new_milestone->setName($item->getDescription());
              $new_milestone->setState(STATE_VISIBLE);
              $new_milestone->setVisibility(VISIBILITY_NORMAL);
              $new_milestone->setCreatedBy($by);
              $new_milestone->save();
            } // if
          } // foreach

          DB::commit('Milestones based on Quote Items created in project @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to create Milestones based on Quote Items in project @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // createMilestones

    /**
     * Get Create Invoice url
     *
     * @return mixed
     */
    function getCreateInvoiceUrl() {
      return Router::assemble('invoices_add', array(
        'quote_id' => $this->getId()
      ));
    } // getCreateInvoiceUrl

    /**
     * Return create a project based on $this quote URL
     *
     * @return string
     */
    function getCreateProjectUrl() {
      return Router::assemble('projects_add', $this->getRoutingContextParams());
    } // getCreateProjectUrl

    /**
     * Return quote lost URL
     *
     * @return string
     */
    function getLostUrl() {
      return Router::assemble('quote_lost', $this->getRoutingContextParams());
    } // getLostUrl

    /**
     * Return notify client URL
     *
     * @return string
     */
    function getNotifyUrl() {
      return Router::assemble('quote_notify', $this->getRoutingContextParams());
    } // getNotifyUrl

    // ------------------------------------------------------------
    //  Workaround that meets comments implementation prerequisites
    // ------------------------------------------------------------

    /**
     * Return project object visibility
     *
     * @return integer
     */
    function getVisibility() {
      return VISIBILITY_NORMAL;
    } // getVisibility

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can access $company quotes
     *
     * @param IUser $user
     * @param Company $company
     * @return boolean
     */
    function canAccessCompanyQuotes(IUser $user, Company $company) {
      if (Quotes::canManage($user)) {
        return true;
      } else {
        return Quotes::canManageClientCompanyFinances($company, $user);
      } // if
    } // canAccessCompanyQuotes

    /**
     * Return true if $user create an invoice based on $this quote
     *
     * @param iUser $user
     * @return boolean
     */
    function canCreateInvoice(IUser $user) {
      return ($this->isWon() || $this->isSent()) && Invoices::canAdd($user);
    } // canCreateInvoice

    /**
     * Return true if $user create project based on $this quote
     *
     * @param IUser $user
     * @return boolean
     */
    function canCreateProject(IUser $user) {
      return Quotes::canManage($user) && Projects::canAdd($user) && ($this->isSent() || $this->isWon());
    } // canCreateProject

    /**
     * Return true if $user can access $this quote
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return Quotes::canManage($user) || ($this->canAccessCompanyQuotes($user, $this->getCompany()) && $this->getStatus() > QUOTE_STATUS_DRAFT);
    } // canView

    /**
     * Return true if $user can edit $this quote
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return ($this->isDraft() || $this->isSent()) && Quotes::canManage($user);
    } // canEdit

    /**
     * Return true if $user can send $this quote
     *
     * @param User $user
     * @return boolean
     */
    function canSend(User $user) {
      return ($this->isDraft() || $this->isSent() || $this->isWon()) && Quotes::canManage($user);
    } // canSend

    /**
     * Return true if $user can won $this quote
     *
     * @param User $user
     * @return boolean
     */
    function canWon(User $user) {
      //return $this->isSent() && Quotes::canManage($user);
      return !$this->isWon() && Quotes::canManage($user);
    } // canWon

    /**
     * Return true if $user can lost $this quote
     *
     * @param User $user
     * @return boolean
     */
    function canLost(User $user) {
      return !$this->isLost() && Quotes::canManage($user);
    } // canLost

    /**
     * Return true if $user can delete $this quote
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return Quotes::canManage($user);
    } // canDelete

    /**
     * Return true if user can change language for quote
     *
     * @param User $user
     * @return bool
     */
    function canChangeLanguage(User $user) {
      return $this->isWon() || $this->isLost() ? parent::canEdit($user) : false;
    } // canChangeLanguage

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);

      $result['based_on'] = $this->getBasedOn() instanceof ProjectRequest ? $this->getBasedOn()->describe($user) : null;
      $result['based_on_type'] = $this->getBasedOnType();
      $result['based_on_id'] = $this->getBasedOnId();

      $result['public_url'] = $this->getStatus() >= QUOTE_STATUS_SENT && $this->getPublicId() && (Quotes::canManage($user) || !$this->isPublicPageExpired()) ? $this->getPublicUrl() : false;

      $result['status_conditions'] = array(
        'is_draft'  => $this->isDraft(),
        'is_sent'   => $this->isSent(),
        'is_won'    => $this->isWon(),
        'is_lost'   => $this->isLost()
      );

      $result['recipient'] = $this->getRecipient();

      $result['client_id'] = $this->getCompanyId() > 0 ? $this->getCompanyId() : Inflector::slug($this->getCompanyName());
      $result['client'] = $this->getCompany() instanceof Company ? $this->getCompany() : array(
        'id' => Inflector::slug($this->getCompanyName()),
        'name' => $this->getCompanyName(),
        'address' => $this->getCompanyAddress()
      );
      $result['company_address'] = $this->getCompanyAddress();

      return $result;
    } // describe

    /**
     * Validate before saving
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Quote summary is required'), 'name');
      } // if
    } // validate

    /**
     * Set client info based on data provided
     *
     * @param string $client_type
     * @param array $client_data
     * @param array $new_client_data
     * @throws InvalidInstanceError
     */
    function setClientInfo($client_type, $client_data, $new_client_data) {
      switch ($client_type) {
        case 'new_client':
          $recipient = new AnonymousUser($new_client_data['recipient_name'], $new_client_data['recipient_email']);
          $this->setCompanyId(0);
          $this->setCompanyName($new_client_data['company_name']);
          $this->setCompanyAddress($new_client_data['company_address']);
          break;
        case 'existing_client':
          $recipient = Users::findById($client_data['recipient_id']);
          $company = Companies::findById($client_data['company_id']);
          $this->setCompanyId($client_data['company_id']);
          $this->setCompanyName($company->getName());
          $this->setCompanyAddress($client_data['company_address']);
          break;
        default:
          $recipient = null;
          break;
      } // switch

      if ($recipient instanceof IUser) {
        $this->setRecipient($recipient);
      } else {
        throw new InvalidInstanceError("recipient", $recipient, "IUSer");
      } // if
    } // setClientInfo

    /**
     * Cached parent request object, if based on project request
     *
     * @var ProjectRequest
     */
    private $based_on = false;

    /**
     * Return parent project request, if set
     *
     * @return ProjectRequest
     */
    function getBasedOn() {
      if($this->based_on === false) {
        $this->based_on = $this->getBasedOnId() ? ProjectRequests::findById($this->getBasedOnId()) : null;
      } // if

      return $this->based_on;
    } // getBasedOn

    /**
     * Cached project that has been created based on this quote
     *
     * @var bool|Project
     */
    private $project = false;

    /**
     * Get a project based on this quote (assume last created one)
     */
    function getProject() {
      if ($this->project === false) {
        $this->project = Projects::find(array(
          "conditions" => array("based_on_type = ? AND based_on_id = ?", get_class($this), $this->getId()),
          "order" => "created_on DESC",
          "one" => true
        ));
      } // if
      return $this->project;
    } // getProject

    /**
     * Cached date value
     *
     * @var DateTimeValue
     */
    protected $date = false;

    /**
     * Return date based on a given $status
     *
     * @param string $status
     * @return DateTimeValue
     */
    function getDate($status = 'sent') {
      switch($status) {
        case 'draft':
          $action_on = 'getCreatedOn';
          break;
        case 'sent':
          $action_on = 'getSentOn';
          break;
        case 'won':
          $action_on = 'getClosedOn';
          break;
        case 'lost':
          $action_on = 'getClosedOn';
          break;
        default:
          $action_on = 'getSentOn';
          break;
      } // switch

      if($this->date === false) {
        $this->date = $this->$action_on();
      } // if
      return $this->date;
    } // getDate

    /**
     * Get verbose status for the quote
     *
     * @return string
     */
    function getVerboseStatus() {
      $status_map = Quotes::getStatusMap();
      return array_key_exists($this->getStatus(), $status_map) ? $status_map[$this->getStatus()] : $this->getStatus();
    } // getVerboseStatus

    /**
     * Return verbose type name
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('quote', null, true, $language) : lang('Quote', null, true, $language);
    } // getVerboseType

    /**
     * Cached value user that the quote is (or will be) sent to
     *
     * @var User
     */
    private $recipient = false;

    /**
     * Prepare recipient's user object based on the information provided
     *
     * @return IUser
     */
    function getRecipient() {
      if ($this->recipient === false) {
        $recipient_id = $this->getRecipientId();
        if ($recipient_id) {
          $this->recipient = Users::findById($recipient_id);
        } // if

        if (!$this->recipient instanceof User || $this->recipient->getState() === 0) {
          // hack because email field has been added later and nowhere to be populated from
          $recipient_email = is_valid_email($this->getRecipientEmail()) ? $this->getRecipientEmail() : 'unknown@domain.com';

          $this->recipient = new AnonymousUser($this->getRecipientName(), $recipient_email);
        } // if
      } // if

      return $this->recipient;
    } // getRecipient

    /**
     * Save recipient's information
     *
     * @param mixed $recipient
     * @throws InvalidInstanceError
     */
    function setRecipient($recipient) {
      if(!$recipient instanceof IUser) {
        $recipient = Users::findById($recipient);
      }//if

      if($recipient instanceof IUser) {
        $this->setRecipientEmail($recipient->getEmail());
        $this->setRecipientId($recipient->getId());
        $this->setRecipientName($recipient->getName());
      } else {
        throw new InvalidInstanceError('recipient', $recipient, 'IUser');
      } // if
    } // setRecipient

    /**
     * Cached sent to by instance
     *
     * @var User
     */
    private $sent_to = false;

    /**
     * Return user to which quote was sent to
     *
     * @return User
     */
    function getSentTo() {
      if($this->sent_to === false) {
        $this->sent_to = Users::findById($this->getSentToId());
      } // if
      return $this->sent_to;
    } // getSentTo

    /**
     * Mark this quote as won
     *
     * @param User $by
     * @param boolean $save
     */
    function markAsWon(User $by, $save = true) {
      $this->setStatus(QUOTE_STATUS_WON);
      $this->setClosedOn(new DateTimeValue());
      $this->setClosedById($by->getId());
      $this->setClosedByName($by->getName());
      $this->setClosedByEmail($by->getEmail());

      if($save) {
        $this->save();
      } // if
    } // markAsWon

    /**
     * Mark this quote as lost
     *
     * @param User $by
     * @param boolean $save
     */
    function markAsLost(User $by, $save = true) {
      $this->setStatus(QUOTE_STATUS_LOST);
      $this->setClosedOn(new DateTimeValue());
      $this->setClosedById($by->getId());
      $this->setClosedByName($by->getName());
      $this->setClosedByEmail($by->getEmail());

      if($save) {
        $this->save();
      } // if
    } // markAsLost

    /**
     * Check if quote is draft
     *
     * @return boolean
     */
    function isDraft() {
      return $this->getStatus() == QUOTE_STATUS_DRAFT;
    } // isDraft

    /**
     * Returns true if $this quote is being sent
     *
     * @return boolean
     */
    function isSent() {
      return $this->getStatus() == QUOTE_STATUS_SENT;
    } // isSent

    /**
     * Returns true if $this quote is won
     *
     * @return boolean
     */
    function isWon() {
      return $this->getStatus() == QUOTE_STATUS_WON;
    } // isWon

    /**
     * Returns true if $this quote is being lost
     *
     * @return boolean
     */
    function isLost() {
      return $this->getStatus() == QUOTE_STATUS_LOST;
    } // isLost

    /**
     * Check if public page for the quote is expired
     *
     * @return boolean
     */
    function isPublicPageExpired() {
      if($this->getStatus() > QUOTE_STATUS_SENT) {
        $status_updated_on = $this->getDate($this->getStatus());

        if($status_updated_on instanceof DateTimeValue) {
          return (time() - $status_updated_on->getTimestamp()) >= (7 * 86400);
        } // if
      } // if

      return false;
    } // isPublicPageExpired

    /**
     * Delete existing quote
     *
     * @return boolean
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Removing quote @ ' . __CLASS__);

        parent::delete();
        QuoteItems::deleteByParent($this);
        Comments::deleteByParent($this);
        Quotes::releaseProjectsBasedOn($this);

        DB::commit('Quote removed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove quote @ ' . __CLASS__);

        throw $e;
      } // try
    } // delete

    /**
     * Close $this quote that's used $by user to create a project
     *
     * @param User $by
     * @param integer $status
     */
    function close(User $by, $status = null) {
      $status = is_null($status) ? QUOTE_STATUS_WON : $status;
      $this->setStatus($status);
      $this->setClosedOn(new DateTimeValue());
      $this->setClosedById($by->getId());
      $this->setClosedByName($by->getDisplayName());
      $this->setClosedByEmail($by->getEmail());

      $this->save();
    } // close

  }