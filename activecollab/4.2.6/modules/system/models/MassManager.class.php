<?php

  /**
   * system level mass manager
   * 
   * @package activeCollab.modules.system
   * @subpackage model
   */
  class MassManager extends FwMassManager {

    /**
     * Return array of actions that can be performed on $this->object
     * 
     * @return array
     */
    function rebuildActions() {
    	parent::rebuildActions();
    	
    	if ($this->object instanceof ProjectObject && $this->object->fieldExists('milestone_id') && !($this->object instanceof Notebook)) {
    		AngieApplication::useHelper('select_milestone', SYSTEM_MODULE);
    		$this->addMultipleAction('change_milestone', array(
          'title' => lang('Change Milestone'),
          'controls' => smarty_function_select_milestone(array(
	          'name' => 'milestone_id',
	          'project' => $this->object->getProject(),
	        	'user' => $this->user,
	          'optional' => true,
	          'can_create_new' => true
          ), SmartyForAngie::getInstance())
        ));
    	} // if
    	
    	if ($this->object instanceof Project) {
    		AngieApplication::useHelper('select_company', SYSTEM_MODULE);
				$this->addMultipleAction('change_project_client', array(
          'title' => lang('Change Client Company'),
          'controls' => smarty_function_select_company(array(
            'name' => 'company_id',
            'user' => $this->user,
            'optional' => true,
            'can_create_new' => false,
          ), SmartyForAngie::getInstance())
        ));    		
    	} // if
    	
    	if ($this->object instanceof User) {
    		AngieApplication::useHelper('select_company', SYSTEM_MODULE);
				$this->addMultipleAction('change_user_company', array(
          'title' => lang('Change User Company'),
          'controls' => smarty_function_select_company(array(
            'name' => 'company_id',
            'user' => $this->user,
            'optional' => true,
            'can_create_new' => false,
          ), SmartyForAngie::getInstance())
        )); 
    	} // if
    } // rebuildActions
    
    /**
     * Change action for $object
     * 
     * @param ProjectObject $object
     * @param array $variables
     * @return bool
     */
    function actionChangeMilestone(&$object, $variables) {
    	if (!$object->fieldExists('milestone_id')) {
    		return false;
    	} // if
    	
			if (method_exists($object, 'canEdit') && !$object->canEdit($this->user)) {
				return false;
			} // if
    	
    	$milestone_id = isset($variables['milestone_id']) ? (integer) $variables['milestone_id'] : 0;
    	
    	$milestone = $milestone_id ? DataObjectPool::get('Milestone', $milestone_id) : null;

      if($milestone instanceof Milestone) {
        $object->setMilestoneId($milestone->getId());
      } else {
        $object->setMilestoneId(null);
      } // if
    } // actionChangeMilestone
    
    /**
     * Change project client
     * 
     * @param Project $object
     * @param array $variables
     * @return bool
     */
    function actionChangeProjectClient(&$object, $variables) {
    	if (!($object instanceof Project)) {
    		return false;
    	} // if
    	
    	if (!$object->canEdit($this->user)) {
    		return false;
    	} // if
    	
    	$company_id = (integer) array_var($variables, 'company_id', 0);
    	
    	$company = new Company($company_id);
    	if ($company->isNew()) {
    		$company_id = 0;
    	} // if
    	
    	$object->setCompanyId($company_id);
    } // actionChangeProjectClient
    
    /**
     * Change project client
     * 
     * @param Project $object
     * @param array $variables
     * @return bool
     */
    function actionChangeUserCompany(&$object, $variables) {
    	if (!($object instanceof User)) {
    		return false;
    	} // if
    	
    	if (!$object->canEdit($this->user)) {
    		return false;
    	} // if    	
    	
    	$company_id = (integer) array_var($variables, 'company_id', 0);
    	
    	$company = new Company($company_id);
    	if ($company->isNew()) {
    		$company_id = 0;
    	} // if
    	
    	$object->setCompanyId($company_id);
    } // actionChangeProjectClient
    
  }