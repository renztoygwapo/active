{use_widget name="ui_sortable" module="environment"}
<script type="text/javascript">

(function () {
  // @todo create function that will get this data effectively
  var notebooks = {$notebooks|json nofilter};
  var project_id = {$active_project->getId()|json nofilter};
  var notebooks_list = $('#notebooks .notebooks_list');
  var notebooks_shelves = $('#notebooks .notebooks_shelves');
  var single_notebook_shelve = $('<div class="notebooks_shelf"><div class="notebooks_shelf_inner"><div class="notebooks_shelf_inner_2"></div></div></div>')
  var in_archive_view = {$in_archive|json nofilter};

  if (in_archive_view) {
    var notebooks_message = $('<p class="empty_page notebooks_empty_page">' + App.lang('There are no notebooks in this archive') + '</p>').insertBefore(notebooks_shelves);
  } else {
    var notebooks_message = $('<p class="empty_page notebooks_empty_page">' + App.lang('This project does not contain any notebooks') + '</p>').insertBefore(notebooks_shelves);
  } // if


  /**
   * Render notebook
   *
   * @param notebook
   * @return object
   */
  var render_notebook = function (notebook) {
    var notebook_jquery = $('<li class="notebook" notebook_id="' + notebook.id + '">' +
      '<span class="drag_handle"></span>' +
      '<a href="'+ notebook.permalink +'" class="notebook_anchor">' +
      '<span class="title_strap_left"></span><span class="title_strap_right"></span>' +
      '<span class="cover">' +
      '<img src="' + notebook.avatar.large + '" alt="" />' +
      '</span>' +
      '<span class="notebook_name"><span class="notebook_name_inner">' +
      App.clean(notebook.name) +
      '</span></span>' +
      '</a>'+
    '</li>');

    notebook_jquery = notebook_jquery.appendTo(notebooks_list);

    notebook_jquery.find('img').load(function () {
      var bottom_spacing = parseInt(notebook_jquery.find('.notebook_name').outerHeight());
      notebook_jquery.find('.title_strap_left').css('bottom', bottom_spacing);
      notebook_jquery.find('.title_strap_right').css('bottom', bottom_spacing);

      var drag_handle = notebook_jquery.find('.drag_handle');
      if (drag_handle.length) {
        drag_handle.css('bottom', Math.round(parseInt(bottom_spacing - drag_handle.height()) / 2)).hide();
        notebook_jquery.hover(function () {
          drag_handle.show();
        }, function(){
          drag_handle.hide();
        });
      } // if
    });

    update_shelves();

    if (notebooks_list.find('li').length) {
      notebooks_message.hide();
    } // if

    return notebook_jquery;
  } // render_notebook

  /**
   * Find notebook with id
   *
   * @param notebook_id
   * @return jQuery
   */
  var find_notebook = function (notebook_id) {
    var notebook = notebooks_list.find('li.notebook[notebook_id="' + notebook_id + '"]');
    return notebook.length > 0 ? notebook : null;
  } // find_notebook

  /**
   * Delete existing notebook
   *
   * @param mixed notebook_id
   */
  var delete_notebook = function (notebook_id) {
    if (!notebook_id) {
      return false;
    } // if

    if (typeof(notebook_id) == 'object') {
      var notebook = notebook_id;
    } else {
      var notebook = find_notebook(notebook_id);
    } // if

    notebook.remove();
    update_shelves();

    if (notebooks_list.find('li').length) {
      notebooks_message.hide();
    } // if
  } // delete_notebook

  // what happens when notebook is created
  App.Wireframe.Content.bind('notebook_created.content', function(event, notebook) {
    if (in_archive_view) {
      return false;
    } // if

    if (notebook.project_id != project_id) {
      return false;
    } // if

    render_notebook(notebook);
  });

  // what happens when notebook is updated
  App.Wireframe.Content.bind('notebook_updated.content', function(event, notebook) {
    var current_notebook = find_notebook(notebook.id);
    var notebook_state = notebook.state;

    if (current_notebook && current_notebook.length) {
      if (notebook.project_id == project_id) {
        // @todo update current notebook
      } else {
        // if project has been changed
        delete_notebook(current_notebook);
      } // if
    } else {
      if (notebook.project_id == project_id) {
        if ((in_archive_view && notebook_state == 2) || (!in_archive_view && notebook_state == 3)) {
          render_notebook(notebook);
        } // if
      } // if
    } // if
  });

  //notebooks reordering
  notebooks_list.sortable({
    items : 'li',
    handle : 'span.drag_handle',
    update : function() {
      var new_order = new Array();

      $(this).find('.notebook').each(function() {
        new_order.push($(this).attr('notebook_id'));
      });

      $.ajax({
        url : App.Config.get('reorder_notebooks_url'),
        type : 'POST',
        data : { 'new_order' : new_order.toString(), 'submitted' : 'submitted' },
        success : function(response) {
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
    notebooks_shelves.empty().append(single_notebook_shelve.clone());

    var number_of_rows = Math.ceil(notebooks_list.height() / notebooks_shelves.height()) - 1;

    if (number_of_rows > 0) {
      for (var counter = 0; counter < number_of_rows; counter ++) {
        notebooks_shelves.append(single_notebook_shelve.clone());
      } // for
    } // if
  }; // update_shelves

  /**
   * Init notebooks page
   */
  var init_notebooks = function () {
    // add existing notebooks to the list
    if (notebooks && notebooks.length) {
      $.each(notebooks, function (index, notebook) {
        render_notebook(notebook);
      });
    } // if

    // hide empty page message if there are notebooks in the list
    if (notebooks_list.find('li').length) {
      notebooks_message.hide();
    } // if

    // initially update shelves
    update_shelves();

    // update shelves when browser resizes
    App.Wireframe.Content.bind('window_resize.content', update_shelves);
  }; // init_notebooks

  init_notebooks();
}())


</script>