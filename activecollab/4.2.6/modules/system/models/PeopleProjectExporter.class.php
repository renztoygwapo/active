<?php

  class PeopleProjectExporter extends ProjectExporter {
    
    /**
     * active module
     * 
     * @var string
     */
    protected $active_module = SYSTEM_MODULE;

    /**
     * Relative path where exported files will be stored
     * 
     * @var String
     */
    protected $relative_path = 'people';
    
    /**
     * Export the companies and people
     * 
     * @param void
     * @return null
     */
    public function export() {
      parent::export();
      
      if ($this->section == 'people') {
        $users = $this->project->users()->get();
        $people = array();
        foreach($users as $user) {
          //find only the users within project and their companies
          $company_id = $user->getCompanyId();
          if(!isset($people[$company_id])) {
            $people[$company_id] = array();
          } // if
          $people[$company_id][] = $user;
          //renders user page
          $this->smarty->assignByRef('user', $user);
          $this->renderTemplate('user', $this->getDestinationPath('user_' . $user->getId() . '.html'));
	      	$this->smarty->clearAssign('user');
        } // foreach
        $companies = Companies::findByIds(array_keys($people));
        foreach ($companies as $company) {
          $this->smarty->assignByRef('company', $company);
          $this->smarty->assignByRef('company_people', $people[$company->getId()]);
          $this->renderTemplate('company', $this->getDestinationPath('company_' . $company->getId() . '.html'));
		      $this->smarty->clearAssign('company');
		      $this->smarty->clearAssign('company_people');
        } //foreach
      	// render index page
      	$this->smarty->assignByRef('people', $people);
      	$this->smarty->assignByRef('companies', $companies);
		    $this->renderTemplate('people_index', $this->getDestinationPath('index.html'));
		    $this->smarty->clearAssign('people');
		    $this->smarty->clearAssign('companies');
      } // if
    } // export
    
  } // PeopleProjectExporter