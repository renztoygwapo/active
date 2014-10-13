<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 16:04:10
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:125437186253ac447a30a3f0-95525342%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7fd9ecffbf7272db70d6b4e5433b9f5199636703' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/add.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '125437186253ac447a30a3f0-95525342',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac447aae1690_73975281',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac447aae1690_73975281')) {function content_53ac447aae1690_73975281($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="add_project_form">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>Router::assemble('projects_add'),'class'=>'big_form')); $_block_repeat=true; echo smarty_block_form(array('action'=>Router::assemble('projects_add'),'class'=>'big_form'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php echo $_smarty_tpl->getSubTemplate (get_view_path('_project_form','project','system'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


  <?php if (AngieApplication::behaviour()->isTrackingEnabled()){?>
    <input type="hidden" name="_intent_id" value="<?php echo clean(AngieApplication::behaviour()->recordIntent('project_created'),$_smarty_tpl);?>
">
  <?php }?>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>Router::assemble('projects_add'),'class'=>'big_form'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
  $('#add_project_form').each(function() {
    var wrapper = $(this);
    
    var template_picker = wrapper.find('div.select_project_template select');
    var first_milestone_starts_on_wrapper = wrapper.find('div.first_milestone_starts_on');
	  var template_positions_container = wrapper.find('div.template_positions_container');
	  var template_position = template_positions_container.children('div:first').clone();

	  var positions_url = '<?php echo smarty_function_assemble(array('route'=>'project_template_min_data','template_id'=>"--TEMPLATE-ID--"),$_smarty_tpl);?>
';

    var selected_template_id = template_picker.val() == '' ? 0 : parseInt(template_picker.val());

    if(selected_template_id < 1) {
	    first_milestone_starts_on_wrapper.hide();
	    template_positions_container.hide();
    } // if

    template_picker.change(function() {
	    first_milestone_starts_on_wrapper.hide();
	    template_positions_container.empty().hide();
	    first_milestone_starts_on_wrapper.find('input').attr('enabled', false).attr('required', false);

      var selected_template_id = template_picker.val() == '' ? 0 : parseInt(template_picker.val());

      if(selected_template_id > 0) {
	      $.ajax({
					'url' : positions_url.replace("--TEMPLATE-ID--", template_picker.val()),
		      'success' : function(response) {
			      var is_scheduled;
			      var positions;

			      // check positions
			      if (typeof response['positions'] != 'undefined') {
				      positions = response['positions'];
			      } else {
			        positions = null;
			      } // if

			      // check is scheduled
			      if (typeof response['is_scheduled'] != 'undefined') {
				      is_scheduled = response['is_scheduled'];
			      } else {
				      is_scheduled = false;
			      } // if

			      if (is_scheduled) {
				      first_milestone_starts_on_wrapper.find('input').attr('enabled', true).attr('required', true);
				      first_milestone_starts_on_wrapper.slideDown('fast');
			      } // if

			      if (positions && positions.length) {
				      $.each(positions, function(index, position) {
				        var object = template_position.clone();
					      object.children('label:first').text(position.name);
					      object.children('select:first').attr('name', 'project[project_template_positions]['+position.id+']');

					      if (position.assigned) {
						      object.children('select:first').val(position.assigned.id);
					      } // if

					      template_positions_container.append(object);
				      });

				      template_positions_container.slideDown('fast');
			      } // if
		      }
	      });
      } else {
	      //first_milestone_starts_on_wrapper.slideUp('fast');
	      //template_positions_container.empty().slideUp('fast');
      } // if
    });

	  var based_on_quote_wrapper = wrapper.find('.based_on_quote');
	  based_on_quote_wrapper.find('input:radio').on('click', function() {
		  var show_template_picker = parseInt($(this).val());
		  if (show_template_picker) {
			  template_picker.parent().hide();
			  template_picker.attr('enabled', false);
			  first_milestone_starts_on_wrapper.hide();
			  template_positions_container.empty().hide();
			  first_milestone_starts_on_wrapper.find('input').attr('enabled', false).attr('required', false);
		  } else {
			  template_picker.val(0).parent().show();
			  template_picker.attr('enabled', true);
			  first_milestone_starts_on_wrapper.find('input').attr('enabled', true).attr('required', true);
		  } // if
	  });
  });
</script><?php }} ?>