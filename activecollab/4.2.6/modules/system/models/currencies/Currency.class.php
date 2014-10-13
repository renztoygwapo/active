<?php

  /**
   * Currency class
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Currency extends FwCurrency {
  
    /**
     * Returns true if $user can delete this currency
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if(parent::canDelete($user)) {
        if(AngieApplication::isModuleLoaded('invoicing')) {
          return Invoices::countByCurrency($this) == 0 && Projects::countByCurrency($this) == 0;
        } else {
          return Projects::countByCurrency($this) == 0;
        } // if
      } else {
        return false;
      } // if
    } // canDelete
    
  }