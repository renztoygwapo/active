<?php

  /**
   * Project creation process implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  final class ProjectCreator {
    
    // Names of additional steps implemented by system module
    const STEP_IMPORT_TEMPLATE = 'import-template';
    const STEP_AUTO_ADD_USERS = 'auto-add-users';
    const STEP_RESCHEDULE = 'reschedule';
    const STEP_MASTER_CATEGORIES = 'import-master-categories';
    const STEP_CLOSE_REQUEST = 'close-request';
  
    /**
     * Create a project, and do all the additional steps right now
     * 
     * @param string $name
     * @param array $additional
     * @param boolean $instantly
     * @throws Exception
     * @return Project
     */
    static function create($name, $additional = null, $instantly = true) {
      $logged_user = Authentication::getLoggedUser();
      
      $based_on = array_var($additional, 'based_on');
      $template = array_var($additional, 'template');
      
      $leader = array_var($additional, 'leader');
      if(!($leader instanceof User)) {
        $leader = $logged_user;
      } // if

      try {
        DB::beginWork('Creating a project @ ' . __CLASS__);

        // Create a new project instance
        $project = self::createProject(
          $name,
          $leader,
          array_var($additional, 'overview'),
          array_var($additional, 'company'),
          array_var($additional, 'category'),
          $template,
          array_var($additional, 'first_milestone_starts_on'),
          $based_on,
          array_var($additional, 'label_id'),
          array_var($additional, 'currency_id'),
          array_var($additional, 'budget'),
          array_var($additional, 'custom_field_1'),
          array_var($additional, 'custom_field_2'),
          array_var($additional, 'custom_field_3')
        );

        // Add leader and person who created a project to the project
        $project->users()->add($logged_user);

        if($logged_user->getId() != $leader->getId()) {
          $project->users()->add($leader);
        } // if

        // If project is created from a template, copy items
        if($template instanceof ProjectTemplate) {
          $positions = array_var($additional, 'positions', array());
          $template->copyItems($project, $positions);

          ConfigOptions::removeValuesFor($project, 'first_milestone_starts_on');

        // In case of a blank project, import users and master categories
        } else {
          Users::importAutoAssignIntoProject($project);
          $project->availableCategories()->importMasterCategories($logged_user);
        } // if

        // Close project request or quote
        if($based_on instanceof ProjectRequest) {
          $based_on->close($logged_user);
        } elseif($based_on instanceof Quote) {
          $based_on->markAsWon($logged_user);
        } // if

        EventsManager::trigger('on_project_created', array(&$project, &$logged_user));

        DB::commit('Project created @ ' . __CLASS__);

        return $project;
      } catch(Exception $e) {
        DB::rollback('Failed to create a project @ ' . __CLASS__);
        throw $e;
      } // try
    } // create

	  /**
	   * Create a new project
	   *
     * @param string $name
     * @param User $leader
     * @param string $overview
     * @param Company $client
     * @param ProjectCategory $category
     * @param ProjectTemplate $template
     * @param DateValue $first_milestone_starts_on
     * @param IProjectBasedOn $based_on
     * @param integer $label_id
     * @param integer $currency_id
     * @param float $budget
     * @param string $custom_field_1
     * @param string $custom_field_2
     * @param string $custom_field_3
     * @return Project
	   */
    static private function createProject($name, User $leader, $overview, $client = null, $category = null, $template = null, $first_milestone_starts_on = null, $based_on = null, $label_id = null, $currency_id = null, $budget = null, $custom_field_1 = null, $custom_field_2 = null, $custom_field_3 = null) {
      $project = new Project();

      $project->setAttributes(array(
        'name' => $name,
        'slug' => Inflector::slug($name),
        'label_id' => (isset($label_id) && $label_id) ? $label_id : 0,
        'overview' => trim($overview) ? trim($overview) : null,
        'company_id' => $client instanceof Company ? $client->getId() : null,
        'category_id' => $category instanceof ProjectCategory ? $category->getId() : null,
        'custom_field_1' => $custom_field_1,
        'custom_field_2' => $custom_field_2,
        'custom_field_3' => $custom_field_3,
      ));

      $project->setState(STATE_VISIBLE);
      $project->setLeader($leader);

      if($template instanceof ProjectTemplate) {
        $project->setTemplate($template);
      } // if

      if($based_on instanceof IProjectBasedOn) {
        $project->setBasedOn($based_on);
      } // if

      $project->setCurrencyId($currency_id);

      if(AngieApplication::isModuleLoaded('tracking') && $budget) {
        $project->setBudget($budget);
      } // if

      $project->setMailToProjectCode(Projects::newMailToProjectCode());

      $project->save();

      if($template instanceof ProjectTemplate && $first_milestone_starts_on instanceof DateValue) {
        ConfigOptions::setValueFor('first_milestone_starts_on', $project, $first_milestone_starts_on->toMySQL());
      } // if

      return $project;
    } // createProject
    
  }