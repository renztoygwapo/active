<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:14
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.0.0\04. Better User Roles.md" */ ?>
<?php /*%%SmartyHeaderCode:1545542fe1c6376df6-89276565%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b81b9face027b84ac932285dd496783b8e3315d' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.0.0\\04. Better User Roles.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1545542fe1c6376df6-89276565',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c65769e9_99059525',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c65769e9_99059525')) {function content_542fe1c65769e9_99059525($_smarty_tpl) {?>* Title: Better User Roles
* Slug: improved-user-roles

================================================================

In activeCollab 4, the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System Roles<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 have been made **more simple and straightforward**. There are now five system roles  - <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administrator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manager<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Member/Employee<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subcontractor<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 and <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Client<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. The core permissions for these roles are predefined and cannot be edited, but there is a number of extra permissions you can add to each role.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('note', array('title'=>"Who Can See Private Objects?")); $_block_repeat=true; echo HelpElementHelpers::block_note(array('title'=>"Who Can See Private Objects?"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
**Clients are the only users who cannot see Private Objects** in activeCollab. 

For example, if a discussion is marked as private, only your team and subcontractors will be able to see that discussion and to participate in it. This is how you can make some conversations with your team invisible to your clients.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_note(array('title'=>"Who Can See Private Objects?"), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


What follows is a brief description of each role and the related system permissions:

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administrator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


This role is reserved for power users, who are meant to have full unrestricted access to the system. By default, **all areas of activeCollab are accessible to administrator users**, except for finances. <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Finances<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 and <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Quotes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 are available as extra permissions, and have to be enabled manually.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manager<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Managers, without any additional permissions, will only be able to see projects they work on and the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Assignments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 report. With this role, **extra permissions determine which area of responsibility is assigned to the Manager**. For example, the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage People<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 permission allow the manager to edit all user and company information (excluding administrators), the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 permission gives unrestricted access to all the projects, etc. 

This role is intended for users who need to have unrestricted access to certain sections of activeCollab along with their respective reports. 

<?php echo HelpElementHelpers::function_image(array('name'=>'roles.png'),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Member/Employee<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Most of your **team members** will probably have this user role. These users **can only access projects that they are working on**. They need additional permissions to <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create New Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 or to <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
See the Project Budget<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. 

There are also a few additional permissions, which will enable them to use <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Documents<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 or <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Status Updates<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
). However, it is not possible to allow employees to access confidential information in your activeCollab by using reporting or finance tools. 

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subcontractor<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


This role is created for **users who are not a part of your company or organization but are included in the project as external associates**. 

The main difference between subcontractors and your employees role is in additional permissions that are unavailable to your subcontractors - these users cannot  <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
See Project Budgets<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 or to <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
See Company Notes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. Also, by default, the contact details of these users will not be displayed to your clients, and vice versa.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Client<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


The Client role has been made the most restrictive to prevent access to any private information, but is still flexible enough to alloe your client to see essential info for their projects in your activeCollab. 

**Clients will only see projects they are assigned to without any other data**. They can be given extra permissions to <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Submit Project Requests<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Use Global Documents<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 and <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Receive and Pay Invoices<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.<?php }} ?>