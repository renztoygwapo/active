<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 13:12:26
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/documents/views/default/documents/_initialize_objects_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:203624617053e8c13a339820-13002553%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b46c5eab357a24ab8dfb77339d999a0c4df525bd' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/documents/views/default/documents/_initialize_objects_list.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '203624617053e8c13a339820-13002553',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'documents' => 0,
    'categories' => 0,
    'letters' => 0,
    'mass_manager' => 0,
    'in_archive' => 0,
    'active_document' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8c13a3c14e3_73544291',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8c13a3c14e3_73544291')) {function content_53e8c13a3c14e3_73544291($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_modifier_map')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.map.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
?><?php echo smarty_function_use_widget(array('name'=>"objects_list",'module'=>"environment"),$_smarty_tpl);?>


<script type="text/javascript">
  $('#documents').each(function() {
    var objects_list_wrapper = $(this);

    var items = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['documents']->value);?>
;
    var categories_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['categories']->value);?>
;
    var letters_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['letters']->value);?>
;
    var print_url = '<?php echo smarty_function_assemble(array('route'=>'documents','print'=>1),$_smarty_tpl);?>
';
    var mass_edit_url = '<?php echo smarty_function_assemble(array('route'=>'documents_mass_edit'),$_smarty_tpl);?>
';

    var init_options = {
      'id'                 : 'global_documents',
      'items'              : items,
      'required_fields'    : ['id', 'name', 'first_letter', 'category_id', 'permalink'],
      'requirements'       : {},
      'objects_type'       : 'documents',
      'print_url'          : print_url,
      'events'             : App.standardObjectsListEvents(),
      'multi_title'        : App.lang(':num Documents Selected'),
      'multi_url'          : mass_edit_url,
      'multi_actions' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['mass_manager']->value);?>
,
      'prepare_item'       : function (item) {
        var result = {
          'id' : item['id'],
          'name' : item['name'],
          'first_letter' : item['first_letter'],
          'permalink' : item['permalink'],
          'is_archived' : item['is_archived'],
          'is_pinned' : item['is_pinned'],
          'is_favorite' : item['is_favorite'],
          'is_trashed' : item['state'] == '1' ? 1 : 0,
          'visibility'    : item['visibility']
        };

        if(typeof(item['category']) == 'undefined') {
          result['category_id'] = item['category_id'];
        } else {
          result['category_id'] = item['category'] ? item['category']['id'] : 0;
        } // if

        return result;
      },
      'render_item'        : function(item) {
        return '<td class="name">' + App.clean(item.name) + App.Wireframe.Utils.renderVisibilityIndicator(item['visibility']) + '</td><td class="task_options"></td>';
      },
      'grouping' : [{
        'label' : App.lang("Don't group"),
        'property' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By Category'),
        'property' : 'category_id' ,
        'map' : categories_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories')
      }, {
        'label' : App.lang('By Name'),
        'property' : 'first_letter',
        'map' : letters_map ,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-name.png', 'environment'),
        'uncategorized_label' : App.lang('*'), 'default' : true
      }],
      'filtering' : []
    };

    if (!<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['in_archive']->value);?>
) {
      init_options.requirements.is_archived = 0;
    } else {
      init_options.requirements.is_archived = 1;
    } // if

    objects_list_wrapper.objectsList(init_options);

    // document added
    if (!<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['in_archive']->value);?>
) {
      App.Wireframe.Events.bind('document_created.content', function (event, document) {
        objects_list_wrapper.objectsList('add_item', document);
      });
    } // if

    // document updated
    App.Wireframe.Events.bind('document_updated.content', function (event, document) {
      objects_list_wrapper.objectsList('update_item', document);

      App.Wireframe.PageTitle.set(App.clean(document.name));
      $('.object_body .object_body_content').html(document.body);
      App.Wireframe.Flash.success(App.lang('Document has been updated'));
    });

    // document deleted
    App.Wireframe.Events.bind('document_deleted.content', function (event, document) {
      if (objects_list_wrapper.objectsList('is_loaded', document['id'], false)) {
        objects_list_wrapper.objectsList('load_empty');
      } // if
      objects_list_wrapper.objectsList('delete_item', document['id']);
    });

    // Kepp categories map up to date
    App.objects_list_keep_categories_map_up_to_date(objects_list_wrapper, 'category_id', <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_document']->value->category()->getCategoryContextString());?>
, <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_document']->value->category()->getCategoryClass());?>
);

    $('#documents_new_text_document').flyoutForm({
      'title' : App.lang('New Text Document'),
      'success_event' : 'document_created',
      'success_message' : App.lang('Text document has been created')
    });

    $('#documents_upload_document').flyoutForm({
      'title' : App.lang('Upload File'),
      'success_event' : 'document_created',
      'success_message' : App.lang('File has been uploaded')
    });

  <?php if ($_smarty_tpl->tpl_vars['active_document']->value->isLoaded()){?>
    objects_list_wrapper.objectsList('load_item', <?php echo clean($_smarty_tpl->tpl_vars['active_document']->value->getId(),$_smarty_tpl);?>
, <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_document']->value->getViewUrl());?>
); // Pre select item if this is permalink
  <?php }?>
  });
</script><?php }} ?>