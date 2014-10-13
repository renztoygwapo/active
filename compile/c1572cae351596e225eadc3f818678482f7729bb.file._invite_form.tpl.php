<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:39:26
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/people/_invite_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:132672583053a0d49eb6b241-10814659%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1572cae351596e225eadc3f818678482f7729bb' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/people/_invite_form.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '132672583053a0d49eb6b241-10814659',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53a0d49ed50695_15508444',
  'variables' => 
  array (
    'logged_user' => 0,
    'invite_data' => 0,
    'default_project_role_id' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53a0d49ed50695_15508444')) {function content_53a0d49ed50695_15508444($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_function_email_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.email_field.php';
if (!is_callable('smarty_function_link_button')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.link_button.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_select_user_role')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.select_user_role.php';
if (!is_callable('smarty_function_select_company')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_company.php';
if (!is_callable('smarty_function_checkbox')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.checkbox.php';
if (!is_callable('smarty_block_textarea_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.textarea_field.php';
if (!is_callable('smarty_block_wrap_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_fields.php';
if (!is_callable('smarty_function_add_user_projects_select')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.add_user_projects_select.php';
if (!is_callable('smarty_function_select_user_project_permissions')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_user_project_permissions.php';
?><div class="content_stack_wrapper">
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
People<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <table class="people_multi_invite" cellspacing="0">
        <thead>
          <tr>
            <th class="first_name"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
First Name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
            <th class="last_name"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Last Name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
            <th class="email"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: *</th>
          </tr>
        </thead>
        <tbody>
          <tr class="user_row" row_number="0">
            <td class="first_name_input"><?php echo smarty_function_text_field(array('name'=>"invite[users][0][first_name]",'value'=>''),$_smarty_tpl);?>
</td>
            <td class="last_name_input"><?php echo smarty_function_text_field(array('name'=>"invite[users][0][last_name]",'value'=>''),$_smarty_tpl);?>
</td>
            <td class="email_input"><?php echo smarty_function_email_field(array('name'=>"invite[users][0][email]",'value'=>'','required'=>true),$_smarty_tpl);?>
</td>
            <td class="options"></td>
          </tr>
        </tbody>
      </table>

      <div class="invite_buttons">
        <?php echo smarty_function_link_button(array('label'=>"Invite Another User",'icon_class'=>'button_add','id'=>"invite_new"),$_smarty_tpl);?>

      </div>
    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Role<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'role_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'role_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_select_user_role(array('name'=>"invite",'active_user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'value'=>$_smarty_tpl->tpl_vars['invite_data']->value,'label'=>'Role','class'=>'required'),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'role_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'company_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'company_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	      <?php echo smarty_function_select_company(array('name'=>'invite[company_id]','value'=>$_smarty_tpl->tpl_vars['invite_data']->value['company_id'],'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'success_event'=>'company_created','optional'=>false,'label'=>'Company','required'=>true),$_smarty_tpl);?>

	    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'company_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>

  <div class="content_stack_element default_or_specified_behavior">
    <div class="content_stack_element_info">
      <div class="content_stack_optional"><?php echo smarty_function_checkbox(array('name'=>"invite[send_welcome_message]",'class'=>"turn_on",'for_id'=>"subject",'label'=>"Send",'value'=>1,'checked'=>$_smarty_tpl->tpl_vars['invite_data']->value['send_welcome_message']),$_smarty_tpl);?>
</div>
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Welcome Message<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <div class="default_behavior">
        <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System will not email a welcome message to the user. You can do that later on using <b>Send Welcome Message</b> tool that will be available in <b>Options</b> drop-down of the newly created account<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
      </div>

      <div class="specified_behavior">
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'welcome_message')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'welcome_message'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

          <?php $_smarty_tpl->smarty->_tag_stack[] = array('textarea_field', array('name'=>"invite[welcome_message]",'label'=>'Personalize welcome message')); $_block_repeat=true; echo smarty_block_textarea_field(array('name'=>"invite[welcome_message]",'label'=>'Personalize welcome message'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['invite_data']->value['welcome_message'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_textarea_field(array('name'=>"invite[welcome_message]",'label'=>'Personalize welcome message'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New lines will be preserved. HTML is not allowed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
        <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'welcome_message'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      </div>
    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_fields', array()); $_block_repeat=true; echo smarty_block_wrap_fields(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <table>
          <tr>
            <td class="projects_list">
              <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'projects','class'=>"select_projects_add_permissions")); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'projects','class'=>"select_projects_add_permissions"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php echo smarty_function_add_user_projects_select(array('name'=>'projects','user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'show_all'=>true,'label'=>'Select Projects'),$_smarty_tpl);?>

              <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'projects','class'=>"select_projects_add_permissions"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </td>
            <td class="people_permissions">
              <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'user_permissions')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'user_permissions'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php echo smarty_function_select_user_project_permissions(array('name'=>'project_permissions','role_id'=>$_smarty_tpl->tpl_vars['default_project_role_id']->value,'label'=>'Permissions'),$_smarty_tpl);?>

              <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'user_permissions'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </td>
          </tr>
        </table>
      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_fields(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>
</div><?php }} ?>