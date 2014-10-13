<?php

  /**
   * Invoicing module on_client_saved event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * @param mixed $object
   * @param User $user
   * @param Company $company
   */
  function invoicing_handle_on_client_saved($object, User $user, Company $company) {
    $find_quotes = false;

    if ($object instanceof ProjectRequest) {
      $client_company_name = $object->getCreatedByCompanyName();
      $client_email = $object->getCreatedByEmail();

      $find_quotes = true;
    } // if

    if ($object instanceof Quote) {
      $client_company_name = $object->getCompanyName();
      $client_email = $object->getRecipientEmail();

      $find_quotes = true;
    } // if

    if ($find_quotes) {
      $quotes = Quotes::find(array(
        "conditions" => array("company_id = 0 AND recipient_id = 0 AND company_name = ? AND recipient_email = ?",
          $client_company_name,
          $client_email
        )
      ));

      if (is_foreachable($quotes)) {
        $company_address = $company->getConfigValue('office_address');

        foreach($quotes as $quote) {
          $quote->setCompanyAddress($company_address);
          $quote->setCompanyId($company->getId());
          $quote->setCompanyName($company->getName());
          $quote->setRecipient($user);
          $quote->save();
        } // foreach
      } // if
    } // if

  } // invoicing_handle_on_client_saved