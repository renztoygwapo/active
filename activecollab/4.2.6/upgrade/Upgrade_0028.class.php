<?php

	/**
   * Update activeCollab 3.0.4 to activeCollab 3.0.5
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0028 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.4';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.0.5';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateManageFinancesPermission' => 'Update invoicing management permissions', 
      );
    } // getActions
    
    /**
     * Update manage finances permission
     * 
     * @return boolean
     */
    function updateManageFinancesPermission() {
      $roles_table = TABLE_PREFIX . 'roles';
      
      try {
        if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'invoicing'")) {
          $roles = DB::execute("SELECT id, permissions FROM $roles_table");
          
          if($roles) {
            foreach($roles as $role) {
              $permissions = $role['permissions'] ? unserialize($role['permissions']) : array();
              
              if(array_key_exists('can_manage_invoices', $permissions)) {
                if($permissions['can_manage_invoices']) {
                  $permissions['can_manage_finances'] = true;
                  $permissions['can_manage_quotes'] = true;
                } // if
                
                unset($permissions['can_manage_invoices']);
                
                DB::execute("UPDATE $roles_table SET permissions = ? WHERE id = ?", serialize($permissions), $role['id']);
              } // if
            } // foreach
          } // if
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateManageFinancesPermission
    
  }