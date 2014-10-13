<?php /* Smarty version Smarty-3.1.12, created on 2014-10-13 07:37:32
         compiled from "C:\wamp\www\active\activecollab\4.2.6\modules\system\views\default\users\edit_company_and_role.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16233543b8058bd1fe4-82589648%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '80003500afd2e2dbd1103a6397bfce18a54e55fa' => 
    array (
      0 => 'C:\\wamp\\www\\active\\activecollab\\4.2.6\\modules\\system\\views\\default\\users\\edit_company_and_role.tpl',
      1 => 1413185832,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16233543b8058bd1fe4-82589648',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_543b8058dcf5d8_66723121',
  'variables' => 
  array (
    'active_user' => 0,
    'exclude_ids' => 0,
    'user_data' => 0,
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_543b8058dcf5d8_66723121')) {function content_543b8058dcf5d8_66723121($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.form.php';
if (!is_callable('smarty_block_wrap_fields')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap_fields.php';
if (!is_callable('smarty_block_wrap')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap.php';
if (!is_callable('smarty_function_select_company')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/modules/system/helpers\\function.select_company.php';
if (!is_callable('smarty_function_select_manage_by')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/modules/system/helpers\\function.select_manage_by.php';
if (!is_callable('smarty_block_label')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.label.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_function_select_user_role')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/authentication/helpers\\function.select_user_role.php';
if (!is_callable('smarty_block_wrap_buttons')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.submit.php';
if (!is_callable('smarty_function_select_personality_type')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/modules/system/helpers\\function.select_personality_type.php';
if (!is_callable('smarty_function_select_enable_private_url')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/modules/system/helpers\\function.select_enable_private_url.php';
if (!is_callable('smarty_function_text_field')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.text_field.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company and Role<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company and Role<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="user_edit_company_and_role">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditCompanyAndRoleUrl(),'csfr_protect'=>true)); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditCompanyAndRoleUrl(),'csfr_protect'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_fields', array()); $_block_repeat=true; echo smarty_block_wrap_fields(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'company_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'company_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	      <?php echo smarty_function_select_company(array('name'=>'user[company_id]','exclude'=>$_smarty_tpl->tpl_vars['exclude_ids']->value,'value'=>$_smarty_tpl->tpl_vars['user_data']->value['company_id'],'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'optional'=>false,'id'=>'userCompanyId','required'=>true,'can_create_new'=>false,'label'=>'Company'),$_smarty_tpl);?>

	    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'company_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'managed_by_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'managed_by_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_select_manage_by(array('name'=>"user[managed_by_id]",'class'=>'select_managed_by','user_id'=>$_smarty_tpl->tpl_vars['user_data']->value['user_id'],'value'=>$_smarty_tpl->tpl_vars['user_data']->value['managed_by_id'],'label'=>"Managed By",'optional'=>true),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'managed_by_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'role_and_permissions')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'role_and_permissions'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php if (Users::isLastAdministrator($_smarty_tpl->tpl_vars['active_user']->value)){?>
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Role and Permissions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administrator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 &mdash; <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Role of last administrator account can't be changed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
        <?php }else{ ?>
          <?php echo smarty_function_select_user_role(array('name'=>'user','value'=>$_smarty_tpl->tpl_vars['user_data']->value,'required'=>true,'label'=>'Role and Permissions'),$_smarty_tpl);?>

        <?php }?>
      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'role_and_permissions'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_fields(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    	<?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <div class="hiddenPersonalityType" style="display:none">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'personality_type')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'personality_type'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'personality_type')); $_block_repeat=true; echo smarty_block_label(array('for'=>'personality_type'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Personality Type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'personality_type'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        <?php echo smarty_function_select_personality_type(array('name'=>'user[personality_type]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['personality_type'],'class'=>'auto','optional'=>true),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'personality_type'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
    <div class="hiddenPrivateUrlEnable" style="display:none">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'enable_private_url')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'enable_private_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

          <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'enable_private_url')); $_block_repeat=true; echo smarty_block_label(array('for'=>'enable_private_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Enable Private URL<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'enable_private_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

           <?php echo smarty_function_select_enable_private_url(array('name'=>'user[private_url_enabled]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['private_url_enabled'],'class'=>'auto','optional'=>true),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'enable_private_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>

    <div class="hiddenPrivateUrl" style="display:none">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'private_url')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'private_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_text_field(array('name'=>"user[private_url]",'value'=>$_smarty_tpl->tpl_vars['user_data']->value['private_url'],'label'=>'Private Url'),$_smarty_tpl);?>

        <span>.<?php echo clean($_smarty_tpl->tpl_vars['user_data']->value['rep_site_domain'],$_smarty_tpl);?>
</span>
        <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
eg: <i>john</i>.abuckagallon.com<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'private_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditCompanyAndRoleUrl(),'csfr_protect'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
  $(document).ready(function(){
    // appemd personality_type field after role:Client


    var personalityTypeHtml = $('.hiddenPersonalityType').html();
    $('.role_client').prepend(personalityTypeHtml);
    $('.hiddenPersonalityType').html('');


    var privateUrlHtml = $('.hiddenPrivateUrlEnable').html();
    $('.role_subcontractor').prepend(privateUrlHtml);
    $('.hiddenPrivateUrlEnable').html('');

    var privateUrlHtml = $('.hiddenPrivateUrl').html();
    $('.role_subcontractor').prepend(privateUrlHtml);
    $('.hiddenPrivateUrl').html('');

  });

</script><?php }} ?>