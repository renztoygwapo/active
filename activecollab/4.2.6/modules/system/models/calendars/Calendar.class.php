<?php

  /**
   * Calendar class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
   class Calendar extends FwCalendar {

	  // Share Types
	  const SHARE_WITH_TEAM_AND_SUBCONTRACTORS = 'members_and_subcontractors';
	  const SHARE_WITH_MANAGERS_ONLY = 'managers';

	  /**
	   * Can view
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canView(User $user) {
		  $creator = $this->getCreatedBy();

		  if ($creator instanceof User) {
			  if($this->getShareType() == Calendar::SHARE_WITH_TEAM_AND_SUBCONTRACTORS) {
				  return $user->isMember() || $user instanceof Subcontractor;
			  } elseif($this->getShareType() == Calendar::SHARE_WITH_MANAGERS_ONLY) {
				  return $user->isManager();
			  } else {
				  return parent::canView($user);
			  } // if
		  } // if

		  return false;
	  } // canView

		/**
		* Return verbose type name
		*
		* @param boolean $lowercase
		* @param Language $language
		* @return string
		*/
		function getVerboseType($lowercase = false, $language = null) {
			return $lowercase ? lang('calendar', null, true, $language) : lang('Calendar', null, true, $language);
		} // getVerboseType

  }