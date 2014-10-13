{add_bread_crumb}List Pages{/add_bread_crumb}
{use_widget name="objects_list" module="environment"}

<div id="notebook_pages">
  <div class="empty_content">
	  {object object=$active_notebook user=$logged_user show_body=false class='empty_content'}
	    <div id="notebook{$active_notebook->getId()}" class="object_notebook wireframe_content_wrapper">
	      <img src="{$active_notebook->avatar()->getUrl(INotebookAvatarImplementation::SIZE_PHOTO)}" alt="" class="notebook_cover" />
	      
	      <div class="object_description content formatted_content object_body_content">
					{if ($active_notebook->getBody())}
					  {$active_notebook->getBody()|rich_text nofilter}
					{else}
					  {lang}No description for this Notebook{/lang}
					{/if}
	      </div>
        
        {object_attachments object=$active_notebook user=$logged_user}
			</div>
	  {/object}
  </div>
</div>

{if !$request->isQuickViewCall()}
<script type="text/javascript">
  var notebook_state = {$active_notebook->getState()};
  var project_id = {$active_project->getId()|json nofilter};
  
  // notebook created
  App.Wireframe.Events.bind('notebook_created.content', function (event, notebook) {
    if (notebook['class'] == 'Notebook') {
      if (project_id == notebook['project_id']) {
        App.Wireframe.Content.setFromUrl(notebook['urls']['view']);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(notebook['urls']['view']);
        } // if
      } // if
    } // if

  });

	App.Wireframe.Events.bind('notebook_updated.{$request->getEventScope()}', function (event, notebook) {
	  if (notebook['class'] == 'Notebook' && notebook['id'] == '{$active_notebook->getId()}') {
      if (project_id == notebook['project_id']) {
        var wrapper = $('#notebook' + notebook['id']);
        var logo_image = wrapper.find('img.notebook_cover');
        logo_image.attr('src', notebook['avatar']['photo']);

        // refresh page list
        if (notebook_state < 2 && notebook['state'] >= 2) {
          $('#notebook_pages').objectsList('refresh');
        } // if

        notebook_state = notebook['state'];
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(notebook['urls']['view']);
        } else {
          App.Wireframe.Content.setFromUrl($('#page_tab_notebooks').find('a').attr('href'));
        } // if
      } // if
	  } // if
	});

  App.Wireframe.Events.bind('notebook_deleted.content', function (event, notebook) {
    if (notebook['class'] == 'Notebook' && notebook['id'] == '{$active_notebook->getId()}') {
      if (notebook['state'] < 2) {
        App.Wireframe.Content.setFromUrl('{assemble route="project_notebooks" project_slug=$active_project->getSlug()}');
      } // if

      notebook_state = notebook['state'];
    } // if
  });

	$('#notebook_pages').each(function() {
	  var wrapper = $(this);
    var notebook_id = '{$active_notebook->getId()}';
    var project_id = {$active_project->getId()|json nofilter};
    
    wrapper.objectsList({
      'id' : 'project_' + {$active_project->getId()} + '_notebook_' + {$active_notebook->getId()} + '_pages',
      'items' : {$notebook_pages|json nofilter},
      'objects_type' : 'notebook_pages',
      'events' : App.standardObjectsListEvents(),
      'refresh_url' : '{assemble route=project_notebook project_slug=$active_project->getSlug() notebook_id=$active_notebook->getId() async=true objects_list_refresh=true}',
      'multi_title' : App.lang(':num Pages Selected'),
      'multi_url'   : '{assemble route=project_notebook_mass_edit project_slug=$active_project->getSlug() notebook_id=$active_notebook->getId()}',
      'multi_actions' : {$mass_manager|json nofilter},
      'requirements' : {
        'notebook_id' : '{$active_notebook->getId()}'
      },
      'prepare_item': function (item) {
        var result = {
          'id' : item['id'],
          'name' : item['name'], 
          'is_archived' : item['state'] == 2 ? 1 : 0,
          'revision_num' : item['revision_num'],
          'depth' : item['depth'],
          'permalink' : item['permalink'],
          'is_favorite' : item['is_favorite']
        }; // result

        result['notebook_id'] = item['notebook'] && !$.isEmptyObject(item['notebook']) ? item['notebook']['id'] : item['notebook_id'];
        result['parent_id'] = item['parent'] && !$.isEmptyObject(item['parent']) ? item['parent']['id'] : item['parent_id'];
         
        return result;
      },
      'render_item' : function (item) {
        var rendered = '<td class="name" parent_id="' + item['parent_id'] + '">';

        if (item['depth']) {
          var counter;
          for (counter = 0; counter < item['depth']; counter ++) {
            rendered += '<span class="tree_indent';
            if (counter == 0) {
              rendered += ' first_tree_indent';
            } // if
            if (counter == (item['depth'] - 1)) {
              rendered += ' last_tree_indent';
            } // if
            rendered += '"></span>';
          }
        } // if

        rendered += '<span class="notebook_name" style="margin-left: ' + (counter * 23 + 5) + 'px">' + App.clean(item['name']) + '</span>';
        rendered += '</td><td class="notebook_page_version"><span>v' + item['revision_num'] + '</span></td>';
        
        return rendered;
      },

      'filtering' : [{ 
        'label' : App.lang('Status'), 
        'property'  : 'is_archived', 
        'values'  : [{ 
          'label' : App.lang('Active Pages'), 
          'value' : '0', 
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'), 
          'default' : true 
        }, { 
          'label' : App.lang('All Pages'), 
          'value' : '', 
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active-and-completed.png', 'complete')
        }]
      }],

      'additional_buttons' : {
        'sort_notebook_pages' : {
          'title' : App.lang('Sort Notebook Pages'),
          'icon' : App.Wireframe.Utils.imageUrl('objects_list/edit-mode.png', 'environment', 'widgets'),
          'onclick' : function () {
            var anchor = $('<a href="{assemble route=project_notebook_pages_reorder project_slug=$active_project->getSlug() notebook_id=$active_notebook->getId()}" title="' + App.lang('Reorder Pages') + '">' + App.lang('Reorder Pages') + '</a>').flyoutForm({
              'success' : function () {
                wrapper.objectsList('refresh')  
              }
            }).trigger('click');
          }
        }
      }
    });

    // notebook page added and deleted
    App.Wireframe.Events.bind('notebook_page_created.content notebook_page_deleted.content notebook_page_updated.content', function (event, notebook_page) {
      var notebook_id = notebook_page['notebook'] && !$.isEmptyObject(notebook_page['notebook']) ? notebook_page['notebook']['id'] : notebook_page['notebook_id'];
      if (notebook_id == {$active_notebook->getId()}) {

        // if item is deleted and it's currently displayed show empty page
        if (event.type == 'notebook_page_deleted') {
          if (wrapper.objectsList('is_loaded', notebook_page['id'], false)) {
            wrapper.objectsList('load_empty');
          } // if
        } // if

        wrapper.objectsList('refresh');
      } // if
    });

    {if $active_notebook_page && $active_notebook_page->isLoaded()}
	  	wrapper.objectsList('load_item', {$active_notebook_page->getId()}, '{$active_notebook_page->getViewUrl()}');
	  {/if}
	});
</script>
{/if}