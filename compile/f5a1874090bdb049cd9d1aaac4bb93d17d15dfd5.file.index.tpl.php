<?php /* Smarty version Smarty-3.1.12, created on 2014-06-27 06:16:56
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_people/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28323455853ad0c5851b417-11252891%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f5a1874090bdb049cd9d1aaac4bb93d17d15dfd5' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_people/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28323455853ad0c5851b417-11252891',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'project_users' => 0,
    'can_manage' => 0,
    'company_name' => 0,
    'users' => 0,
    'user' => 0,
    'can_see_contact_details' => 0,
    'active_project' => 0,
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ad0c5872c0f7_22822417',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ad0c5872c0f7_22822417')) {function content_53ad0c5872c0f7_22822417($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
People<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="project_people">
  <?php  $_smarty_tpl->tpl_vars['users'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['users']->_loop = false;
 $_smarty_tpl->tpl_vars['company_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['project_users']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['users']->key => $_smarty_tpl->tpl_vars['users']->value){
$_smarty_tpl->tpl_vars['users']->_loop = true;
 $_smarty_tpl->tpl_vars['company_name']->value = $_smarty_tpl->tpl_vars['users']->key;
?>
    <table class="common" cellspacing="0">
      <thead>
        <tr>
          <th colspan="<?php if ($_smarty_tpl->tpl_vars['can_manage']->value){?>4<?php }else{ ?>3<?php }?>"><?php echo clean($_smarty_tpl->tpl_vars['company_name']->value,$_smarty_tpl);?>
</th>
        </tr>
      </thead>
      <tbody>
      <?php  $_smarty_tpl->tpl_vars['user'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['user']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['users']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['user']->key => $_smarty_tpl->tpl_vars['user']->value){
$_smarty_tpl->tpl_vars['user']->_loop = true;
?>
        <tr class="project_company_user" user_id="<?php echo clean($_smarty_tpl->tpl_vars['user']->value->getId(),$_smarty_tpl);?>
">
          <td class="avatar"><img src="<?php echo clean($_smarty_tpl->tpl_vars['user']->value->avatar()->getUrl(@IUserAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
"></td>
          <td class="name">
            <a href="<?php echo clean($_smarty_tpl->tpl_vars['user']->value->getViewUrl(),$_smarty_tpl);?>
" class="project_company_user_name"><?php echo clean($_smarty_tpl->tpl_vars['user']->value->getDisplayName(),$_smarty_tpl);?>
</a>
          <?php if ($_smarty_tpl->tpl_vars['can_see_contact_details']->value){?>
            <ul class="project_company_user_contact_details">
              <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <a href="mailto:<?php echo clean($_smarty_tpl->tpl_vars['user']->value->getEmail(),$_smarty_tpl);?>
"><?php echo clean($_smarty_tpl->tpl_vars['user']->value->getEmail(),$_smarty_tpl);?>
</a></li>
            </ul>
          <?php }?>
          </td>
          <td class="role">
          <?php if ($_smarty_tpl->tpl_vars['active_project']->value->isLeader($_smarty_tpl->tpl_vars['user']->value)){?>
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Full Access<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span>(<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project Leader<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)</span>
          <?php }elseif($_smarty_tpl->tpl_vars['user']->value->isAdministrator()){?>
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Full Access<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span>(<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administrator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)</span>
          <?php }elseif($_smarty_tpl->tpl_vars['user']->value->isProjectManager()){?>
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Full Access<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span>(<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project Manager<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)</span>
          <?php }else{ ?>
            <?php echo clean($_smarty_tpl->tpl_vars['user']->value->projects()->getRoleName($_smarty_tpl->tpl_vars['active_project']->value),$_smarty_tpl);?>

          <?php }?>
          </td>
          <?php if ($_smarty_tpl->tpl_vars['can_manage']->value){?>
          <td class="options">
          <?php if ($_smarty_tpl->tpl_vars['user']->value->canChangeProjectPermissions($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>
            <a href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getUserPermissionsUrl($_smarty_tpl->tpl_vars['user']->value),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Change Permissions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="change_permissions"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/configure.png','module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
"></a>
          <?php }?>
          <?php if ($_smarty_tpl->tpl_vars['user']->value->canReplaceOnProject($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>
            <a href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getReplaceUserUrl($_smarty_tpl->tpl_vars['user']->value),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Replace User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="replace_user"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/swap.png','module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
"></a>
          <?php }?>
          <?php if ($_smarty_tpl->tpl_vars['user']->value->canRemoveFromProject($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>
            <a href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getRemoveUserUrl($_smarty_tpl->tpl_vars['user']->value),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="remove_user"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/delete.png','module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
"></a>
          <?php }?>
          </td>
          <?php }?>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  <?php } ?>
</div>

<script type="text/javascript">
  $('#project_people').each(function() {
    var wrapper = $(this);

    wrapper.on('click', 'a.change_permissions', function(event) {
      App.Delegates.flyoutFormClick.apply(this, [event, {
        'success_event' : 'project_people_updated',
        'width' : 450,
        'success' : function() {
          App.Wireframe.Flash.success('User permissions have been updated');
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to update user permissions. Please try again later');
        },
        'stop_propagation' : true
      }]);

      return false;
    });

    wrapper.on('click', 'a.replace_user', function(event) {
      App.Delegates.flyoutFormClick.apply(this, [event, {
        'success_event' : 'project_people_updated',
        'width' : '500',
        'success' : function(response) {
          App.Wireframe.Flash.success('Selected user has been replaced on this project');
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to replace selected user on this project. Please try again later');
        },
        'stop_propagation' : true
      }]);

      return false;
    });

    wrapper.on('click', 'a.remove_user', function(event) {
      App.Delegates.flyoutFormClick.apply(this, [event, {
        'success_event' : 'project_people_updated',
        'width' : '500',
        'success' : function(response) {
          App.Wireframe.Flash.success('Selected user has been removed from this project');
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to remove selected user from this project. Please try again later');
        },
        'stop_propagation' : true
      }]);

      return false;
    });

    // People added event handler
    App.Wireframe.Events.bind('project_people_created.content', function (event, response) {
      App.Wireframe.Content.reload();
      App.Wireframe.Flash.success('Selected people have been added to the project');
    });

    // People updated event handler
    App.Wireframe.Events.bind('project_people_updated.content', function (event, response) {
      App.Wireframe.Content.reload();
    });
  });
</script><?php }} ?>