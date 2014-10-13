<?php

// Build on top of framework level implementation
AngieApplication::useController('fw_network_admin', ENVIRONMENT_FRAMEWORK);

/**
 * Application level network admin controller implementation
 *
 * @package activeCollab.modules.system
 * @subpackage controllers
 */
class NetworkAdminController extends FwNetworkAdminController {

}