<?php /* Smarty version Smarty-3.1.12, created on 2014-06-27 06:16:59
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_people/add_people.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12791241953ad0c5b310358-11454391%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '43c41985b5b3276384e6cba01447df1cddcb9543' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_people/add_people.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12791241953ad0c5b310358-11454391',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'is_empty_select' => 0,
    'active_project' => 0,
    'logged_user' => 0,
    'exclude_users' => 0,
    'default_project_role_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ad0c5b4440b3_10594423',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ad0c5b4440b3_10594423')) {function content_53ad0c5b4440b3_10594423($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_wrap_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_fields.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_select_users')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.select_users.php';
if (!is_callable('smarty_function_select_user_project_permissions')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_user_project_permissions.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add Users<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="add_people">
<?php if ($_smarty_tpl->tpl_vars['is_empty_select']->value){?>
  <p class="empty_page"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There are no users to be added<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
<?php }else{ ?>
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['active_project']->value->getAddPeopleUrl(),'method'=>'post')); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_project']->value->getAddPeopleUrl(),'method'=>'post'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_fields', array('style'=>"min-height: 300px;")); $_block_repeat=true; echo smarty_block_wrap_fields(array('style'=>"min-height: 300px;"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'users','visible_overflow'=>true)); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'users','visible_overflow'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php if ($_smarty_tpl->tpl_vars['logged_user']->value->isOwner()||$_smarty_tpl->tpl_vars['logged_user']->value->isAdministrator()||$_smarty_tpl->tpl_vars['logged_user']->value->isProjectManager()||$_smarty_tpl->tpl_vars['logged_user']->value->isPeopleManager()){?>
          <?php echo smarty_function_select_users(array('name'=>'users','exclude'=>$_smarty_tpl->tpl_vars['exclude_users']->value,'label'=>'Select Users','user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'mode'=>'input','width'=>600,'required'=>true),$_smarty_tpl);?>

        <?php }else{ ?>
          <?php echo smarty_function_select_users(array('name'=>'users','object'=>$_smarty_tpl->tpl_vars['logged_user']->value->getCompany(),'exclude'=>$_smarty_tpl->tpl_vars['exclude_users']->value,'label'=>'Select Users','user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'mode'=>'input','width'=>600,'required'=>true),$_smarty_tpl);?>

        <?php }?>
      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'users','visible_overflow'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'user_permissions')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'user_permissions'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_select_user_project_permissions(array('name'=>'project_permissions','role_id'=>$_smarty_tpl->tpl_vars['default_project_role_id']->value,'label'=>'Permissions','required'=>true),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'user_permissions'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_fields(array('style'=>"min-height: 300px;"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add People<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_project']->value->getAddPeopleUrl(),'method'=>'post'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
</div>

<script type="text/javascript">
    // this is to make chosen control required
    var form = $('#add_people');
    var control = form.find('.chzn-done');
    var wrapper = control.parents('div.select_users_input:first');
    var chosen_container = form.find('.chzn-container:first');

    control.attr('required', 'required');
    control.show().css({
      'position'        : 'absolute',
      'height'          : chosen_container.height(),
      'padding-top'     : '0px',
      'padding-bottom'  : '0px'
    });
</script><?php }} ?>