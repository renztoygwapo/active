{title}Network Settings{/title}
{add_bread_crumb}Network Settings{/add_bread_crumb}

<div id="date_time_settings">
  {form action=Router::assemble('network_settings') method=post}
    <div class="content_stack_wrapper">

      <!-- Proxy -->
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Proxy Settings{/lang}</h3>
        </div>
        <div class="content_stack_element_body">

          {wrap field=network_proxy_enabled id="networkProxyToggler"}
            {yes_no name='network[network_proxy_enabled]' value=$network_data.network_proxy_enabled label='Proxy Enabled' id="networkProxyToggler"}
          {/wrap}

          <div class="network_proxy_address_wrapper">
            <div class="network_proxy_protocol">
              {wrap field=network_proxy_protocol}
                {label for=networkProxyProtocol}Proxy Protocol{/label}
                {select_http_protocol name="network[network_proxy_protocol]" value=$network_data.network_proxy_protocol id=networkProxyProtocol}
              {/wrap}
            </div>

            <div class="network_proxy_address">
              {wrap field=network_proxy_address}
                {label for=networkProxyAddress}Proxy Address{/label}
                {text_field name="network[network_proxy_address]" value=$network_data.network_proxy_address id=networkProxyAddress}
              {/wrap}
            </div>

            <div class="network_proxy_port">
              {wrap field=network_proxy_port}
                {label for=networkProxyPort}Proxy Port{/label}
                {text_field name="network[network_proxy_port]" value=$network_data.network_proxy_port id=networkProxyPort}
              {/wrap}
            </div>
          </div>

        </div>
      </div>
    </div>

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<style type="text/css">
  .network_proxy_address_wrapper {
    overflow: hidden;
  }

  .network_proxy_address_wrapper .network_proxy_protocol {
    width: 100px;
    float: left;
  }

  .network_proxy_address_wrapper .network_proxy_protocol select {
    position: relative;
    top: 1px;
  }

  .network_proxy_address_wrapper .network_proxy_address {
    width: 270px;
    float: left;
  }

  .network_proxy_address_wrapper .network_proxy_port {
    width: 90px;
    float: left;
  }

  .network_proxy_address_wrapper .network_proxy_port input {
    width: 70px;
  }
</style>

<script type="text/javascript">
  var proxy_fields_wrapper = $('.network_proxy_address_wrapper');
  var proxy_toggler_wrapper = $('#networkProxyToggler');
  var proxy_toggler_yes = proxy_toggler_wrapper.find('#networkProxyTogglerYesInput');
  var proxy_toggler_no = proxy_toggler_wrapper.find('#networkProxyTogglerNoInput');

  /**
   * Proxy enabled changed
   */
  var proxy_enabled_changed = function () {
    if (proxy_toggler_yes.is(":checked")) {
      proxy_fields_wrapper.find('input, select').removeAttr('disabled');
    } else {
      proxy_fields_wrapper.find('input, select').attr('disabled', 'disabled');
    } // if
  } // proxy_enabled_changed

  proxy_toggler_yes.change(function () {
    proxy_enabled_changed();
  });

  proxy_toggler_no.change(function () {
    proxy_enabled_changed();
  });

  proxy_enabled_changed();
</script>