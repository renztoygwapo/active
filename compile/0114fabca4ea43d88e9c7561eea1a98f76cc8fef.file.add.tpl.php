<?php /* Smarty version Smarty-3.1.12, created on 2014-08-01 10:21:34
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/users/add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:594802127539bec906d72d8-00731812%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0114fabca4ea43d88e9c7561eea1a98f76cc8fef' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/users/add.tpl',
      1 => 1406740592,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '594802127539bec906d72d8-00731812',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_539bec90999730_93554286',
  'variables' => 
  array (
    'active_company' => 0,
    'user_data' => 0,
    'only_administrator' => 0,
    'active_user' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539bec90999730_93554286')) {function content_539bec90999730_93554286($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_email_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.email_field.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_function_select_user_role')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.select_user_role.php';
if (!is_callable('smarty_function_select_enable_private_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_enable_private_url.php';
if (!is_callable('smarty_function_select_personality_type')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_personality_type.php';
if (!is_callable('smarty_function_select_manage_by')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_manage_by.php';
if (!is_callable('smarty_function_checkbox')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.checkbox.php';
if (!is_callable('smarty_function_password_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.password_field.php';
if (!is_callable('smarty_function_password_rules')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.password_rules.php';
if (!is_callable('smarty_block_textarea_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.textarea_field.php';
if (!is_callable('smarty_function_select_user_project_permissions')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_user_project_permissions.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<div id="new_user">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['active_company']->value->getAddUserUrl(),'csfr_protect'=>true)); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_company']->value->getAddUserUrl(),'csfr_protect'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Email Address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'email')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'email'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_email_field(array('name'=>"user[email]",'value'=>$_smarty_tpl->tpl_vars['user_data']->value['email'],'label'=>"Email",'required'=>true),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'email'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
First Name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'first_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'first_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_text_field(array('name'=>"user[first_name]",'value'=>$_smarty_tpl->tpl_vars['user_data']->value['first_name'],'label'=>'First Name','required'=>true),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'first_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Last Name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'last_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'last_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_text_field(array('name'=>"user[last_name]",'value'=>$_smarty_tpl->tpl_vars['user_data']->value['private_url'],'label'=>'Last Name','required'=>true),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'last_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Role and Permissions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'role_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'role_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'userRole','required'=>'yes')); $_block_repeat=true; echo smarty_block_label(array('for'=>'userRole','required'=>'yes'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Role<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'userRole','required'=>'yes'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


            <?php if ($_smarty_tpl->tpl_vars['only_administrator']->value){?>
              <?php echo clean($_smarty_tpl->tpl_vars['active_user']->value->getRoleName(),$_smarty_tpl);?>

            <?php }else{ ?>
              <?php echo smarty_function_select_user_role(array('name'=>'user','active_user'=>$_smarty_tpl->tpl_vars['active_user']->value,'value'=>$_smarty_tpl->tpl_vars['user_data']->value,'class'=>'required'),$_smarty_tpl);?>

            <?php }?>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'role_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


          <div class="hiddenPrivateUrlEnable" style="display:none">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'enable_private_url')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'enable_private_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'enable_private_url')); $_block_repeat=true; echo smarty_block_label(array('for'=>'enable_private_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Enable Private URL<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'enable_private_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

                 <?php echo smarty_function_select_enable_private_url(array('name'=>'user[private_url_enabled]','value'=>'','class'=>'auto','optional'=>true),$_smarty_tpl);?>

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

          <div class="hiddenPersonalityType" style="display:none">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'personality_type')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'personality_type'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'personality_type')); $_block_repeat=true; echo smarty_block_label(array('for'=>'personality_type'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Personality Type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'personality_type'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

              <?php echo smarty_function_select_personality_type(array('name'=>'user[personality_type]','value'=>'','class'=>'auto','optional'=>true),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'personality_type'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage By<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'managed_by_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'managed_by_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_select_manage_by(array('name'=>"user[managed_by_id]",'class'=>'select_managed_by','value'=>'','label'=>"Managed By",'optional'=>true),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'managed_by_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'title')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'title'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_text_field(array('name'=>'user[title]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['title'],'label'=>'Title'),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'title'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>
      
      <!-- Moved first, last name to top , set both for required=true

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional"><?php echo smarty_function_checkbox(array('name'=>"user[profile_details]",'class'=>"turn_on",'for_id'=>"subject",'label'=>"Specify",'value'=>1,'checked'=>$_smarty_tpl->tpl_vars['user_data']->value['profile_details']),$_smarty_tpl);?>
</div>
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Name and Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="default_behavior">
            <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Full name and title will be left blank. You can always populate these details later on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
          </div>

          <div class="specified_behavior">
            <div class="col">
              <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'first_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'first_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php echo smarty_function_text_field(array('name'=>"user[first_name]",'value'=>$_smarty_tpl->tpl_vars['user_data']->value['first_name'],'label'=>'First Name'),$_smarty_tpl);?>

              <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'first_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </div>

            <div class="col">
              <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'last_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'last_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php echo smarty_function_text_field(array('name'=>"user[last_name]",'value'=>$_smarty_tpl->tpl_vars['user_data']->value['last_name'],'label'=>'Last Name'),$_smarty_tpl);?>

              <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'last_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </div>

            <div class="clear"></div>

            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'title')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'title'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_text_field(array('name'=>'user[title]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['title'],'label'=>'Title'),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'title'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>
        </div>
      </div>
      -->

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional"><?php echo smarty_function_checkbox(array('name'=>"user[specify_password]",'class'=>"turn_on",'for_id'=>"subject",'label'=>"Specify",'value'=>1,'checked'=>$_smarty_tpl->tpl_vars['user_data']->value['specify_password']),$_smarty_tpl);?>
</div>
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="default_behavior">
            <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System will automatically generate a safe password for this account<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
          </div>

          <div class="specified_behavior">
            <div class="col">
              <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'password')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'password'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php echo smarty_function_password_field(array('name'=>'user[password]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['password'],'label'=>'Password'),$_smarty_tpl);?>

              <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'password'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </div>

            <div class="col">
              <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'password_a')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'password_a'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php echo smarty_function_password_field(array('name'=>'user[password_a]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['password_a'],'label'=>'Retype'),$_smarty_tpl);?>

              <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'password_a'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </div>

            <div class="clear"></div>
            <?php echo smarty_function_password_rules(array(),$_smarty_tpl);?>

          </div>
        </div>
      </div>

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional"><?php echo smarty_function_checkbox(array('name'=>"user[send_welcome_message]",'class'=>"turn_on",'for_id'=>"subject",'label'=>"Send Now",'value'=>1,'checked'=>$_smarty_tpl->tpl_vars['user_data']->value['send_welcome_message']),$_smarty_tpl);?>
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

              <?php $_smarty_tpl->smarty->_tag_stack[] = array('textarea_field', array('name'=>"user[welcome_message]",'label'=>'Personalize welcome message')); $_block_repeat=true; echo smarty_block_textarea_field(array('name'=>"user[welcome_message]",'label'=>'Personalize welcome message'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['user_data']->value['welcome_message'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_textarea_field(array('name'=>"user[welcome_message]",'label'=>'Personalize welcome message'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

              <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New lines will be preserved. HTML is not allowed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'welcome_message'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>
        </div>
      </div>

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional"><?php echo smarty_function_checkbox(array('name'=>"user[auto_assign]",'class'=>"turn_on",'for_id'=>"subject",'label'=>"Enabled",'value'=>1,'checked'=>$_smarty_tpl->tpl_vars['user_data']->value['auto_assign']),$_smarty_tpl);?>
</div>
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Auto Assign<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="default_behavior">
            <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System will <b>not</b> add this user to new projects automatically. Administrators and project managers will need to manually add this user to new projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
          </div>

          <div class="specified_behavior">
            <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Set a role or custom permissions to be used when user is automatically added to the project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</p>
            <?php echo smarty_function_select_user_project_permissions(array('name'=>"user",'role_id'=>$_smarty_tpl->tpl_vars['user_data']->value['auto_assign_role_id'],'permissions'=>$_smarty_tpl->tpl_vars['user_data']->value['auto_assign_permissions'],'role_id_field'=>'auto_assign_role_id','permissions_field'=>'auto_assign_permissions'),$_smarty_tpl);?>

          </div>
        </div>
      </div>
    </div>

  <?php if (AngieApplication::behaviour()->isTrackingEnabled()){?>
    <input type="hidden" name="_intent_id" value="<?php echo clean(AngieApplication::behaviour()->recordIntent('user_created'),$_smarty_tpl);?>
">
  <?php }?>
  
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_company']->value->getAddUserUrl(),'csfr_protect'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

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

  }); //document

  $('#new_user').each(function() {
    var wrapper = $(this);

    wrapper.find('div.default_or_specified_behavior').each(function() {
      var section_wrapper = $(this);

      section_wrapper.find('input.turn_on').click(function() {
        if(this.checked) {
          section_wrapper.find('div.default_behavior').hide();
          section_wrapper.find('div.specified_behavior').slideDown(function() {
            var first_input = section_wrapper.find('input[type=text]:first, input[type=password]:first');

            if(first_input.length) {
              first_input.focus();
            } else {
              var first_textarea = section_wrapper.find('textarea:first');

              if(first_textarea.length) {
                first_textarea.focus();
              } // if
            } // if
          });
        } else {
          section_wrapper.find('div.specified_behavior').slideUp(function() {
            section_wrapper.find('div.default_behavior').show();
          });
        } // if
      });
    });
  });
</script><?php }} ?>