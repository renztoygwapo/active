<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 16:04:10
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/_project_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:87239374353ac447ab65899-34897127%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c4017d3605e412375078571e6ed320f0c2c3568a' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/_project_form.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '87239374353ac447ab65899-34897127',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'project_data' => 0,
    'active_project' => 0,
    'logged_user' => 0,
    'based_on' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac447b06e6c5_94967650',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac447b06e6c5_94967650')) {function content_53ac447b06e6c5_94967650($_smarty_tpl) {?><?php if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_wrap_editor')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_editor.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_block_editor_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/visual_editor/helpers/block.editor_field.php';
if (!is_callable('smarty_function_select_user')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.select_user.php';
if (!is_callable('smarty_function_select_project_category')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_project_category.php';
if (!is_callable('smarty_function_select_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.select_label.php';
if (!is_callable('smarty_function_select_company')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_company.php';
if (!is_callable('smarty_function_money_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/function.money_field.php';
if (!is_callable('smarty_function_select_currency')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/function.select_currency.php';
if (!is_callable('smarty_function_custom_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/custom_fields/helpers/function.custom_fields.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_yes_no')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.yes_no.php';
if (!is_callable('smarty_function_select_project_template')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_project_template.php';
if (!is_callable('smarty_function_select_due_on')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.select_due_on.php';
if (!is_callable('smarty_function_select_position_template_user')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_position_template_user.php';
?><script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_text_field(array('name'=>"project[name]",'value'=>$_smarty_tpl->tpl_vars['project_data']->value['name'],'id'=>'projectName','class'=>"title required validate_minlength 3",'label'=>"Name",'required'=>true,'maxlength'=>"150"),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_editor', array('field'=>'overview')); $_block_repeat=true; echo smarty_block_wrap_editor(array('field'=>'overview'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('editor_field', array('name'=>"project[overview]",'id'=>'projectOverview','inline_attachments'=>$_smarty_tpl->tpl_vars['project_data']->value['inline_attachments'],'object'=>$_smarty_tpl->tpl_vars['active_project']->value,'images_enabled'=>false)); $_block_repeat=true; echo smarty_block_editor_field(array('name'=>"project[overview]",'id'=>'projectOverview','inline_attachments'=>$_smarty_tpl->tpl_vars['project_data']->value['inline_attachments'],'object'=>$_smarty_tpl->tpl_vars['active_project']->value,'images_enabled'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['project_data']->value['overview'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_editor_field(array('name'=>"project[overview]",'id'=>'projectOverview','inline_attachments'=>$_smarty_tpl->tpl_vars['project_data']->value['inline_attachments'],'object'=>$_smarty_tpl->tpl_vars['active_project']->value,'images_enabled'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_editor(array('field'=>'overview'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  </div>
  
  <div class="form_sidebar form_first_sidebar">
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'leader_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'leader_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_user(array('name'=>"project[leader_id]",'value'=>$_smarty_tpl->tpl_vars['project_data']->value['leader_id'],'label'=>"Leader",'exclude_ids'=>$_smarty_tpl->tpl_vars['project_data']->value['exclude_ids'],'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'optional'=>false,'required'=>true),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'leader_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'category_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'category_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_project_category(array('name'=>"project[category_id]",'value'=>$_smarty_tpl->tpl_vars['project_data']->value['category_id'],'label'=>"Category",'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'optional'=>true,'success_event'=>'category_created'),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'category_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'label_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'label_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_label(array('label'=>"Label",'name'=>'project[label_id]','value'=>$_smarty_tpl->tpl_vars['project_data']->value['label_id'],'type'=>'ProjectLabel','user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'can_create_new'=>false,'optional'=>true),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'label_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'company_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'company_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	  <?php echo smarty_function_select_company(array('name'=>"project[company_id]",'class'=>'project_select_client','value'=>$_smarty_tpl->tpl_vars['project_data']->value['company_id'],'label'=>"Client",'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'success_event'=>'company_created','optional'=>true,'required'=>true),$_smarty_tpl);?>

	  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'company_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	  <?php if (AngieApplication::isModuleLoaded('tracking')&&$_smarty_tpl->tpl_vars['logged_user']->value instanceof User&&$_smarty_tpl->tpl_vars['logged_user']->value->canSeeProjectBudgets()){?>
		  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'budget')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'budget'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		  <?php echo smarty_function_money_field(array('name'=>"project[budget]",'value'=>$_smarty_tpl->tpl_vars['project_data']->value['budget'],'label'=>"Budget"),$_smarty_tpl);?>

		  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'budget'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


		  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'currency_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'currency_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		  <?php echo smarty_function_select_currency(array('name'=>"project[currency_id]",'value'=>$_smarty_tpl->tpl_vars['project_data']->value['currency_id'],'label'=>"Currency",'optional'=>true),$_smarty_tpl);?>

		  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'currency_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	  <?php }?>

	  <?php echo smarty_function_custom_fields(array('name'=>'project','object'=>$_smarty_tpl->tpl_vars['active_project']->value,'object_data'=>$_smarty_tpl->tpl_vars['project_data']->value),$_smarty_tpl);?>

    
  <?php if ($_smarty_tpl->tpl_vars['project_data']->value['based_on_type']&&$_smarty_tpl->tpl_vars['project_data']->value['based_on_type']=='Quote'){?>
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'create_milestones','class'=>'based_on_quote')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'create_milestones','class'=>'based_on_quote'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php ob_start();?><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo "Yes, create them";?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo "Don't create";?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_tmp2=ob_get_clean();?><?php echo smarty_function_yes_no(array('name'=>'project[create_milestones]','data'=>$_smarty_tpl->tpl_vars['project_data']->value['milestones'],'yes_text'=>$_tmp1,'no_text'=>$_tmp2,'label'=>"Milestones based on Quote Items",'id'=>'create_milestones'),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'create_milestones','class'=>'based_on_quote'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['based_on']->value instanceof IProjectBasedOn){?>
  	<input type="hidden" name="project[based_on_type]" value="<?php echo clean(get_class($_smarty_tpl->tpl_vars['based_on']->value),$_smarty_tpl);?>
" />
  	<input type="hidden" name="project[based_on_id]" value="<?php echo clean($_smarty_tpl->tpl_vars['based_on']->value->getId(),$_smarty_tpl);?>
" />
  <?php }?>
  </div>
  
  <div class="form_sidebar form_second_sidebar">
	  <?php if ($_smarty_tpl->tpl_vars['active_project']->value->isNew()){?>
		  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'project_template_id','class'=>'select_project_template')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'project_template_id','class'=>'select_project_template'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		    <?php echo smarty_function_select_project_template(array('name'=>"project[project_template_id]",'value'=>$_smarty_tpl->tpl_vars['project_data']->value['project_template_id'],'label'=>"Project Template"),$_smarty_tpl);?>

		  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'project_template_id','class'=>'select_project_template'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


		  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'starts_on','class'=>'first_milestone_starts_on')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'starts_on','class'=>'first_milestone_starts_on'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		    <?php echo smarty_function_select_due_on(array('name'=>"project[first_milestone_starts_on]",'id'=>"first_milestone_starts_on",'value'=>$_smarty_tpl->tpl_vars['project_data']->value['first_milestone_starts_on'],'label'=>"Project Starts On",'required'=>true),$_smarty_tpl);?>

			  <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select when project should start. System will reschedule all other milestones and assignments based on that start point<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
		  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'starts_on','class'=>'first_milestone_starts_on'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


		  <div class="template_positions_container">
			  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'project_template_position','class'=>'select_project_template_position')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'project_template_position','class'=>'select_project_template_position'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

			    <?php echo smarty_function_select_position_template_user(array('user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'label'=>'Position'),$_smarty_tpl);?>

			  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'project_template_position','class'=>'select_project_template_position'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		  </div>
	  <?php }?>
	</div>
</div><?php }} ?>