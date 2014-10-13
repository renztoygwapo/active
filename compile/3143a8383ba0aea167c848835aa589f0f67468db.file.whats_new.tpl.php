<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 07:20:09
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/backend/whats_new.tpl" */ ?>
<?php /*%%SmartyHeaderCode:116766630753e86ea92655f2-86601632%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3143a8383ba0aea167c848835aa589f0f67468db' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/backend/whats_new.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '116766630753e86ea92655f2-86601632',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logged_user' => 0,
    'activity_logs' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e86ea9520823_29135264',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e86ea9520823_29135264')) {function content_53e86ea9520823_29135264($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_welcome_message')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.welcome_message.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_user_announcements')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/announcements/helpers/function.user_announcements.php';
if (!is_callable('smarty_function_user_reminders')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/reminders/helpers/function.user_reminders.php';
if (!is_callable('smarty_function_my_projects')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.my_projects.php';
if (!is_callable('smarty_function_activity_log')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/activity_logs/helpers/function.activity_log.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
What's New<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
What's New<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="whats_new">
  <div id="whats_new_sidebar">
  <?php if ($_smarty_tpl->tpl_vars['logged_user']->value instanceof Client||$_smarty_tpl->tpl_vars['logged_user']->value instanceof Subcontractor){?>
    <div id="whats_new_welcome" class="whats_new_sidebar_widget">
      <?php echo smarty_function_welcome_message(array('show_title'=>true),$_smarty_tpl);?>

    </div>
  <?php }?>

    <div id="whats_new_announcements" class="whats_new_sidebar_widget user_announcements_wrapper">
      <h3 class="head"><span class="head_inner"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Announcements<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></h3>
      <div class="body"><div class="body_inner"><?php echo smarty_function_user_announcements(array('user'=>$_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>
</div></div>
    </div>

    <div id="whats_new_reminders" class="whats_new_sidebar_widget user_reminders_wrapper">
      <h3 class="head"><span class="head_inner"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Reminders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></h3>
      <div class="body"><div class="body_inner"><?php echo smarty_function_user_reminders(array('user'=>$_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>
</div></div>
    </div>

    <div id="whats_new_my_projects" class="whats_new_sidebar_widget">
      <h3 class="head"><span class="head_inner"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
My Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></h3>
      <div class="body"><div class="body_inner"><?php echo smarty_function_my_projects(array('user'=>$_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>
</div></div>
    </div>
  </div>

  <div id="whats_new_recent_activities">
    <?php echo smarty_function_activity_log(array('user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'activity_logs'=>$_smarty_tpl->tpl_vars['activity_logs']->value),$_smarty_tpl);?>

  </div>
</div>

<script type="text/javascript">
  $('#whats_new').each(function() {
    var wrapper = $(this);

    var activities_wrapper = wrapper.find('#whats_new_recent_activities');
    var wireframe_content_height = $('#wireframe_content').height() - 33;

    if(activities_wrapper.height() < wireframe_content_height) {
      activities_wrapper.height(wireframe_content_height + 'px');
    } // if
  });
</script><?php }} ?>