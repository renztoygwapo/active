{if !$refresh}
	{title}Trash{/title}
	{assign var=wrapper_id value=HTML::uniqueId('trash')}
	<div id="{$wrapper_id}" class="trashed_items">
	  <form action="{assemble route=trash}">
		  <div class="fields_container context_popup_scrollable">
{/if}

    {if $trash_sections}
		  {foreach $trash_sections as $trash_section_name => $trash_section}
		  <div class="trash_section" trash_section="{$trash_section_name}">
		    <table class="common" cellspacing="0">
		      <thead>
			      <tr>
		          <th class="checkbox"><input type="checkbox" name="selected[]" value="{$trash_section_name}_master" class="master_checkbox" /></th>
			        <th>{$trash_section.label}</th>
			        <th class="count"><span class="count">{$trash_section.count}</span></th>
			      </tr>
		      </thead>
		      <tbody>
			      {foreach $trash_section.items as $trash_section_item}
			      <tr>
		          <td class="checkbox"><input type="checkbox" name="selected[]" value="{$trash_section_name}_{$trash_section_item.id}" class="slave_checkbox" object_type="{$trash_section_item.type}" object_id="{$trash_section_item.id}" /></td>
			        <td>
                {if isset($trash_section_item.permalink) && $trash_section_item.permalink}
                  <a href="{$trash_section_item.permalink}">{$trash_section_item.name|excerpt:60}</a>
                {else}
                	{$trash_section_item.name|excerpt:60}
                {/if}
              </td>
			        <td class="options">
		            <a href="{$restore_url|replace:'--OBJECT-ID--':$trash_section_item.id|replace:'--OBJECT-TYPE--':$trash_section_item.type}" title="{lang}Restore from Trash{/lang}" class="restore_from_trash {if isset($trash_section_item.can_be_parent) && $trash_section_item.can_be_parent}requires_update{/if}"><img src="{image_url name='icons/16x16/restore-from-trash-mono.png' module='environment'}" /></a>
		            <a href="{$delete_url|replace:'--OBJECT-ID--':$trash_section_item.id|replace:'--OBJECT-TYPE--':$trash_section_item.type}" title="{lang}Delete Permanently{/lang}" class="delete_permanently"><img src="{image_url name='icons/16x16/delete-mono.png' module='environment'}" /></a>
		          </td>
			      </tr>
			      {/foreach}
		      </tbody>
		    </table>
		  </div>
		  {/foreach}
    {/if}
    
{if !$refresh}
	  </div>
    
	  <div class="button_holder">
      <a href="{assemble route='trash_empty'}" class="empty_trash_link"><img src="{image_url name='icons/16x16/empty-trash.png' module='environment'}" />{lang}Empty Trash{/lang}</a>
	    <select name="action">
	      <option value="">-- {lang}With selected{/lang} --</option>
	      <option value="restore">{lang}Restore From Trash{/lang}</option>
	      <option value="delete">{lang}Permanently Delete{/lang}</option>
	    </select>
      {submit}Submit{/submit}
	  </div>
  </form>
  
  <p class="empty_page">{lang}Trash is empty{/lang}</p>
</div>

