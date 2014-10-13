{title}Quotes{/title}
{add_bread_crumb}All Quotes{/add_bread_crumb}
{use_widget name="objects_list" module="environment"}

<div id="quotes">
  <div class="empty_content">
      <div class="objects_list_title">{lang}Quotes{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/proposals.png' module=invoicing}" alt=""/></div>

      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a quote and load its details, please click on it in the list on the left{/lang}</li>
          <!--<li>{lang}It is possible to select multiple quotes at the same time. Just hold Ctrl key on your keyboard and click on all quotes that you want to select{/lang}</li>-->
        </ul>
      </div>
  </div>
  
  <!--<div class="multi_content"></div>-->
</div>

<script type="text/javascript">
  $('#quotes').each(function() {
    var objects_list_wrapper = $(this);
    
    var items = {$quotes|json nofilter};
    var state_map = {$status_map|json nofilter};
    var print_url = '{assemble route=quotes print=1}';
    
    objects_list_wrapper.objectsList({
      'id'                : 'quotes',
      'items'             : items,      
      'objects_type'      : 'quotes',
      'required_fields'   : ['id', 'name', 'status', 'company_id', 'permalink'],
      'events'            : App.standardObjectsListEvents(),
      'multi_title'       : App.lang(':num Quotes Selected'),
      'multi_url'         : '',
      'print_url'         : print_url,
      'prepare_item'      : function (item) {
        return {
          'id' : item['id'],
          'name' : item['name'],
          'status' : item['status'],
          'permalink' : item['permalink']
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

    {if $active_quote->isLoaded()}
  	objects_list_wrapper.objectsList('load_item', {$active_quote->getId()}, {$active_quote->getCompanyViewUrl()|json nofilter});
    {/if}
  });
</script>