<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:58:33
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/_initialize_templates.tpl" */ ?>
<?php /*%%SmartyHeaderCode:63995642653e8afe9c35295-24508415%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0ff81a960908a8b5b210e92f7ed8a5d02266619d' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/_initialize_templates.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '63995642653e8afe9c35295-24508415',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'templates' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8afe9c7cfd9_13804773',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8afe9c7cfd9_13804773')) {function content_53e8afe9c7cfd9_13804773($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php echo smarty_function_use_widget(array('name'=>"ui_sortable",'module'=>"environment"),$_smarty_tpl);?>

<script type="text/javascript">

	(function () {
		// @todo create function that will get this data effectively
		var templates = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['templates']->value);?>
;
		var templates_list = $('#templates .templates_list');
		var templates_shelves = $('#templates .templates_shelves');
		var single_template_shelve = $('<div class="templates_shelf"><div class="templates_shelf_inner"><div class="templates_shelf_inner_2"></div></div></div>')
		var templates_message = $('<p class="empty_page templates_empty_page">' + App.lang('Templates list is empty.') + '</p>').insertBefore(templates_shelves);


		/**
		 * Render template
		 *
		 * @param template
		 * @return object
		 */
		var render_template = function (template) {
			var template_jquery = $('<li class="template" template_id="' + template.id + '">' +
				'<span class="drag_handle"></span>' +
				'<a href="' + template.permalink + '" class="template_anchor">' +
				'<span class="title_strap_left"></span><span class="title_strap_right"></span>' +
				'<span class="cover">' +
				'<img src="' + template.avatar.large + '" alt="" />' +
				'</span>' +
				'<span class="template_name"><span class="template_name_inner">' +
				App.clean(template.name) +
				'</span></span>' +
				'</a>' +
				'</li>');

			template_jquery = template_jquery.appendTo(templates_list);

			template_jquery.find('img').load(function () {
				var bottom_spacing = parseInt(template_jquery.find('.template_name').outerHeight());
				template_jquery.find('.title_strap_left').css('bottom', bottom_spacing);
				template_jquery.find('.title_strap_right').css('bottom', bottom_spacing);

				var drag_handle = template_jquery.find('.drag_handle');
				if (drag_handle.length) {
					drag_handle.css('bottom', Math.round(parseInt(bottom_spacing - drag_handle.height()) / 2)).hide();
					template_jquery.hover(function () {
						drag_handle.show();
					}, function () {
						drag_handle.hide();
					});
				} // if
			});

			update_shelves();

			if (templates_list.find('li').length) {
				templates_message.hide();
			} // if

			return template_jquery;
		} // render_template

		/**
		 * Find template with id
		 *
		 * @param template_id
		 * @return jQuery
		 */
		var find_template = function (template_id) {
			var template = templates_list.find('li.template[template_id="' + template_id + '"]');
			return template.length > 0 ? template : null;
		} // find_template

		/**
		 * Delete existing template
		 *
		 * @param mixed template_id
		 */
		var delete_template = function (template_id) {
			if (!template_id) {
				return false;
			} // if

			if (typeof(template_id) == 'object') {
				var template = template_id;
			} else {
				var template = find_template(template_id);
			} // if

			template.remove();
			update_shelves();

			if (templates_list.find('li').length) {
				templates_message.hide();
			} // if
		} // delete_template

		// what happens when template is created
		App.Wireframe.Content.bind('template_created.content', function (event, template) {
			if (template['class'] == 'ProjectTemplate') {
				App.Wireframe.Content.setFromUrl(template['urls']['view']);
			} // if
		});

		// what happens when template is updated
		App.Wireframe.Content.bind('template_updated.content', function (event, template) {
			var current_template = find_template(template.id);
			var template_state = template.state;

			if (current_template && current_template.length) {
				// if project has been changed
				delete_template(current_template);
			} else {
				/*
				if ((in_archive_view && notebook_state == 2) || (!in_archive_view && notebook_state == 3)) {
						render_notebook(notebook);
				}
				*/ // if
			} // if
		});

		//templates reordering
		templates_list.sortable({
			items:'li',
			handle:'span.drag_handle',
			update:function () {
				var new_order = new Array();

				$(this).find('.template').each(function () {
					new_order.push($(this).attr('template_id'));
				});

				$.ajax({
					url:App.Config.get('reorder_templates_url'),
					type:'POST',
					data:{ 'new_order':new_order.toString(), 'submitted':'submitted' },
					success:function (response) {
					}
				});
			}
		});

		/**
		 * Function to update number of shelves on the page
		 *
		 * @param void
		 * @return null
		 */
		var update_shelves = function () {
			templates_shelves.empty().append(single_template_shelve.clone());

			var number_of_rows = Math.ceil(templates_list.height() / templates_shelves.height()) - 1;

			if (number_of_rows > 0) {
				for (var counter = 0; counter < number_of_rows; counter++) {
					templates_shelves.append(single_template_shelve.clone());
				} // for
			} // if
		}; // update_shelves

		/**
		 * Init templates page
		 */
		var init_templates = function () {
			// add existing templates to the list
			if (templates && templates.length) {
				$.each(templates, function (index, template) {
					render_template(template);
				});
			} // if

			// hide empty page message if there are templates in the list
			if (templates_list.find('li').length) {
				templates_message.hide();
			} // if

			// initially update shelves
			update_shelves();

			// update shelves when browser resizes
			App.Wireframe.Content.bind('window_resize.content', update_shelves);
		}; // init_templates

		init_templates();
	}());
</script><?php }} ?>