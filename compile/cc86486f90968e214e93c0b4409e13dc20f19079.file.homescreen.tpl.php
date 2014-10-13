<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:57:15
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/homescreens/views/default/fw_homescreen/homescreen.tpl" */ ?>
<?php /*%%SmartyHeaderCode:143630214353e8af9b0e4267-25120231%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cc86486f90968e214e93c0b4409e13dc20f19079' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/homescreens/views/default/fw_homescreen/homescreen.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '143630214353e8af9b0e4267-25120231',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_object' => 0,
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8af9b1b4ac6_57143275',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8af9b1b4ac6_57143275')) {function content_53e8af9b1b4ac6_57143275($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_configure_homescreen')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/homescreens/helpers/function.configure_homescreen.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Configure Custom Home Screen Tabs<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Custom Home Screen Tab<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="manage_own_homescreen">
  <?php echo smarty_function_configure_homescreen(array('parent'=>$_smarty_tpl->tpl_vars['active_object']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'id'=>'configure_user_homescreen'),$_smarty_tpl);?>

  <p class="empty_page" style="display: none"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No custom tabs defined<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
</div>

<script type="text/javascript">
  $('#manage_own_homescreen').each(function() {
    var wrapper = $(this);

    var configure_home_screen = wrapper.find('#configure_user_homescreen');
    var no_tabs_message = wrapper.find('p.empty_page');

    /**
     * Refresh element visibility
     */
    var refresh_view = function() {
      if(configure_home_screen.find('li.homescreen_tab.real_homescreen_tab').length < 1) {
        configure_home_screen.hide();
        no_tabs_message.show();
      } else {
        no_tabs_message.hide();
        configure_home_screen.show();
      } // if
    } // if

    App.Wireframe.Events.bind('homescreen_tab_added.content homescreen_tab_deleted.content', function(e, tab) {
      refresh_view();
    });

    refresh_view();
  });
</script><?php }} ?>