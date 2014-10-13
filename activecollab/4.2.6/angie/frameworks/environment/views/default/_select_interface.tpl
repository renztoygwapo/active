<div class="select_interface" id="{$_interface_id}">
  <ul>
  {foreach from=$_interface_possibilities item=interface key=interface_id}
    <li><a href="#" title="{$interface}" value="{$interface_id}"><img src='{image_url name="icons/interface_$interface_id.png" module="environment"}' alt="{$interface}" /></a></li>
  {/foreach}
  </ul>
  <input type="hidden" name="{$_interface_name}" class='selected_value' />
</div>

<script type="text/javascript">
  var current_interface = {$_interface_current|json nofilter};
  var control = $('#{$_interface_id}');
  var interfaces = control.find('a');
  var field = control.find('input.selected_value');

  interfaces.each(function () {
    var _interface = $(this);
    _interface.click(function () {
      interfaces.removeClass('selected');
      _interface.addClass('selected');
      field.val(_interface.attr('value'));
      return false;
    });  
  });

  // initial select
  control.find('a[value='+current_interface+']').click();
</script>