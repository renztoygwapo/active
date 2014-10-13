<?php /* Smarty version Smarty-3.1.12, created on 2014-08-19 15:49:07
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/_initialize_sidebar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:189634222653f371f3d32a15-64960363%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7470816231ce534f385b775c680b8106d6cc2c37' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/_initialize_sidebar.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '189634222653f371f3d32a15-64960363',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'positions' => 0,
    'task_categories' => 0,
    'discussion_categories' => 0,
    'file_categories' => 0,
    'files' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53f371f3df3052_98263117',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53f371f3df3052_98263117')) {function content_53f371f3df3052_98263117($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><script type="text/javascript">

	(function() {
		var positions             = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['positions']->value);?>
;
		var task_categories       = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['task_categories']->value);?>
;
		var discussion_categories = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['discussion_categories']->value);?>
;
		var file_categories       = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['file_categories']->value);?>
;
		var files                 = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['files']->value);?>
;

		/**
		 * Render Box Item
		 *
		 * @param object item
		 * @param jQuery parent
		 * @return jQuery item_jquery
		 */
		var render_position = function(position, as_object) {
			var render = '';

			if (typeof position == 'undefined' || !position) return;

			render += '<div class="item" item_id="' + position.id + '">';
			render +=   '<span class="item_name">' + position.name + '</span>';
			if (typeof position.permissions != "undefined") {
				render +=   '<div class="controls">';
				if (typeof position.permissions.can_edit != "undefined" && position.permissions.can_edit) {
					render +=     '<a href="' + position.urls.edit + '" class="edit position">';
					render +=       '<img src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/edit.png', 'environment') + '" />';
					render +=     '</a>';
				}
				if (typeof position.permissions.can_delete != "undefined" && position.permissions.can_delete) {
					render +=     '<a href="' + position.urls.delete + '" class="delete position">';
					render +=       '<img src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment') + '" />';
					render +=     '</a>';
				}
				render +=   '</div>';
			}
			if (typeof position['assigned'] != 'undefined' && position['assigned']) {
				render += render_assigned(position['assigned']);
			} // if
			render += '</div>';

			return as_object ? $(render) : render;
		} // render_position

		/**
		 * Render Category (Task, Discussion, File)
		 *
		 * @param category
		 * @param context
		 * @param as_object
		 * @returns jQuery
		 */
		var render_category = function(category, context, as_object) {
			var render = '';

			if (typeof category == 'undefined' || !category) return;

			render += '<div class="item" item_id="' + category.id + '">';
			render +=   '<span class="item_name">' + category.name + '</span>';
			if (typeof category.permissions != "undefined") {
				render +=   '<div class="controls">';
				if (typeof category.permissions.can_edit != "undefined" && category.permissions.can_edit) {
					render +=     '<a href="' + category.urls.edit + '" class="edit ' + context + ' category">';
					render +=       '<img src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/edit.png', 'environment')  + '" />';
					render +=     '</a>';
				}
				if (typeof category.permissions.can_delete != "undefined" && category.permissions.can_delete) {
					render +=     '<a href="' + category.urls.delete + '" class="delete ' + context + ' category">';
					render +=       '<img src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment')  + '" />';
					render +=     '</a>';
				}
				render +=   '</div>';
			}
			render += '</div>';

			return as_object ? $(render) : render;
		} // render_category

		/**
		 * Rener File
		 *
		 * @param file
		 * @param as_object
		 * @returns jQuery
		 */
		var render_file = function(file, as_object) {
			var render = '';

			render += '<div class="item">';
			render +=   '<div class="icon">';
			render +=     '<img src="' + file.icon + '" />';
			render +=   '</div>';
			render +=   '<div class="info">';
			render +=     '<div class="top">' + file.name + '</div>';
			render +=     '<div class="bottom">';
			render +=       '<span class="file_size">' + file.file_size + '</span>';
			render +=     '</div>';
			render +=   '</div>';
			render +=   '<div class="controls">';
			render +=     '<a href="' + file['urls']['delete'] + '" class="delete file_upload">';
			render +=     	'<img src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment')  + '" />';
			render +=     '</a>';
			render +=   '</div>';
			render += '</div>';

			return as_object ? $(render) : render;
		} // render_file

		/**
		 * Render Assignee
		 *
		 * @param user
		 * @param as_object
		 * @returns jQuery
		 */
		var render_assigned = function(user, as_object) {
			var render = "";

			if (typeof(user) == "undefined" || !user) return;

			render += '<div class="assigned">';
			render +=   '<img class="assigned_avatar" src="' + user.avatar + '" />';
			render += '</div>';

			return as_object ? $(render) : render;
		} // render_assigned

		/**
		 * Flash background on add, edit or triggered event
		 */
		var flash_background = function() {
			var object = $(this);
			object.stop().css("background-color", "#FFFF9C").animate({ backgroundColor: "#FFFFFF"}, 1500);
		} // flash_background

		/**
		 * Handle Interaction
		 */
		var handle_interaction = function() {
			var wrapper = $(this);
			var wrapper_dom = this;

			// on delete click
			wrapper.on('click', 'a.delete', function(dom_event) {
				var object = $(this);
				var parent = object.parents('div.item:first');
				var context = "";

				if (object.hasClass('task')) {
					context = "task_category";
				} else if (object.hasClass('discussion')) {
					context = "discussion_category";
				} else if (object.hasClass('file')) {
					context = "file_category";
				} else if (object.hasClass('file_upload')) {
					context = "file";
				} else if (object.hasClass('position')) {
					context = "position";
				} else {
					return false;
				} // if

				$.ajax({
					'url'   : object.attr('href'),
					'type'  : 'POST',
					'data'  : {
						'submitted' : 'submitted'
					},
					success: function() {
						var data_to_send = {
							id : parent.attr('item_id')
						};
						if (context == 'position') {
							data_to_send['company_id'] = 0;
						} // if
						App.Wireframe.Events.trigger(context + '_template_deleted', data_to_send);
						parent.remove();
					}
				});

				return false;
			});

			// on add click
			wrapper.on('click', 'a.add', function(dom_event) {
				var object = $(this);
				var url = object.attr('href');
				var title = "";
				var context = "";

				// positions
				if (object.hasClass('position')) {
					App.Delegates.flyoutFormClick.apply(this, [dom_event, {
						'width' : '500',
						'title' : App.lang('Add position'),
						'success_event' : 'position_template_created'
					}]);
					return false;
				} //if

				// files
				if (object.hasClass('file_upload')) {
					App.Delegates.flyoutFormClick.apply(this, [dom_event, {
						'title' : App.lang('Upload Files'),
						'success_event' : 'files_uploaded'
					}]);
					return false;
				} // if

				if (object.hasClass('task')) {
					title = App.lang('Add new task category');
					context = "task";
				} else if (object.hasClass('discussion')) {
					title = App.lang('Add new discussion category');
					context = "discussion";
				} else if (object.hasClass('file')) {
					title = App.lang('Add new file category');
					context = "file";
				} else {
					return false;
				} // if

				var name = prompt(title + ':',"");
				if (name != null && name != "") {
					name = $.trim(name);
					var data = {
						'submitted' : 'submitted',
						'category'  : {
							'name': name
						}
					};
					$.ajax({
						'url'         : url,
						'data'        : data,
						'type'        : 'POST',
						'success'     : function (category) {
							if (!category) {
								App.Wireframe.Flash.error(App.lang('Insert :context category failed!', { context : context }));
							} // if

							var item = render_category(category, context, true);
							wrapper.find('div.data.' + context + '_categories').append(item);
							flash_background.apply(item.get(0));

							App.Wireframe.Events.trigger(context + '_category_template_created', category);
						},
						'error'       : function (category) {
							App.Wireframe.Flash.error(App.lang('Insert :context category failed!', { context : context }));
						}
					});
				} // if

				return false;
			});

			// on click edit
			wrapper.on('click', 'a.edit', function(dom_event) {
				var object = $(this);
				var url = object.attr('href');
				var title = "";
				var context = "";

				// positions
				if (object.hasClass('position')) {
					App.Delegates.flyoutFormClick.apply(this, [dom_event, {
						'width' : '500',
						'title' : App.lang('Edit position'),
						'success_event' : 'position_template_updated'
					}]);
					return false;
				} //if

				// categories
				if (object.hasClass('task')) {
					title = App.lang('Edit task category');
					context = "task";
				} else if (object.hasClass('discussion')) {
					title = App.lang('Edit discussion category');
					context = "discussion";
				} else if (object.hasClass('file')) {
					title = App.lang('Edit file category');
					context = "file";
				} else {
					return false;
				} // if

				var current_name = object.parents('div.item:first').find('span.item_name').text();
				var new_name = prompt(title + ':', current_name);
				if (new_name != null && new_name != "") {
					new_name = $.trim(new_name);
					var data = {
						'submitted' : 'submitted',
						'category'  : {
							'name': new_name
						}
					};
					$.ajax({
						'url' : url,
						'type' : 'POST',
						'data' :data,
						success: function(category) {
							App.Wireframe.Events.trigger(context + '_category_template_updated', category);
						},
						error: function() {
							object.parents('div.item:first').find('span.item_name').text(current_name);
							App.Wireframe.Flash.error('Edit category failed!');
						}
					});

					object.parents('div.item:first').find('span.item_name').text(new_name);
					flash_background.apply(object.parents('div.item:first').get(0));
				} // if

				return false;
			});
		};

		/**
		 * Handle all events and update the internal storage and displayed rows with it
		 */
		var handle_events = function () {
			var wrapper = $(this);
			var wrapper_dom = this;

			App.Wireframe.Events.bind('position_template_created.content', function (event, position) {
				var assinged = null;
				if (typeof position.permissions.can_assign != "undefined" && position.permissions.can_assign) {
					assinged = render_assigned(position.assigned);
				} // if

				var item_jquery = render_position(position, true);
				item_jquery.append(assinged);
				wrapper.find('div.data.positions').append(item_jquery);
				flash_background.apply(item_jquery.get(0));
			});

			App.Wireframe.Events.bind('position_template_updated.content', function (event, position) {
				var cur_item = wrapper.find('div.item[item_id="' + position.id + '"]');

				cur_item.find(".item_name").text(position.name);
				var assigned = cur_item.find(".assigned");
				if (!assigned.length) {
					cur_item.append(render_assigned(position.assigned));
				} else {
					assigned.find(".assigned_avatar").attr("src", position.assigned.avatar);
				} // if
				flash_background.apply(cur_item.get(0));
			});

			App.Wireframe.Events.bind('file_category_created.content', function(event, category) {
				var category = render_category(category, 'file', true);
				wrapper.find('div.data.file_categories').append(category);
				flash_background.apply(category.get(0));
			});

			App.Wireframe.Events.bind('files_uploaded.content', function(event, files) {
				$.each(files, function(index, file) {
					var object = render_file(file, true);
					wrapper.find('div.data.files').append(object);
					flash_background.apply(object.get(0));
				});
			});
		};

		/**
		 * Initialize Side Boxes
		 */
		var init_sidebar = function() {
			var wrapper = $('#template_home_left div.box');

			// define variables
			var rendered_positions = "";
			var rendered_files = "";
			var rendered_task_categories = "";
			var rendered_discussion_categories = "";
			var rendered_file_categories = "";

			// render positions
			$.each(positions, function(index, position) {
				rendered_positions += render_position(position);
			});
			if (rendered_positions) {
				wrapper.find('div.data.positions').append(rendered_positions);
			} // if

			// render files
			$.each(files, function(index, file) {
				rendered_files += render_file(file, false);
			});
			if (rendered_files) {
				wrapper.find('div.data.files').append(rendered_files);
			} // if

			// render task categories
			$.each(task_categories, function(index, category) {
				rendered_task_categories += render_category(category, 'task');
			});
			if (task_categories) {
				wrapper.find('div.data.task_categories').append(rendered_task_categories);
			} // if

			// render discussion categories
			$.each(discussion_categories, function(index, category) {
				rendered_discussion_categories += render_category(category, 'discussion');
			});
			if (task_categories) {
				wrapper.find('div.data.discussion_categories').append(rendered_discussion_categories);
			} // if

			// render file categories
			$.each(file_categories, function(index, category) {
				rendered_file_categories += render_category(category, 'file');
			});
			if (file_categories) {
				wrapper.find('div.data.file_categories').append(rendered_file_categories);
			} // if

			// handle all interaction
			handle_interaction.apply(wrapper);

			// handle all events
			handle_events.apply(wrapper);
		};

		// Initialize Sidebar
		init_sidebar();

	}());

</script><?php }} ?>