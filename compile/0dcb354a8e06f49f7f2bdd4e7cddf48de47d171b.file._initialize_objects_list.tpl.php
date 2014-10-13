<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:58:38
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/invoicing/views/default/quotes/_initialize_objects_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:94459014153e8afeebdac61-26116320%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0dcb354a8e06f49f7f2bdd4e7cddf48de47d171b' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/invoicing/views/default/quotes/_initialize_objects_list.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '94459014153e8afeebdac61-26116320',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'quotes' => 0,
    'companies_map' => 0,
    'state_map' => 0,
    'print_url' => 0,
    'in_archive' => 0,
    'active_quote' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8afeed0e8c7_84874486',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8afeed0e8c7_84874486')) {function content_53e8afeed0e8c7_84874486($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
?><?php echo smarty_function_use_widget(array('name'=>"objects_list",'module'=>"environment"),$_smarty_tpl);?>


<script type="text/javascript">
  $('#new_quote').flyoutForm({
    'success_event' : 'quote_created',
    'title' : App.lang('New Quote')
  });

  $('#quotes').each(function() {
    var objects_list_wrapper = $(this);

    var items = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['quotes']->value);?>
;
    var mass_edit_url = '<?php echo smarty_function_assemble(array('route'=>'recurring_profiles_mass_edit'),$_smarty_tpl);?>
';
    var companies_map = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['companies_map']->value);?>
;
    var state_map = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['state_map']->value);?>
;
    var print_url = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['print_url']->value);?>
;


    objects_list_wrapper.objectsList({
      'id'                : 'quotes',
      'items'             : items,
      'objects_type'      : 'quotes',
      'required_fields'   : ['id', 'name', 'status', 'company_id', 'permalink'],
      'requirements' : {
        'is_archived' : <?php if ($_smarty_tpl->tpl_vars['in_archive']->value){?>1<?php }else{ ?>0<?php }?>
      },
      'events'            : App.standardObjectsListEvents(),
      'multi_title'       : App.lang(':num Quotes Selected'),
      'multi_url'         : mass_edit_url,
      'print_url'         : print_url,
      'prepare_item'      : function (item) {
        return {
          'id' : item['id'],
          'name' : item['name'],
          'status' : item['status'],
          'company_id' : item['client'] && typeof(item['client']) == 'object' ? item['client']['id'] : item['client_id'],
          'permalink' : item['permalink'],
          'is_archived' : item['state'] == '2' ? 1 : 0
        };
      },

      'grouping'          : [{
        'label' : App.lang("Don't group"),
        'property' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By State'),
        'property' : 'status',
        'map' : state_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-status.png', 'environment'),
        'default' : true
      }, {
        'label' : App.lang('By Client'),
        'property' : 'company_id',
        'map' : companies_map ,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system'),
        'uncategorized_label' : App.lang('Unknown Client')
      }],

      'filtering' : [{
        'label' : App.lang('Status'),
        'property'  : 'status', 'values'  : [{
          'label' : App.lang('All Quotes'),
          'value' : '',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/all-invoices.png', 'invoicing'),
          'default' : true,
          'breadcrumbs' : App.lang('All Quotes')
        }, {
          'label' : App.lang('Drafts'),
          'value' : '0',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/draft-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Drafts')
        }, {
          'label' : App.lang('Sent'),
          'value' : '1',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/issued-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Sent')
        }, {
          'label' : App.lang('Won'),
          'value' : '2',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/won-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Won')
        }, {
          'label' : App.lang('Lost'),
          'value' : '3',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/lost-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Lost')
        }]
      }]
    });

    // quote added
    App.Wireframe.Events.bind('quote_created.content', function (event, quote) {
      objects_list_wrapper.objectsList('add_item', quote);
    });

    // quote updated
    App.Wireframe.Events.bind('quote_updated.content quote_sent.content', function (event, quote) {
      objects_list_wrapper.objectsList('update_item', quote);
    });

    // keep company_id map up to date
    App.objects_list_keep_companies_map_up_to_date(objects_list_wrapper, 'company_id', 'content');

    // redirect to project after it's created from quote
    App.Wireframe.Events.bind('project_created.content', function (event, project) {
      App.Wireframe.Content.setFromUrl(project.permalink);
    });

    // quote deleted
    App.Wireframe.Events.bind('quote_deleted.content', function (event, quote) {
      objects_list_wrapper.objectsList('delete_item', quote.id);
      objects_list_wrapper.objectsList('load_empty');
      return true;
    });

    <?php if ($_smarty_tpl->tpl_vars['active_quote']->value->isLoaded()){?>
    objects_list_wrapper.objectsList('load_item', <?php echo clean($_smarty_tpl->tpl_vars['active_quote']->value->getId(),$_smarty_tpl);?>
, '<?php echo clean($_smarty_tpl->tpl_vars['active_quote']->value->getViewUrl(),$_smarty_tpl);?>
');
    <?php }?>
  });
</script><?php }} ?>