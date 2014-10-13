{$control_tower->render() nofilter}

<script type="text/javascript">
  App.Wireframe.Statusbar.setItemBadge('statusbar_item_control_tower', {$control_tower->loadBadgeValue()|json nofilter});

  $('#control_tower').each(function() {
    var wrapper = $(this);
    var original_image = false;

    wrapper.find('td.submit a').each(function() {
      var anchor = $(this);
      var image = anchor.find('img:first');

      if (original_image === false) {
        original_image = image.attr('src');
      } // if

      anchor.click(function () {
        image.attr('src', App.Wireframe.Utils.indicatorUrl());
        $.ajax({
          'url' : anchor.attr('href'),
          'type' : 'post',
          'data' : { 'submitted' : 'submitted' },
          success : function () {
            App.Wireframe.Flash.success(anchor.attr('success_message'));
            image.attr('src', original_image);
          },
          error : function () {
            App.Wireframe.Flash.error(anchor.attr('error_message'));
            image.attr('src', original_image);
          }
        });

        return false;
      });
    });
  });
</script>