<script type="text/javascript">
  var wrapper = $('#{$wrapper_id}');
  var empty_page = wrapper.find('p.empty_page');
  var form = wrapper.find('form:first');
  var form_fields_container = form.find('.fields_container:first');
  var refresh_url = '{assemble route=trash}';

  var context_popup_trigger = wrapper.parents('#context_popup:first').data('trigger');

  /**
   * Refresh popup position
   */
  var refresh_popup = function () {
    if (context_popup_trigger && context_popup_trigger.length) {
      context_popup_trigger.contextPopup('reposition');
    } // if   
  }; 

  // variables
  var processing = false;
  var refresh = false;
  var selection = new Array();

  /**
   * Initialize
   */
  var initialize = function () {
    check_if_empty();
    hide_empty_groups();
    wrapper.find('table').checkboxes();
  }; // initialize

  /**
   * Check if page is empty
   */
  var check_if_empty = function () {
    if (!wrapper.find('input[type="checkbox"].slave_checkbox').length) {
      form.hide();
      empty_page.show();
    } else {
      form.show();
      empty_page.hide();      
    } // if
    
    refresh_popup();
  }; // check_if_empty

  /**
   * Hides empty groups
   */
  var hide_empty_groups = function () {
    wrapper.find('div.trash_section').each(function () {
      var trash_section = $(this);
      if (!trash_section.find('input.slave_checkbox').length) {
        trash_section.remove();
      } // if
    });
  }; // hide_empty_groups

  /**
   * Put blinds on popup
   */
  var start_processing = function () {
    if (processing) {
      return false;
    } // if

    processing = $('<div class="trash_processing"></div>').insertAfter(form).css('height', form.height());
    form.hide();

    refresh_popup();
  }; // start_processing

  /**
   * Remove blinds from popup
   */
  var stop_processing = function () {
    if (processing && processing.length) {
      processing.remove();
      form.show();
    } // if
    processing = false;
    refresh_popup();
  }; // stop_processing

  /**
   * update list of items in trash
   */
  var update_trash_list = function (callback) {
    // if there's already some refresh request cancel it
    if (refresh) {
      refresh.abort();
    } // if
    
    // save selection so it can be restored
    save_selection();

    // perform ajax request to update list
    refresh = $.ajax({
      'url' : App.extendUrl(refresh_url, { 'refresh' : true }),
      'type' : 'get',
      'success' : function (response) {
        // put indicator in right state
        refresh = false;
        // append response
        form_fields_container.empty().append(response);
        // resotre selection prior update
        restore_selection();
        // stop processing
        stop_processing();
        // initialize items
        initialize();

        if (callback && typeof(callback) == 'function') {
          callback();
        } // if
      }
    });
  }; // update_trash_list

  /**
   * Save current checkbox selection
   */
  var save_selection = function () {
    selection = new Array();
    wrapper.find('input[type="checkbox"]:checked').each(function () {
      selection.push($(this).val());
    });
  };

  /**
   * Restore saved selection
   */
  var restore_selection = function () {
    var selector = new Array();

    $.each(selection, function (index, value) {
      selector.push('input[type="checkbox"][value="' + value + '"]');
    });

    // deselect all checkboxes
    wrapper.find('input[type="checkbox"]:checked').attr('checked', false);
    // select one that were selected in saved selection
    wrapper.find(selector.join(',')).attr('checked', true);
  };

  // empty trash behavior
  wrapper.find('a.empty_trash_link').click(function () {
    if (!confirm(App.lang('Are you sure that you want to empty the trash'))) {
      return false;
    };

    var anchor = $(this);
    start_processing();

    $.ajax({
      'url' : anchor.attr('href'),
      'type' : 'post',
      'data' : { 'submitted' : 'submitted' },
      'success' : function (response) {
        update_trash_list();
        App.Wireframe.Flash.success('Trash emptied successfully');
      },
      'error' : function (response) {
        stop_processing();
      }
    });
    
    return false;
  });

  // restore items from trash
  wrapper.delegate('a.restore_from_trash', 'click', function () {
    var anchor = $(this);
    var img = anchor.find('img:first');
    var original_image = img.attr('src');

    start_processing();

    img.attr('src', App.Wireframe.Utils.indicatorUrl());

    $.ajax({
      'url' : anchor.attr('href'),
      'data' : { 'submitted' : 'submitted' },
      'type' : 'post',
      'success' : function (object) {
        App.Wireframe.Flash.success('Item successfully restored from trash');

        update_trash_list();

        // trigger that object has been updated
        if (object && object.event_names && object.event_names.updated) {
          App.Wireframe.Events.trigger(object.event_names.updated, object);
        } // if
      },
      'error' : function (response) {
        stop_processing();
      },
      'complete' : function () {
        img.attr('src', original_image);
      }
    });
    
    return false;
  });

  // delete single item permanently
  wrapper.delegate('a.delete_permanently', 'click', function () {
    if (!confirm(App.lang('Are you sure that you want to permanently remove this item?'))) {
      return false;
    };
    
    var anchor = $(this);
    var img = anchor.find('img:first');
    var original_image = img.attr('src');

    img.attr('src', App.Wireframe.Utils.indicatorUrl());

    $.ajax({
      'url' : anchor.attr('href'),
      'data' : { 'submitted' : 'submitted' },
      'type' : 'post',
      'success' : function (object) {
        App.Wireframe.Flash.success('Item successfully deleted permanently');
        // remove row of current item
        anchor.parents('tr:first').remove();

        check_if_empty();
        hide_empty_groups();

        // trigger that object has been updated
        if (object && object.event_names && object.event_names.deleted) {
          App.Wireframe.Events.trigger(object.event_names.deleted, object);
        } // if
      },
      'complete' : function () {
        img.attr('src', original_image);
      }
    });

    return false;
  });

  // perform mass action
  form.submit(function () {
    var data = new Array();

    // retrieve data
    form.find('td input[type="checkbox"]:checked').each(function () {
      var checkbox = $(this);
      data.push({
        'id'    : checkbox.attr('object_id'),
        'type'  : checkbox.attr('object_type')
      });
    });

    // validate that we have items selected
    if (!data.length) {
      App.Wireframe.Flash.error('Choose at least one item');
      return false;
    } // if

    // validate action
    var action = form.find('select:first').val();
    if (!action) {
      App.Wireframe.Flash.error('Choose action first');
      return false;
    } // if

    if (action == 'delete') {
      if (!confirm(App.lang('Are you sure that you want to permanently remove selected items?'))) {
        return false;
      };
    } // if

    start_processing();

    // perform mass update
    $.ajax({
      'url' : form.attr('action'),
      'type' : 'post',
      'data' : {
        'items' : data,
        'action' : action,
        'submitted' : 'submitted'
      },
      'success'   : function (response) {
        // trigger events for updated objects
        if (response && typeof(response) == 'object' && response.length) {
          var event_name = (action == 'delete' ? 'deleted' : 'updated');
          $.each(response, function (index, object) {
            if (object && object.event_names && object.event_names[event_name]) {
              App.Wireframe.Events.trigger(object.event_names[event_name], object);
            } // if
          });
        } // if

        // update list
        update_trash_list(function () {
          if (action == 'delete') {
            App.Wireframe.Flash.success(':num items successfully permanently deleted', { 'num' : response.length });
          } else {
            App.Wireframe.Flash.success(':num items successfully restored from trash', { 'num' : response.length });
          } // if
        });
      },
      'error'     : function (response) {
        stop_processing();
      }      
    });

    return false;
  });

  initialize();
</script>
{/if}