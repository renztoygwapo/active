{wrap field=client_name}
  {text_field name="api_client_subscription[client_name]" value=$api_client_subscription_data.client_name required=true label="Client Name"}
  <p class="aid">{lang}Example: activeCollab Timer{/lang}</p>
{/wrap}

{wrap field=client_vendor}
  {text_field name="api_client_subscription[client_vendor]" value=$api_client_subscription_data.client_vendor label="Client Vendor"}
{/wrap}

{wrap field=read_only}
  {yes_no name="api_client_subscription[is_read_only]" value=$api_client_subscription_data.is_read_only label="Read Only"}
{/wrap}