{title}Languages{/title}
{add_bread_crumb}Details{/add_bread_crumb}
{use_widget name="objects_list" module="environment"}
{use_widget name="form" module="environment"}

<div id="languages"> 
  <div class="empty_content">
      <div class="objects_list_title">{lang}Language{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='admin_panel/languages.png' module=$smarty.const.GLOBALIZATION_FRAMEWORK}" alt=""/></div>
      <div class="objects_list_details_actions">
        <ul>
          <li><a href="{assemble route='admin_languages_add'}" id="new_language">{lang}New Language{/lang}</a></li>
          <li><a href="{assemble route='admin_languages_set_default'}" id="set_default_language">{lang}Set Default Language{/lang}</a></li>
          {if $import_enabled}<li><a href="#" id="import_language">{lang}Import Language{/lang}</a></li>{/if}
        </ul>
      </div>
      
     	{if $import_enabled}
      <div class="upload_to_flyout import_language">
      	{form method=post action=$import_url id="import_form" enctype="multipart/form-data" class="import_language_form"}
          {wrap field=xml}
            {label for=xml}Select Language XML File{/label}
            {file_field name=xml id=xml}
          {/wrap}
          
          {submit}Import{/submit} 
      	{/form}
      </div>
    	{else}
			<div class="objects_list_details_actions">
        <p>{lang}Importing is not enabled, please review errors{/lang}</p>
      </div>
    	{/if}
      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a language and load its details, please click on it in the list on the left{/lang}</li>
        </ul>
      </div>  
  </div>
</div>

<script type="text/javascript">
  $('#new_language').flyoutForm({
    'success_event' : 'language_created',
    'title' : App.lang('New Language'),
    'width' : 'narrow'
  });
  
  $('#set_default_language').flyoutForm({
    'title' : App.lang('Set Default Language'),
    'width' : 'narrow'
  });

	$('#languages').each(function() {
	  var wrapper = $(this);
    
    wrapper.objectsList({
      'id' : 'languages',
      'items' : {$languages|json nofilter},
      'objects_type' : 'language',
      'print_url' : null,
      'required_fileds'   : ['id', 'name', 'locale'],
      'events' : App.standardObjectsListEvents(),
      'multi_url' : '{assemble route=people_mass_edit}',
      'render_item' : function (item) {
        return '<td class="name">' + App.clean(item['name']) + '</td>';
      },
      'search_index' : function (item) {
        return App.clean(item.name) + ' ' + App.clean(item.email);
      }
		});

    //import language form
  	var page_action_import_language = $("#page_title_actions #page_action_import_language a");
  	page_action_import_language.click(function(){
  		var import_language_form = $("#import_language_form");
  		import_language_form.slideToggle('slow',function() {
  			return false;
  		});
  	});
	
    // Handle new language event
    App.Wireframe.Events.bind('language_created.content', function(e, language) {
      wrapper.objectsList('add_item', language);
      App.Wireframe.Flash.success(App.lang(':name language added', {
        'name' : language.name
      }));
    });

    App.Wireframe.Events.bind('language_deleted.content', function(e, language) {
      wrapper.objectsList('delete_item', language);
      App.Wireframe.Flash.success(App.lang(':name  language deleted', {
        'name' : language.name
      }));
    });
    
    // Handle language event
    App.Wireframe.Events.bind('language_updated.content', function(e, language) {
    	wrapper.objectsList('update_item', language);
    	App.Wireframe.Flash.success(App.lang(':name  language updated', {
        'name' : language.name
      }));
    });
  
    // handle on importing vCard (upload/review)
    var upload_to_flyout_wrapper = $('div.import_language.upload_to_flyout');
  	var form = upload_to_flyout_wrapper.find('.import_language_form');
  	
    $('#import_language').click(function() {
    	upload_to_flyout_wrapper.slideToggle('fast');
    });
    
    form.find('button').click(function() {
      var file_input = form.find('input[type=file]:first');

      if (!file_input.val()) {
        App.Wireframe.Flash.error('Please choose XML file which contains language translation');
        return false;
      } // if

      form.ajaxSubmit({
        'url' : App.extendUrl(form.attr('action'), {
          'async' : 1
        }),
        'type' : 'post',
        'success' : function(response) {

        	// hide/reset import langiage form
        	upload_to_flyout_wrapper.slideToggle('fast');
        	file_input.val('');
        	
          var dialog = App.widgets.FlyoutDialog.show({
		        'title' : App.lang('Importing Language file'),
		        'data' : response,
		        'min_height' : 'auto',
		        'width' : 520
					});
        },
        'error' : function(response) {
          App.Wireframe.Flash.error(App.lang('An error occurred while trying to import language'));
        }
      });
      
      return false;
    }); //importing language

    // Pre select item if this is permalink
    {if $active_language->isLoaded()}
    	wrapper.objectsList('load_item', {$active_language->getId()|json nofilter}, {$active_language->getViewUrl()|json nofilter});
    {/if}
	});
</script>
