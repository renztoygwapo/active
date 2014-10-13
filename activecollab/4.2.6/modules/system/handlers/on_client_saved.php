<?php

  /**
   * System module on_client_saved event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * @param mixed $object
   * @param User $user
   * @param Company $company
   */
  function system_handle_on_client_saved($object, User $user, Company $company) {
    $find_project_requests = false;

    if ($object instanceof ProjectRequest) {
      $client_company_name = $object->getCreatedByCompanyName();
      $client_email = $object->getCreatedByEmail();

      $find_project_requests = true;
    } // if

    if ($object instanceof Quote) {
      $client_company_name = $object->getCompanyName();
      $client_email = $object->getRecipientEmail();

      $find_project_requests = true;
    } // if

    if ($find_project_requests) {
      $requests = ProjectRequests::find(array(
        "conditions" => array("created_by_company_id = 0 AND created_by_id = 0 AND created_by_company_name = ? AND created_by_email = ?",
          $client_company_name,
          $client_email)
      ));

      if (is_foreachable($requests)) {
        $company_address = $company->getConfigValue('office_address');

        foreach($requests as $request) {
          $request->setCreatedByCompanyName($company->getName());
          $request->setCreatedByCompanyAddress($company_address);
          $request->setCreatedByCompanyId($company->getId());
          $request->setCreatedBy($user);
          $request->save();
        } // foreach
      } // if
    } // if

  } // system_handle_on_client_saved