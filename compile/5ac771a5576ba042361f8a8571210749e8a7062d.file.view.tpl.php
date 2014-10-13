<?php /* Smarty version Smarty-3.1.12, created on 2014-06-18 17:11:42
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/companies/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1899097679539bec823823f2-46920871%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5ac771a5576ba042361f8a8571210749e8a7062d' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/companies/view.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1899097679539bec823823f2-46920871',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_539bec82462979_32750831',
  'variables' => 
  array (
    'active_company' => 0,
    'logged_user' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539bec82462979_32750831')) {function content_539bec82462979_32750831($_smarty_tpl) {?><?php if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_object')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.object.php';
if (!is_callable('smarty_function_inline_tabs')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.inline_tabs.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Profile<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('object', array('object'=>$_smarty_tpl->tpl_vars['active_company']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value)); $_block_repeat=true; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_company']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <div class="wireframe_content_wrapper" id="company_inline_tabs">
    <?php echo smarty_function_inline_tabs(array('object'=>$_smarty_tpl->tpl_vars['active_company']->value),$_smarty_tpl);?>

  </div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_company']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<script type="text/javascript">
  App.Wireframe.Events.bind('company_updated.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function(event, company) {
    if (company['class'] == 'Company' && company.id == '<?php echo clean($_smarty_tpl->tpl_vars['active_company']->value->getId(),$_smarty_tpl);?>
') {
      var wrapper = $('#company_page_' + company.id);
      var logo_image = wrapper.find('#select_company_icon .properties_icon');
      logo_image.attr('src', company.avatar.photo);
    } // if
  });

  App.Wireframe.Events.bind('company_deleted.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, company) {
    $('#page_title_actions #page_action_add_user').hide();
  });
</script><?php }} ?>