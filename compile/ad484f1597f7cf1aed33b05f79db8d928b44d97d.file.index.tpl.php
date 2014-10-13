<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:58:30
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/projects_timeline/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19039872553e8afe6b5a722-25765421%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ad484f1597f7cf1aed33b05f79db8d928b44d97d' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/projects_timeline/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19039872553e8afe6b5a722-25765421',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_project' => 0,
    'day_width' => 0,
    'projects' => 0,
    'diagram_images' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8afe6c01509_86728566',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8afe6c01509_86728566')) {function content_53e8afe6c01509_86728566($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="projects_diagram"></div>

<script type="text/javascript">
	$('#projects_diagram').each(function() {
		var projects_wrapper = $(this);

		projects_wrapper.projectsTimelineDiagram({
			project_id : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->getId());?>
,
			day_width : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['day_width']->value);?>
,
			data : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['projects']->value);?>
,
			work_days : App.Config.get('work_days'),
			days_off : App.Config.get('days_off'),
			skip_days_off : true,
			images : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['diagram_images']->value);?>
,
			reschedule : function (project, start_date, end_date) { },
			// @petar proveriti da li je greskom napisano "start_ate"
			select : function (project, start_ate, end_date) { }
		});
	});

	// Milestones reordered
	App.Wireframe.Events.bind('projects_reordered.content', function (event, milestones) {
		App.Wireframe.Content.reload();
	});
</script><?php }} ?>