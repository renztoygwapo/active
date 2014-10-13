<?php

	// Inherit firewall controller
	AngieApplication::useController('fw_firewall', AUTHENTICATION_FRAMEWORK);

	/**
	 * Main firewal rule controller
	 *
	 * @package activeCollab.modules.authentication
	 * @subpackage controllers
	 */
	abstract class FwFirewallRulesController extends FwFirewallController {

		/**
		 * Active Firewall rule
		 *
		 * @var FirewallRule
		 */
		protected $active_rule;

		/**
		 * Construct firewall rules controller
		 *
		 * @param Request $parent
		 * @param string $context
		 */
		function __construct(Request $parent, $context = null) {
			parent::__construct($parent, $context);
		} // __construct

		/**
		 * Prepare controller
		 */
		function __before() {
			parent::__before();

			$rule_id = $this->request->getId('rule_id');
			if ($rule_id) {
				$this->active_rule = FirewallRules::findById($rule_id);
			} // if

			if (!($this->active_rule instanceof FirewallRule)) {
				$this->active_rule = new FirewallRule();
			} // if

			$this->response->assign('active_rule', $this->active_rule);
		} // before

		/**
		 * Add new firewall rule
		 */
		function add() {
			if($this->request->isAsyncCall()) {
				$rule_data = $this->request->post('rule');
				$this->response->assign('rule_data', $rule_data);

				if($this->request->isSubmitted()) {
					try {
						DB::beginWork('Create rule @ ' . __CLASS__);

						$expire_on = array_var($rule_data, 'expire_on', null);
						if ($expire_on) {
							$tmp = new DateTimeValue($expire_on);
							$rule_data['expire_on'] = $tmp->getForUserInGMT($this->logged_user);
						} // if

						$this->active_rule->setAttributes($rule_data);
						$this->active_rule->save();

						DB::commit('Rule created @ ' . __CLASS__);

						$this->response->respondWithData($this->active_rule, array(
							'as' => 'rule',
							'detailed' => true,
						));
					} catch(Exception $e) {
						DB::rollback('Failed to create rule @ ' . __CLASS__);
						$this->response->exception($e);
					} // try
				} // if
			} else {
				$this->response->badRequest();
			} // if
		} // add

		/**
		 * Edit firewall rule
		 */
		function edit() {
			if($this->request->isAsyncCall()) {
				if($this->active_rule->isLoaded()) {
					if($this->active_rule->canEdit($this->logged_user)) {
						$rule_data = $this->request->post('rule', array(
							'permission' => $this->active_rule->getPermission(),
							'user_id' => $this->active_rule->getUserId(),
							'ip_range' => $this->active_rule->getIpRange(),
							'expire_on' => $this->active_rule->getExpireOn()
						));

						$this->response->assign('rule_data', $rule_data);

						if($this->request->isSubmitted()) {
							try {
								DB::beginWork('Updating rule @ ' . __CLASS__);

								$expire_on = array_var($rule_data, 'expire_on', null);
								if ($expire_on) {
									$tmp = new DateTimeValue($expire_on);
									$rule_data['expired_on'] = $tmp->getForUserInGMT($this->logged_user);
								} // if

								$this->active_rule->setAttributes($rule_data);
								$this->active_rule->save();

								DB::commit('Rule updated @ ' . __CLASS__);

								$this->response->respondWithData($this->active_rule, array(
									'as' => 'rule',
									'detailed' => true,
								));
							} catch(Exception $e) {
								DB::rollback('Failed to update rule @ ' . __CLASS__);
								$this->response->exception($e);
							} // try
						} // if
					} else {
						$this->response->forbidden();
					} // if
				} else {
					$this->response->notFound();
				} // if
			} else {
				$this->response->badRequest();
			} // if
		} // edit

		/**
		 * Delete firewall rule
		 */
		function delete() {
			if ($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
				if ($this->active_rule->isLoaded()) {
					if ($this->active_rule->canDelete($this->logged_user)) {

						$this->active_rule->delete();

						$this->response->ok();
					} else {
						$this->response->forbidden();
					} // if
				} else {
					$this->response->notFound();
				} // if
			} else {
				$this->response->badRequest();
			} // if
		} // delete

	}