<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:59:36
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_outline/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:143181536253e8b028999a02-35276313%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd97d8b4da768150934d03cbca2f6d3fed0986e09' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_outline/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '143181536253e8b028999a02-35276313',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_project' => 0,
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
    'add_urls' => 0,
    'mass_edit_urls' => 0,
    'permissions' => 0,
    'shortcuts_url' => 0,
    'unclassified_label' => 0,
    'default_billable_status' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8b028a75686_81450705',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8b028a75686_81450705')) {function content_53e8b028a75686_81450705($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_modifier_map')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.map.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project Outline<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project Outline<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="project_outline"></div>

<script type="text/javascript">
  $('#project_outline').projectOutline({
    'initial_object' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value);?>
,
    'default_visibility' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['default_visibility']->value);?>
,
    'initial_subobjects' : <?php echo $_smarty_tpl->tpl_vars['initial_subobjects']->value;?>
,
    'subobjects_url' : '<?php echo clean($_smarty_tpl->tpl_vars['subobjects_url']->value,$_smarty_tpl);?>
',
    'reorder_url' : '<?php echo clean($_smarty_tpl->tpl_vars['reorder_url']->value,$_smarty_tpl);?>
',
    'users' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['users']->value);?>
,
    'labels' : <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['labels_map']->value);?>
,
    'default_labels' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['default_labels']->value);?>
,
    'categories' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['categories_map']->value);?>
,
    'milestones' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['milestones_map']->value);?>
,
    'users_map' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['users_map']->value);?>
,
    'companies_map' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['companies_map']->value);?>
,
    'job_types_map' : <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['job_types_map']->value);?>
,
    'add_urls' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['add_urls']->value);?>
,
    'mass_edit_urls' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['mass_edit_urls']->value);?>
,
    'permissions' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['permissions']->value);?>
,
    'shortcuts_url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['shortcuts_url']->value);?>
,
    'unclassified_label' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['unclassified_label']->value);?>
,
    'default_billable_status' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['default_billable_status']->value);?>

  });
</script><?php }} ?>