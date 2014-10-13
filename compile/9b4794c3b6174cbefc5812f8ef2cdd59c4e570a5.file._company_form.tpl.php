<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 07:30:08
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/companies/_company_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:84873116853e87100d97219-65638851%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9b4794c3b6174cbefc5812f8ef2cdd59c4e570a5' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/companies/_company_form.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '84873116853e87100d97219-65638851',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'company_data' => 0,
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e87100eb5ec8_29035388',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e87100eb5ec8_29035388')) {function content_53e87100eb5ec8_29035388($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_function_address_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.address_field.php';
if (!is_callable('smarty_function_url_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.url_field.php';
if (!is_callable('smarty_function_company_note_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.company_note_field.php';
?><div class="content_stack_wrapper">
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company Name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 *</h3>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_text_field(array('name'=>'company[name]','value'=>$_smarty_tpl->tpl_vars['company_data']->value['name'],'required'=>true),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Contact Details<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'office_address')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'office_address'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_address_field(array('name'=>'company[office_address]','label'=>"Address",'value'=>$_smarty_tpl->tpl_vars['company_data']->value['office_address']),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'office_address'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'office_phone')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'office_phone'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_text_field(array('name'=>'company[office_phone]','value'=>$_smarty_tpl->tpl_vars['company_data']->value['office_phone'],'label'=>"Phone Number"),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'office_phone'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'office_fax')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'office_fax'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_text_field(array('name'=>'company[office_fax]','value'=>$_smarty_tpl->tpl_vars['company_data']->value['office_fax'],'label'=>"Fax Number"),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'office_fax'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'office_homepage')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'office_homepage'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_url_field(array('name'=>'company[office_homepage]','value'=>$_smarty_tpl->tpl_vars['company_data']->value['office_homepage'],'label'=>"Homepage"),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'office_homepage'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>

<?php if (Companies::canSeeNotes($_smarty_tpl->tpl_vars['logged_user']->value)){?>
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Note<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
      <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company note will be displayed only to people with proper permissions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'note')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'note'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_company_note_field(array('name'=>'company[note]','value'=>$_smarty_tpl->tpl_vars['company_data']->value['note'],'maxlength'=>255),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'note'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>
<?php }?>
</div><?php }} ?>