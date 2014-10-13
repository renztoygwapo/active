<?php

  /**
   * Quotes class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Quotes extends InvoiceObjects {

    // Project steps
    const STEP_IMPORT_DISCUSSION = 'import-quote-discussion';
    const STEP_IMPORT_MILESTONES = 'import-quote-items';

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can create new quote
     *
     * Optonally, $based_on can be provided, so we can check if user can create
     * a new quote based on a given project request
     *
     * @param IUser $user
     * @param ProjectRequest $based_on
     * @throws InvalidInstanceError
     * @return bool
     */
    static function canAdd(IUser $user, $based_on = null) {
      if(Quotes::canManage($user)) {
        if($based_on === null) {
          return true;
        } else {
          if($based_on instanceof ProjectRequest) {
            return DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'invoice_objects WHERE based_on_type = ? AND based_on_id = ? AND type = ?', get_class($based_on), $based_on->getId(), 'Quote') == 0;
          } else {
            throw new InvalidInstanceError('based_on', $based_on, 'ProjectRequest', 'Quotes can be based on project requests only');
          } // if
        } // if
      } else {
        return false;
      } // if
    } // canAdd

    /**
     * Returns true if $user can manage quotes
     *
     * @param IUser $user
     * @return boolean
     */
    static function canManage(IUser $user) {
      if($user instanceof User) {
        return (($user->isAdministrator() || $user->isManager()) && $user->getSystemPermission('can_manage_quotes')) || (AngieApplication::isOnDemand() && OnDemand::isAccountOwner($user));
      } else {
        return false;
      } // if
    } // canManage

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Find quotes by ID-s
     *
     * @param array $ids
     * @return DBResult
     */
    static function findByIds($ids) {
      return Quotes::find(array(
        'conditions' => array('id IN (?) AND type = ?', $ids, 'Quote'),
      ));
    } // findByIds

    /**
     * Find quote by public id
     *
     * @param string $public_id
     * @return Quote
     */
    static function findByPublicId($public_id) {
      return Quotes::find(array(
        'conditions' => array('varchar_field_1 = ? AND type = ?', $public_id, 'Quote'),
        'one' => true
      ));
    } // findByPublicId

    /**
     * Get status map for quotes
     *
     * @return array
     */
    static function getStatusMap() {
      return array(
        QUOTE_STATUS_DRAFT => lang('Draft'),
        QUOTE_STATUS_LOST => lang('Lost'),
        QUOTE_STATUS_SENT => lang('Sent'),
        QUOTE_STATUS_WON => lang('Won')
      );
    } // getStatusMap

    /**
     * Get quote statuses that $user can see
     *
     * @param User $user
     * @return array
     */
    static function getVisibleStatuses(User $user) {
      $statuses = array(QUOTE_STATUS_SENT, QUOTE_STATUS_WON, QUOTE_STATUS_LOST);
      if (Quotes::canManage($user)) {
        $statuses[] = QUOTE_STATUS_DRAFT;
      } // if

      return $statuses;
    } // getVisibleStatuses

    /**
     * Get id-name map of companies. This method is specific because it also adds client information
     * from companies that haven't been saved yet
     */
    static function getCompaniesIdNameMap(User $user) {
      $companies = Companies::getIdNameMap($user->visibleCompanyIds());
      $nonexisting_companies = DB::execute("SELECT DISTINCT company_name FROM " . TABLE_PREFIX . "invoice_objects WHERE company_id = 0 AND type = ?", 'Quote');
      if (is_foreachable($nonexisting_companies)) {
        foreach ($nonexisting_companies as $value) {
          if (trim($value['company_name'] !== "" && !is_null($value['company_name']))) {
            $companies[Inflector::slug($value['company_name'])] = $value['company_name'];
          } // if
        } // foreach
      } // if

      return $companies;
    } // getCompaniesIdNameMap

    /**
     * Return number of quote drafts
     *
     * @return integer
     */
    static function countDrafts() {
      return Quotes::count(array('status = ? AND type = ?', QUOTE_STATUS_DRAFT, 'Quote'));
    } // countDrafts

    /**
     * Count quotes by company
     *
     * @param Company $company
     * @param User $user
     * @param array $statuses
     * @return integer
     */
    static function countByCompany(&$company, User $user, $statuses = null) {
      if (is_null($statuses)) {
        $statuses = self::getVisibleStatuses($user);
      } // if

      return Quotes::count(array('company_id = ? AND status IN (?) AND type = ?', $company->getId(), $statuses, 'Quote'));
    } // countByCompany

    /**
     * Return quotes by company
     *
     * @param Company $company
     * @param User $user
     * @param mixed $statuses
     * @param string $order_by
     * @return Quote[]
     */
    static function findByCompany(Company $company, User $user, $statuses = null, $order_by = 'created_on') {
      if (is_null($statuses)) {
        $statuses = self::getVisibleStatuses($user);
      } // if

      return Quotes::find(array(
        'conditions' => array('company_id = ? AND status IN (?) AND type = ?', $company->getId(), $statuses, 'Quote'),
        'order' => $order_by,
      ));
    } // findByCompany

    /**
     * Find quotes for object list
     *
     * @param User $user
     * @param Company $company
     * @param integer $state
     * @return array
     */
    static function findForObjectsList(User $user, $company = null, $state = STATE_VISIBLE) {
      $statuses = self::getVisibleStatuses($user);

      if ($company instanceof Company) {
        $quote_url = Router::assemble('people_company_quote', array('company_id' => '--COMPANYID--', 'quote_id' => '--QUOTEID--'));
        $quotes = DB::execute('SELECT id, name, status, company_name FROM ' . TABLE_PREFIX . 'invoice_objects WHERE company_id = ? AND status IN (?) AND type = ? AND state = ?', $company->getId(), $statuses, 'Quote', $state);
      } else {
        $quote_url = Router::assemble('quote', array('quote_id' => '--QUOTEID--'));
        $quotes = DB::execute('SELECT id, name, status, company_id, company_name FROM ' . TABLE_PREFIX . 'invoice_objects WHERE status IN (?) AND type = ? AND state = ?', $statuses, 'Quote', $state);
      } // if

      $result = array();

      if (is_foreachable($quotes)) {
        foreach ($quotes as $quote) {
          $result[] = array(
            'id' => $quote['id'],
            'name' => $quote['name'],
            'status' => $quote['status'],
            'company_id' => isset($quote['company_id']) && $quote['company_id'] > 0 ? $quote['company_id'] : Inflector::slug($quote['company_name']),
            'permalink' => $company instanceof Company ? str_replace(array('--COMPANYID--', '--QUOTEID--'), array($company->getId(), $quote['id']), $quote_url) : str_replace('--QUOTEID--', $quote['id'], $quote_url)
          );
        } // foreach
      } // if

      return $result;
    } // findForObjectsList

    /**
     * Find quotes for phone list view
     *
     * @param User $user
     * @param Company $company
     * @return array
     */
    static function findForPhoneList(User $user, $company = null) {
      $statuses = self::getVisibleStatuses($user);

      $quotes_table = TABLE_PREFIX . 'invoice_objects';

      // exclude draft quotes from company quotes
      if ($company instanceof Company) {
        $quotes = DB::execute("SELECT id, name, status FROM $quotes_table WHERE company_id = ? AND status IN (?) AND type = ? ORDER BY status, id", $company->getId(), $statuses, 'Quote');
        $view_quote_url_template = Router::assemble('people_company_quote', array('company_id' => '--COMPANYID--', 'quote_id' => '--QUOTEID--'));
      } else {
        $quotes = DB::execute("SELECT id, name, status FROM $quotes_table WHERE status IN (?) AND type = ? ORDER BY status, id", $statuses, 'Quote');
        $view_quote_url_template = Router::assemble('quote', array('quote_id' => '--QUOTEID--'));
      } // if

      $result = array();

      if(is_foreachable($quotes)) {
        foreach($quotes as $quote) {
          $result[$quote['status']][] = array(
            'name' => $quote['name'],
            'permalink' => $company instanceof Company ? str_replace(array('--COMPANYID--', '--QUOTEID--'), array($company->getId(), $quote['id']), $view_quote_url_template) : str_replace('--QUOTEID--', $quote['id'], $view_quote_url_template)
          );
        } // foreach
      } // if

      return $result;
    } // findForPhoneList

    /**
     * Find quotes for printing by grouping and filtering criteria
     *
     * @param User $user
     * @param Company $company
     * @param string $group_by
     * @param array $filter_by
     * @param $state
     * @return DBResult
     */
    static function findForPrint(User $user, $company = null, $group_by = null, $filter_by = null, $state = STATE_VISIBLE) {
      if (!in_array($group_by, array('company_id', 'status'))) {
        $group_by = null;
      } // if

      // filter by status
      $filter_type = array_var($filter_by, 'status', null);
      if ($filter_type === '0' && Quotes::canManage($user)) {
        $conditions[] = DB::prepare('(status=?)', QUOTE_STATUS_DRAFT);
      } else if ($filter_type === '1') {
        $conditions[] = DB::prepare('(status=?)', QUOTE_STATUS_SENT);
      } else if ($filter_type === '2') {
        $conditions[] = DB::prepare('(status=?)', QUOTE_STATUS_WON);
      } else if ($filter_type === '3') {
        $conditions[] = DB::prepare('(status=?)', QUOTE_STATUS_LOST);
      } else {
        $conditions[] = DB::prepare('status IN (?)', self::getVisibleStatuses($user));
      } // if

      $conditions[] = DB::prepare('state = ?', $state);

      if ($company instanceof Company) {
        $conditions[] = DB::prepare('company_id = ?', $company->getId());
      } // if

      $conditions[] = DB::prepare('type = ?', 'Quote');

      // do find tasks
      $quotes = Quotes::find(array(
        'conditions' => implode(' AND ', $conditions),
        'order' => $group_by ? $group_by : 'id DESC'
      ));
      return $quotes;
    } // findForPrint

    /**
     * Release projects that are based on given quote
     *
     * @param Quote $quote
     */
    static function releaseProjectsBasedOn(Quote $quote) {
      DB::execute('UPDATE ' . TABLE_PREFIX . "projects SET based_on_type = NULL, based_on_id = NULL WHERE based_on_type = 'Quote' AND based_on_id = ?", $quote->getId());
      AngieApplication::cache()->removeByModel('projects');
    } // releaseProjectsBasedOn
    
    /*
     * Get Settings for quote form
     *
     * @return array
     */
    static function getSettingsForInvoiceForm(Invoice $invoice) {
      $result = parent::getSettingsForInvoiceForm($invoice);
      $result['add_quote_url'] = Router::assemble('quotes_add');
      return $result;
    } // getSettingsForInvoiceForm

  }