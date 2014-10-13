<?php

  /**
   * Backend wireframe
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class BackendWireframe extends FwBackendWireframe {
    
//    /**
//     * Construct backend wireframe
//     *
//     * @param Request $request
//     */
//    function __construct(Request $request) {
//    	parent::__construct($request);
//
//      $this->breadcrumbs->add('home', lang('Home'), Router::assemble('homepage'));
//    } // __construct
    
    /**
     * Return init wireframe parameters
     * 
     * @param User $user
     * @return array
     */
    function getInitParams($user = null) {
      $params = parent::getInitParams($user);

      $result['powered_by_url'] = 'https://www.activecollab.com/r/backend';
      $result['powered_by_title'] = 'Powered by activeCollab';
      $result['powered_by_icon'] = AngieApplication::getImageUrl('layout/branding/acpowered-white.png', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT);
      
      if($user instanceof User) {
        $params['search_url'] = Router::assemble('backend_search');
        $params['quick_search_url'] = Router::assemble('quick_backend_search');

        if($user instanceof User && AngieApplication::help()->isHelpUser($user)) {
          $params['help_url'] = Router::assemble('help');
          $params['help_popup_url'] = Router::assemble('help_popup');
          $params['whats_new_url'] = Router::assemble('help_whats_new');
        } // if
      } // if
      
      if ($user instanceof User) {
      	$params['menu_items_current'] = $this->getCurrentMenuItem();
      	$params['page_tabs'] = $this->tabs->toArray();
      	$params['page_tabs_current'] = $this->tabs->getCurrentTab();
      	$params['breadcrumbs'] = $this->breadcrumbs;
      	$params['page_title'] = $this->getPageTitle();
      	$params['page_title_actions'] = $this->actions;
      	$params['list_mode_enabled'] = $this->list_mode->isEnabled();
      	$params['print_url'] = $this->print->getUrl();
      	
      	if (AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
	      	$params['benchmark'] = array(
	      		'execution_time' => number_format(BenchmarkForAngie::getTimeElapsed()),
	      		'memory_usage' => format_file_size(BenchmarkForAngie::getMemoryUsage()),
	      		'all_queries' => BenchmarkForAngie::getQueries(),
	      		'queries_count' => BenchmarkForAngie::getQueriesCount()
	      	);

          if (!AngieApplication::isOnDemand() && AngieApplication::getVersion() !== 'current' && $user instanceof User && $user->isAdministrator()) {
            $selfhosted_book = AngieApplication::help()->getBooks()->get("self-hosted-edition");
            if ($selfhosted_book instanceof HelpBook && $selfhosted_book->getPages($user)->get("debugging") instanceof HelpBookPage) {
              $help_page_url = $selfhosted_book->getPages($user)->get("debugging")->getUrl();
            } else {
              $help_page_url = "https://www.activecollab.com/help/books/self-hosted-edition/debugging.html";
            } // if

            $params['system_bar_message'] = lang('activeCollab is running in debug mode, please turn it off. For more information, check <a href=":url" target="_blank">this documentation article</a>',array("url" => $help_page_url));
          }
      	} // if

        if ($user instanceof User && AngieApplication::isOnDemand()) {
          $message = null;

          switch (OnDemand::getAccountStatus()->getStatus()) {
            case OnDemand::STATUS_SUSPENDED_FREE:
            case OnDemand::STATUS_SUSPENDED_PAID:
              $account_expiration_date = OnDemand::getAccountStatus()->getExpiresOn()->formatDateForUser($user);
              $buy_now_url = extend_url(Router::assemble('on_demand_admin_account'), array("buy-now" => 1));
              $account_type = OnDemand::getAccountStatus()->getStatus() == OnDemand::STATUS_SUSPENDED_FREE ? lang("Free Trial") : lang("activeCollab Cloud account");
              $message = lang("Your :account_type is suspended and will be removed on :date. <a href=':buy_now_url'>Click here</a> to place an order and avoid account removal.", array("date" => $account_expiration_date, "buy_now_url" => $buy_now_url, "account_type" => $account_type));
              break;
            case Ondemand::STATUS_FAILED_PAYMENT:
              if (OnDemand::isAccountOwner($user)) {
                $billing_info_url = OnDemand::getSubscriptionInfo()->getCustomerUrl();
                $message = lang('Last payment for your activeCollab Cloud was not successful, please <a href=":billing_info_url" target="_blank">click here</a> to update your billing information', array('billing_info_url' => $billing_info_url));
              } // if
              break;
            default:
              $message = null;
          } // switch

          $params['system_bar_message'] = &$message;
        } // if
      } // if

      return $params;
    } // getInitParams
    
    
  }