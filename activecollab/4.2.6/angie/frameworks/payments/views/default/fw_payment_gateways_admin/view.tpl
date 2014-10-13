{title}{$payment_gateway->getName()}{/title}
{add_bread_crumb}View{/add_bread_crumb}
{use_widget name=properties_list module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="payment" class="properties_list">
  <div class="head">
    <div class="properties">
    	
      <div class="property">
        <div class="label">{lang}Name{/lang}</div>
        <div class="data">{$payment_gateway->getName()}</div>
      </div>
      <div class="property">
        <div class="label">{lang}Type{/lang}</div>
        <div class="data">{$payment_gateway->getGatewayName()}</div>
      </div>
      {if $payment_gateway->getAPIUsername()}
        <div class="property">
          <div class="label">{lang}API Username{/lang}</div>
          <div class="data">{$payment_gateway->getAPIUsername()}</div>
        </div>
      {/if}
      {if $payment_gateway->getAPISignature()}
        <div class="property">
          <div class="label">{lang}API Signature{/lang}</div>
          <div class="data">{$payment_gateway->getAPISignature()}</div>
        </div>
      {/if}
      {if $payment_gateway->getApiLoginId()}
        <div class="property">
          <div class="label">{lang}API Login Id{/lang}</div>
          <div class="data">{$payment_gateway->getApiLoginId()}</div>
        </div>
      {/if}
      {if $payment_gateway->getTransactionId()}
        <div class="property">
          <div class="label">{lang}Transaction Id{/lang}</div>
          <div class="data">{$payment_gateway->getTransactionId()}</div>
        </div>
      {/if}
      <div class="property">
        <div class="label">{lang}Default Gateway{/lang}</div>
        <div class="data">{if $payment_gateway->getIsDefault() == '0'}{lang}No{/lang}{else}{lang}Yes{/lang}{/if}</div>
      </div>
      <div class="property">
        <div class="label">{lang}Go Live{/lang}</div>
        <div class="data">{if $payment_gateway->getGoLive() == '0'}{lang}No{/lang}{else}{lang}Yes{/lang}{/if}</div>
      </div>
      
    </div>
  </div>
  
  <div class="body">&nbsp;</div>
  
</div>
 