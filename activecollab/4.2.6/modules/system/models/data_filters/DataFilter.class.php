<?php

  /**
   * DataFilter class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class DataFilter extends FwDataFilter {

    const USER_FILTER_COMPANY_MEMBER = 'company';

    /**
     * Returns true if $value is a valid and supported user filter
     *
     * @param string $value
     * @return bool
     */
    protected function isValidUserFilter($value) {
      if($value == self::USER_FILTER_COMPANY_MEMBER) {
        return true;
      } // if

      return parent::isValidUserFilter($value);
    } // isValidUserFilter

    /**
     * Prepare and return user filter getter method names
     *
     * @param string $user_filter_name
     * @return array
     */
    protected function prepareUserFilterGetterNames($user_filter_name) {
      return array_merge(parent::prepareUserFilterGetterNames($user_filter_name), array('get' . Inflector::camelize($user_filter_name) . 'ByCompanyMember'));
    } // prepareUserFilterGetterNames

    /**
     * Prepare and return user filter setter method names
     *
     * @param string $user_filter_name
     * @return array
     */
    protected function prepareUserFilterSetterNames($user_filter_name) {
      return array_merge(parent::prepareUserFilterSetterNames($user_filter_name), array(lcfirst(Inflector::camelize($user_filter_name)) . 'ByCompanyMember'));
    } // prepareUserFilterSetterNames

    /**
     * Set user filter settings from attributes
     *
     * @param string $user_filter_name
     * @param array $attributes
     */
    protected function setUserFilterAttributes($user_filter_name, $attributes) {
      parent::setUserFilterAttributes($user_filter_name, $attributes);

      if($attributes[$this->getFilterByUserFilterName($user_filter_name)] == self::USER_FILTER_COMPANY_MEMBER) {
        list($filter_setter, $selected_users_setter, $company_member_setter) = $this->getUserFilterSetters($user_filter_name);

        $this->$company_member_setter($attributes["{$user_filter_name}_by_company_member_id"]);
      } // if
    } // setUserFilterAttributes

    /**
     * Describe user filter
     *
     * @param string $user_filter_name
     * @param array $result
     */
    protected function describeUserFilter($user_filter_name, &$result) {
      parent::describeUserFilter($user_filter_name, $result);

      if($this->getFilterByUserFilterName($user_filter_name) == self::USER_FILTER_COMPANY_MEMBER) {
        list($filter_getter, $selected_users_getter, $company_member_getter) = $this->getUserFilterGetters($user_filter_name);

        $result["{$user_filter_name}_by_company_member_id"] = $this->$company_member_getter();
      } // if
    } // describeUserFilter

    /**
     * Prepare conditions for a particular user filter
     *
     * @param User $user
     * @param string $user_filter_name
     * @param string $table_name
     * @param array $conditions
     * @param string $field_name
     * @throws DataFilterConditionsError
     */
    protected function prepareUserFilterConditions(User $user, $user_filter_name, $table_name, &$conditions, $field_name = null) {
      list($filter_getter, $selected_users_getter, $company_member_getter) = $this->getUserFilterGetters($user_filter_name);

      if($this->$filter_getter() == self::USER_FILTER_COMPANY_MEMBER) {
        $user_filter = $this->getFilterByUserFilterName($user_filter_name);
        $field_name = $this->getUserFilterFieldName($field_name, $user_filter_name);

        $company_id = $this->$company_member_getter();

        if($company_id) {
          $company = Companies::findById($company_id);

          if($company instanceof Company) {
            $visible_user_ids = $user->visibleUserIds($company);

            if($visible_user_ids) {
              $conditions[] = DB::prepare("($table_name.$field_name IN (?))", $visible_user_ids);
            } else {
              throw new DataFilterConditionsError($user_filter, self::USER_FILTER_COMPANY_MEMBER, $company_id, "User can't see any members of target company");
            } // if
          } else {
            throw new DataFilterConditionsError($user_filter, self::USER_FILTER_COMPANY_MEMBER, $company_id, 'Company not found');
          } // if
        } else {
          throw new DataFilterConditionsError($user_filter, self::USER_FILTER_COMPANY_MEMBER, $company_id, 'Company not selected');
        } // if
      } else {
        parent::prepareUserFilterConditions($user, $user_filter_name, $table_name, $conditions, $field_name);
      } // if
    } // prepareUserFilterConditions

  }