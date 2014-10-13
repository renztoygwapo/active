<?php

  /**
   * InvoiceObjects class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceObjects extends BaseInvoiceObjects {

    /**
     * Returns true if $user can create new invoices
     *
     * @param IUser $user
     * @return boolean
     */
    static function canAdd(IUser $user) {
      return $user->isFinancialManager();
    } // canAdd

    /**
     * Returns true if $user can manage invoices
     *
     * @param IUser $user
     * @return boolean
     */
    static function canManage(IUser $user) {
      return $user->isFinancialManager();
    } // canManage

    /**
     * Returns true if $user can manage $company finances
     *
     * @param Company|null $company
     * @param User|null $user
     * @return bool
     */
    static function canManageClientCompanyFinances($company, $user) {
      if($company instanceof Company) {
        return $user instanceof Client && $user->canManageCompanyFinances() && $user->getCompanyId() == $company->getId();
      } else {
        return false;
      } // if
    } // canManageClientCompanyFinances

    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------

    /**
     * Return list of financial managers
     *
     * @param User $exclude_user
     * @return Manager[]
     */
    static function findFinancialManagers($exclude_user = null) {
      $managers = array();

      $all_admins_and_managers = Users::findByType(array('Administrator', 'Manager'));

      if($all_admins_and_managers) {
        foreach($all_admins_and_managers as $user) {
          if($exclude_user instanceof User && $exclude_user->is($user)) {
            continue;
          } // if

          if($user->getSystemPermission('can_manage_finances')) {
            $managers[] = $user;
          } // if
        } // foreach
      } // if

      return $managers;
    } // findFinancialManagers

    /**
     * Get Settings for invoice form
     *
     * @param InvoiceObject $invoice_object
     * @return array
     */
    static function getSettingsForInvoiceForm(InvoiceObject $invoice_object) {
      return array(
        'tax_rates' => TaxRates::find(),
        'default_tax_rate' => TaxRates::getDefault(),
        'invoice_notes' => InvoiceNoteTemplates::find(),
        'original_note' => $invoice_object->getNote(),
        'invoice_item_templates' => InvoiceItemTemplates::findByTaxMode(self::isSecondTaxEnabled()),
        'invoice_item_template' => get_view_path('_invoice_item_row', 'invoices', INVOICING_MODULE),
        'js_invoice_notes' => InvoiceNoteTemplates::findForSelect(),
        'js_original_note' => $invoice_object->getNote(),
        'js_invoice_item_templates' => InvoiceItemTemplates::findForSelect(self::isSecondTaxEnabled()),
        'js_company_details_url' => Router::assemble('people_company_details'),
        'js_company_projects_url' => Router::assemble('people_company_projects', array('company_id' => '--COMPANY_ID--')),
        'js_move_icon_url' => AngieApplication::getImageUrl('layout/bits/handle-move.png', ENVIRONMENT_FRAMEWORK),
        'js_delete_icon_url' => AngieApplication::getImageUrl('icons/12x12/delete-gray.png', ENVIRONMENT_FRAMEWORK),
        'js_second_tax_is_enabled' => $invoice_object->getSecondTaxIsEnabled(),
        'js_second_tax_is_compound' => $invoice_object->getSecondTaxIsCompound(),
      );
    } // getSettingsForInvoiceForm

    /**
     * If Second Tax is enabled
     *
     * @var boolean
     */
    static private $second_tax_enabled = null;

    /**
     * Check if second tax is enabled
     *
     * @return boolean
     */
    static function isSecondTaxEnabled() {
      if (self::$second_tax_enabled === null) {
        self::$second_tax_enabled = (boolean) ConfigOptions::getValue('invoice_second_tax_is_enabled');
      } // if

      return self::$second_tax_enabled;
    } // isSecondTaxEnabled

    /**
     * @var nullCheck if second tax is compound tax
     *
     * @var boolean
     */
    static private $second_tax_is_compound = null;

    /**
     * Check if second tax is compound
     *
     * @return boolean
     */
    static function isSecondTaxCompound() {
      if (!self::isSecondTaxEnabled()) {
        return false;
      } // if

      if (self::$second_tax_is_compound === null) {
        self::$second_tax_is_compound = ConfigOptions::getValue('invoice_second_tax_is_compound');
      } // if

      return self::$second_tax_is_compound;
    } // isSecondTaxCompound
  
  }