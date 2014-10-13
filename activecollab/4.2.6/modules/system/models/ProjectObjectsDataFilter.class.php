<?php

  /**
   * Common functionality shared by some project objects filters and reports
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class ProjectObjectsDataFilter extends DataFilter {

    // Project filter
    const PROJECT_FILTER_ANY = 'any';
    const PROJECT_FILTER_ACTIVE = 'active';
    const PROJECT_FILTER_COMPLETED = 'completed';
    const PROJECT_FILTER_CATEGORY = 'category';
    const PROJECT_FILTER_CLIENT = 'client';
    const PROJECT_FILTER_SELECTED = 'selected';

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['project_filter'])) {
        if($attributes['project_filter'] == Projects::PROJECT_FILTER_CATEGORY) {
          $this->filterByProjectCategory(array_var($attributes, 'project_category_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_CLIENT) {
          $this->filterByProjectClient(array_var($attributes, 'project_client_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_SELECTED) {
          $this->filterByProjects(array_var($attributes, 'project_ids'));
        } else {
          $this->setProjectFilter($attributes['project_filter']);
        } // if
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return project filter value
     *
     * @return string
     */
    function getProjectFilter() {
      return $this->getAdditionalProperty('project_filter', Projects::PROJECT_FILTER_ANY);
    } // getProjectFilter

    /**
     * Set project filter value
     *
     * @param string $value
     * @return string
     */
    function setProjectFilter($value) {
      return $this->setAdditionalProperty('project_filter', $value);
    } // setProjectFilter

    /**
     * Set filter to filter records by project category
     *
     * @param integer $project_category_id
     * @return integer
     */
    function filterByProjectCategory($project_category_id) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_CATEGORY);
      $this->setAdditionalProperty('project_category_id', (integer) $project_category_id);
    } // filterByProjectCategory

    /**
     * Return project category ID
     *
     * @return integer
     */
    function getProjectCategoryId() {
      return (integer) $this->getAdditionalProperty('project_category_id');
    } // getProjectCategoryId

    /**
     * Set filter to filter records by project client
     *
     * @param integer $project_client_id
     * @return integer
     */
    function filterByProjectClient($project_client_id) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_CLIENT);
      $this->setAdditionalProperty('project_client_id', (integer) $project_client_id);
    } // filterByProjectClient

    /**
     * Return project client ID
     *
     * @return integer
     */
    function getProjectClientId() {
      return (integer) $this->getAdditionalProperty('project_client_id');
    } // getProjectClientId

    /**
     * Set this report to filter records by project ID-s
     *
     * @param array $project_ids
     * @return array
     */
    function filterByProjects($project_ids) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_SELECTED);

      if(is_array($project_ids)) {
        foreach($project_ids as $k => $v) {
          $project_ids[$k] = (integer) $v;
        } // foreach
      } else {
        $project_ids = null;
      } // if

      $this->setAdditionalProperty('project_ids', $project_ids);
    } // filterByProjects

    /**
     * Return project ID-s
     *
     * @return array
     */
    function getProjectIds() {
      return $this->getAdditionalProperty('project_ids');
    } // getProjectIds

    /**
     * Use by managers for serious reporting, so it needs to go through all projects
     *
     * @return bool
     */
    function getIncludeAllProjects() {
      return true;
    } // getIncludeAllProjects

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can edit this tracking report
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isProjectManager();
    } // canEdit

    /**
     * Returns true if $user can delete this tracking report
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isProjectManager();
    } // canDelete

  }