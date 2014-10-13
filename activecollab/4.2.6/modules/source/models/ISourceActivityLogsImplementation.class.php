<?php

/**
 * Source repository activity logs helper implementation
 *
 * @package activeCollab.modules.source
 * @subpackage models
 */
class ISourceActivityLogsImplementation extends IActivityLogsImplementation {


  /**
   * Create issue log for parent invoice
   *
   * @param IUser $by
   */
  function logRepositoryUpdated(IUser $by) {
    return ActivityLogs::log($this->object, 'repository/updated', $by, $this->getTarget('updated'), $this->getComment('updated'));
  } // logIssuing
}