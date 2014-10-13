<?php

  /**
   * Expense class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class Expense extends BaseExpense implements IRoutingContext {
     
    /**
     * Return name string
     *
     * @param boolean $detailed
     * @param boolean $in_category
     * @return string
     */
    function getName($detailed = false, $in_category = false) {

      if ($detailed) {
        $user = $this->getUser();
        $value = $this->getFormatedValue();

        if($in_category) {
          return lang(':value in :category', array('value' => $value, 'category' => $this->getCategoryName()));
        } else {
          if($user instanceof IUser) {
            return lang(':value by :name', array('value' => $value, 'name' => $user->getDisplayName(true)));
          } else {
            return $value;
          } // if
        }
      } else {
        return Globalization::formatMoney($this->getValue(), $this->getCurrency(), null, true);
      } // if
    } // getName
    
    /**
     * Cached parent category instance
     *
     * @var ExpenseCategory::
     */
    private $category = false;
    
    /**
     * Return expense category
     * 
     * @return ExpenseCategory
     */
    function getCategory() {
      if($this->category === false) {
        $this->category = ExpenseCategories::findById($this->getCategoryId());
      } // if
      
      return $this->category;
    } // getCategory
    
    /**
     * Set expense category
     * 
     * @param ExpenseCategory $category
     * @return ExpenseCategory
     */
    function setCategory(ExpenseCategory $category) {
      $this->setCategoryId($category->getId());
      $this->category = $category;
      
      return $this->category;
    } // setCategory
    
    /**
     * Return value formated with currency
     * 
     * @return float
     * 
     */
    function getFormatedValue() {
      return Globalization::formatMoney($this->getValue(), $this->getCurrency());
    }//getFormatedValue
    
    /**
     * Return expense category name
     * 
     * @return string
     */
    function getCategoryName() {
      return $this->getCategory() instanceof ExpenseCategory ? $this->getCategory()->getName() : ExpenseCategories::getNameById($this->getCategoryId());
    } // getCategoryName
    
    
    /**
     * Return Currency 
     * 
     * @return Currency
     */
    function getCurrency() {
      return $this->getProject() instanceof Project && $this->getProject()->getCurrency() instanceof Currency ? $this->getProject()->getCurrency() : null;
    }//getCurrency
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      
      if($detailed) {
        $result['category'] = $this->getCategory() instanceof ExpenseCategory ? $this->getCategory()->describe($user, false, $for_interface) : null;
        $result['currency'] = $this->getCurrency();
      } else {
        $result['category_id'] = $this->getCategoryId();
        $result['currency_id'] = $this->getProject() instanceof Project && $this->getProject()->getCurrencyId() ? $this->getProject()->getCurrencyId() : Currencies::getDefaultId();
      } // if
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      if($detailed) {
        $result['category'] = $this->getCategory() instanceof ExpenseCategory ? $this->getCategory()->describeForApi($user) : null;
        $result['currency'] = $this->getProject() instanceof Project && $this->getProject()->getCurrency() instanceof Currency ? $this->getProject()->getCurrency()->describeForApi($user) : null;
      } else {
        $result['category_id'] = $this->getCategoryId();
        $result['currency_id'] = $this->getProject() instanceof Project && $this->getProject()->getCurrencyId() ? $this->getProject()->getCurrencyId() : Currencies::getDefaultId();
      } // if

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/expenses/' . $this->getId();
    } // getObjectContextPath
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return $this->getParent()->getRoutingContext() . '_tracking_expense';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      $parent_context_params = $this->getParent()->getRoutingContextParams();
      
      return is_array($parent_context_params) ? array_merge($parent_context_params, array('expense_id' => $this->getId())) : array('expense_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoTrackFields('category_id');
    } // history
    
    /**
     * Cached inspector instance
     * 
     * @var ITrackingInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return ITrackingInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new ITrackingInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
  }