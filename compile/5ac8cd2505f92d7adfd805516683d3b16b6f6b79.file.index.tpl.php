<?php /* Smarty version Smarty-3.1.12, created on 2014-06-19 16:14:46
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/user_projects/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1565204605539bec897cb7e1-79880954%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5ac8cd2505f92d7adfd805516683d3b16b6f6b79' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/user_projects/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1565204605539bec897cb7e1-79880954',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_539bec8993c4b7_86254085',
  'variables' => 
  array (
    'projects' => 0,
    'is_archive' => 0,
    'project' => 0,
    'active_user' => 0,
    'logged_user' => 0,
    'change_permissions_title' => 0,
    'projects_toggle_url' => 0,
    'projects_toggle_text' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539bec8993c4b7_86254085')) {function content_539bec8993c4b7_86254085($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_project_link')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.project_link.php';
if (!is_callable('smarty_modifier_date')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/modifier.date.php';
if (!is_callable('smarty_function_object_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.object_label.php';
if (!is_callable('smarty_block_assign_var')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.assign_var.php';
if (!is_callable('smarty_block_link')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.link.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
?><div id="user_projects">
<?php if ($_smarty_tpl->tpl_vars['projects']->value){?>
  <table class="active_projects common" cellspacing="0">
    <tr>
      <th class="icon"></th>
      <th class="name"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
      <?php if ($_smarty_tpl->tpl_vars['is_archive']->value){?>
      <th><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Completed On<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
      <?php }?>
      <th class="label"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Label<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
      <th class="role"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project Role<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
      <th class="options"></th>
    </tr>
  <?php  $_smarty_tpl->tpl_vars['project'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['project']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['projects']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['project']->key => $_smarty_tpl->tpl_vars['project']->value){
$_smarty_tpl->tpl_vars['project']->_loop = true;
?>
    <tr <?php if ($_smarty_tpl->tpl_vars['project']->value->complete()->isCompleted()){?>class="completed"<?php }?>>
      <td class="icon"><img src="<?php echo clean($_smarty_tpl->tpl_vars['project']->value->avatar()->getUrl(IProjectAvatarImplementation::SIZE_SMALL),$_smarty_tpl);?>
" alt="" /></td>
      <td class="name"><?php echo smarty_function_project_link(array('project'=>$_smarty_tpl->tpl_vars['project']->value),$_smarty_tpl);?>
</td>
      <?php if ($_smarty_tpl->tpl_vars['is_archive']->value){?>
        <td class="completed_on"><?php echo clean(smarty_modifier_date($_smarty_tpl->tpl_vars['project']->value->getCompletedOn()),$_smarty_tpl);?>
</td>
      <?php }?>
      <td class="label"><?php echo smarty_function_object_label(array('object'=>$_smarty_tpl->tpl_vars['project']->value),$_smarty_tpl);?>
</td>
      <td class="role"><?php echo clean($_smarty_tpl->tpl_vars['active_user']->value->projects()->getRoleName($_smarty_tpl->tpl_vars['project']->value),$_smarty_tpl);?>
</td>
      <td class="options">
      <?php if ($_smarty_tpl->tpl_vars['active_user']->value->canChangeProjectPermissions($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['project']->value)){?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('assign_var', array('name'=>'change_permissions_title')); $_block_repeat=true; echo smarty_block_assign_var(array('name'=>'change_permissions_title'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('user'=>$_smarty_tpl->tpl_vars['active_user']->value->getFirstName(true))); $_block_repeat=true; echo smarty_block_lang(array('user'=>$_smarty_tpl->tpl_vars['active_user']->value->getFirstName(true)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Change :user's Permissions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('user'=>$_smarty_tpl->tpl_vars['active_user']->value->getFirstName(true)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_assign_var(array('name'=>'change_permissions_title'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        <?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>$_smarty_tpl->tpl_vars['project']->value->getUserPermissionsUrl($_smarty_tpl->tpl_vars['active_user']->value),'title'=>$_smarty_tpl->tpl_vars['change_permissions_title']->value,'class'=>'change_permissions')); $_block_repeat=true; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['project']->value->getUserPermissionsUrl($_smarty_tpl->tpl_vars['active_user']->value),'title'=>$_smarty_tpl->tpl_vars['change_permissions_title']->value,'class'=>'change_permissions'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/permissions.png",'module'=>@SYSTEM_MODULE),$_smarty_tpl);?>
" alt=""><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['project']->value->getUserPermissionsUrl($_smarty_tpl->tpl_vars['active_user']->value),'title'=>$_smarty_tpl->tpl_vars['change_permissions_title']->value,'class'=>'change_permissions'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['active_user']->value->canRemoveFromProject($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['project']->value)){?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>$_smarty_tpl->tpl_vars['project']->value->getRemoveUserUrl($_smarty_tpl->tpl_vars['active_user']->value),'title'=>'Remove from Project','class'=>'remove_from_project')); $_block_repeat=true; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['project']->value->getRemoveUserUrl($_smarty_tpl->tpl_vars['active_user']->value),'title'=>'Remove from Project','class'=>'remove_from_project'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/delete.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" alt=""><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['project']->value->getRemoveUserUrl($_smarty_tpl->tpl_vars['active_user']->value),'title'=>'Remove from Project','class'=>'remove_from_project'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php }?>
      </td>
    </tr>
  <?php } ?>
  </table>
<?php }else{ ?>
  <p class="empty_page"><span class="inner"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There are no projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></p>
<?php }?>
   <p class="projects_status_toggle"><a id="projects_url_toggle" href="<?php echo clean($_smarty_tpl->tpl_vars['projects_toggle_url']->value,$_smarty_tpl);?>
"><?php echo clean($_smarty_tpl->tpl_vars['projects_toggle_text']->value,$_smarty_tpl);?>
</a></p>
</div>
<script type="text/javascript">
  $('#user_projects').each(function() {
    var wrapper = $(this);

    wrapper.find('a.change_permissions').flyoutForm({
      'success_message' : App.lang('Permissions have been updated'),
      'success_event' : 'project_permissions_updated',
      'width' : 450
    });

    wrapper.find('a.remove_from_project').flyoutForm({
      'success_event' : 'project_people_updated',
      'width' : 500
    });

    // Refresh Content on One of the Listed Events
    var inline_tabs = wrapper.parents('.inline_tabs:first');

    if (inline_tabs.length) {
      var tabs_id = inline_tabs.attr('id');

      App.Wireframe.Events.bind('project_created.inline_tab project_updated.inline_tab project_deleted.inline_tab project_people_updated.inline_tab user_added_to_project.inline_tab project_permissions_updated.inline_tab', function (event, invoice) {
        App.widgets.InlineTabs.refresh(tabs_id);
      });

      var projects_tab = inline_tabs.find('div.inline_tabs_links ul li a.selected');
      var projects_toggle_link = $('#projects_url_toggle');
      var projects_page = projects_toggle_link.attr('href');

      // toggle archive/active projects
      projects_toggle_link.click(function () {
        projects_tab.attr('href', projects_page).click();
        return false;
      });
    } // if
  });
</script><?php }} ?>