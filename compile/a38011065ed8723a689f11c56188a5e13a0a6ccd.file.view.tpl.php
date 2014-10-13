<?php /* Smarty version Smarty-3.1.12, created on 2014-08-19 15:49:07
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:36618262953f371f3b45920-53281132%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a38011065ed8723a689f11c56188a5e13a0a6ccd' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/view.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '36618262953f371f3b45920-53281132',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_template' => 0,
    'default_visibility' => 0,
    'initial_subobjects' => 0,
    'subobjects_url' => 0,
    'reorder_url' => 0,
    'users' => 0,
    'labels_map' => 0,
    'default_labels' => 0,
    'categories_map' => 0,
    'milestones_map' => 0,
    'users_map' => 0,
    'companies_map' => 0,
    'job_types_map' => 0,
    'visual_editor' => 0,
    'add_urls' => 0,
    'mass_edit_urls' => 0,
    'permissions' => 0,
    'shortcuts_url' => 0,
    'unclassified_label' => 0,
    'default_billable_status' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53f371f3d22988_24769762',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53f371f3d22988_24769762')) {function content_53f371f3d22988_24769762($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['active_template']->value->getName();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="template_home_left">
	<div class="template_home_container">
		<div class="box">
			<div class="box-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Positions on the project template<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
			<div class="box-body">
				<div class="data positions"></div>
				<div class="footer">
					<a href="<?php echo smarty_function_assemble(array('route'=>'project_object_template_add','template_id'=>$_smarty_tpl->tpl_vars['active_template']->value->getId(),'object_type'=>'position'),$_smarty_tpl);?>
" class="add position"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add New<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="box-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Attached Files<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
			<div class="box-body">
				<div class="data files">
					<!--<ul id="files_table"></ul>-->
				</div>
				<div class="footer">
					<a href="<?php echo smarty_function_assemble(array('route'=>'project_template_file_add','template_id'=>$_smarty_tpl->tpl_vars['active_template']->value->getId()),$_smarty_tpl);?>
" class="add file_upload"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Upload Files<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="box-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Task Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
			<div class="box-body">
				<div class="data task_categories"></div>
				<div class="footer">
					<a href="<?php echo smarty_function_assemble(array('route'=>'project_object_template_add','template_id'=>$_smarty_tpl->tpl_vars['active_template']->value->getId(),'object_type'=>'category','category_type'=>'task'),$_smarty_tpl);?>
" class="add task category"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add New<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
			<div class="data"></div>
		</div>
		<div class="box">
			<div class="box-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Discussion Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
			<div class="box-body">
				<div class="data discussion_categories"></div>
				<div class="footer">
					<a href="<?php echo smarty_function_assemble(array('route'=>'project_object_template_add','template_id'=>$_smarty_tpl->tpl_vars['active_template']->value->getId(),'object_type'=>'category','category_type'=>'discussion'),$_smarty_tpl);?>
" class="add discussion category"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add New<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="box-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
File Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
			<div class="box-body">
				<div class="data file_categories"></div>
				<div class="footer">
					<a href="<?php echo smarty_function_assemble(array('route'=>'project_object_template_add','template_id'=>$_smarty_tpl->tpl_vars['active_template']->value->getId(),'object_type'=>'category','category_type'=>'file'),$_smarty_tpl);?>
" class="add file category"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add New<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="template_home_right">
	<div id="template_outline"></div>
</div>

<?php echo $_smarty_tpl->getSubTemplate (get_view_path('_initialize_sidebar','project_templates',@SYSTEM_MODULE), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<script type="text/javascript">
	$('#template_outline').templateOutline({
		'initial_object'        : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_template']->value);?>
,
		'default_visibility'    : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['default_visibility']->value);?>
,
		'initial_subobjects'    : <?php echo $_smarty_tpl->tpl_vars['initial_subobjects']->value;?>
,
		'subobjects_url'        : '<?php echo clean($_smarty_tpl->tpl_vars['subobjects_url']->value,$_smarty_tpl);?>
',
		'reorder_url'           : '<?php echo clean($_smarty_tpl->tpl_vars['reorder_url']->value,$_smarty_tpl);?>
',
		'users'                 : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['users']->value);?>
,
		'labels'                : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['labels_map']->value);?>
,
		'default_labels'        : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['default_labels']->value);?>
,
		'categories'            : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['categories_map']->value);?>
,
		'milestones'            : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['milestones_map']->value);?>
,
		'users_map'             : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['users_map']->value);?>
,
		'companies_map'         : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['companies_map']->value);?>
,
		'job_types_map'         : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['job_types_map']->value);?>
,
		'visual_editor'         : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['visual_editor']->value);?>
,
		'add_urls'              : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['add_urls']->value);?>
,
		'mass_edit_urls'        : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['mass_edit_urls']->value);?>
,
		'permissions'           : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['permissions']->value);?>
,
		'shortcuts_url'         : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['shortcuts_url']->value);?>
,
		'unclassified_label'    : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['unclassified_label']->value);?>
,
		'default_billable_status' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['default_billable_status']->value);?>

	});
</script>

<?php if (!$_smarty_tpl->tpl_vars['request']->value->isQuickViewCall()){?>
	<script type="text/javascript">

		// template created
		App.Wireframe.Events.bind('template_created.content', function (event, template) {
			App.Wireframe.Content.setFromUrl(template['urls']['view']);
		});

		App.Wireframe.Events.bind('template_deleted.content', function (event, template) {
			App.Wireframe.Content.setFromUrl('<?php echo smarty_function_assemble(array('route'=>'project_templates'),$_smarty_tpl);?>
');
		});

		App.Wireframe.Events.bind('template_edited.content', function (event, template) {
			App.Wireframe.PageTitle.set(template.name);
		});

	</script>
<?php }?><?php }} ?>