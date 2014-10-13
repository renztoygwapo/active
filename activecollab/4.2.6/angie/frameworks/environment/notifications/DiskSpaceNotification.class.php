<?php
/**
 * Created by JetBrains PhpStorm.
 * User: igor
 * Date: 5/10/13 11:18 AM
 * JetBrains PhpStorm
 */

abstract class DiskSpaceNotification extends Notification {

  /**
   * Return Disk space used
   *
   * @return mixed
   */
  function getDiskSpaceUsed() {
    return $this->getAdditionalProperty('disk_space_used');
  } //getDiskSpaceUsed

  /**
   * Set Disk space used
   *
   * @param  $value
   * @return LowDiskSpaceNotification
   */
  function &setDiskSpaceUsed($value) {
    $this->setAdditionalProperty('disk_space_used', $value);

    return $this;
  } // setDiskSpaceUsed

  /**
   * Return Disk space limit
   *
   * @return mixed
   */
  function getDiskSpaceLimit() {
    return $this->getAdditionalProperty('disk_space_limit');
  } //getDiskSpaceLimit

  /**
   * Set Disk space limit
   *
   * @param  $value
   * @return LowDiskSpaceNotification
   */
  function &setDiskSpaceLimit($value) {
    $this->setAdditionalProperty('disk_space_limit', $value);

    return $this;
  } // setDiskSpaceLimit

  /**
   * Return Disk space admin url
   *
   * @return mixed
   */
  function getDiskSpaceAdminUrl() {
    return $this->getAdditionalProperty('disk_space_admin_url');
  } //getDiskSpaceAdminUrl

  /**
   * Set Disk space admin url
   *
   * @param  $value
   * @return LowDiskSpaceNotification
   */
  function &setDiskSpaceAdminUrl($value) {
    $this->setAdditionalProperty('disk_space_admin_url', $value);

    return $this;
  } // setDiskSpaceAdminUrl

  /**
   * Return additional template variables
   *
   * @param NotificationChannel $channel
   * @return array
   */
  function getAdditionalTemplateVars(NotificationChannel $channel) {
    return array(
      'disk_space_used' => $this->getDiskSpaceUsed(),
      'disk_space_limit' => $this->getDiskSpaceLimit(),
      'disk_space_admin_url' => $this->getDiskSpaceAdminUrl(),

    );
  } // getAdditionalTemplateVars

} //DiskSpaceNotification