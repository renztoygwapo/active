{title}View Payment{/title}
{add_bread_crumb}View Payment{/add_bread_crumb}
{use_widget name=properties_list module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="payment" class="properties_list">
  <div class="head">
    <div class="properties">
    	
    	{if $active_payment->getTransactionId()}
        <div class="property">
          <div class="label">{lang}Transaction id{/lang}</div>
          <div class="data">{$active_payment->getTransactionId()}</div>
        </div>
      {/if}
      <div class="property">
        <div class="label">{lang}Paid for{/lang}</div>
        <div class="data">{$active_payment->getParent()->getName()}</div>
      </div>
      <div class="property">
        <div class="label">{lang}Amount{/lang}</div>
        <div class="data">{$active_payment->getAmount()|money:$active_payment->getCurrency()}</div>
      </div>
      {if $active_payment->getTaxAmount()}
        <div class="property">
          <div class="label">{lang}Transaction Tax{/lang}</div>
          <div class="data">{$active_payment->getTaxAmount()|money:$active_payment->getCurrency()}</div>
        </div>
      {/if}
      <div class="property">
        <div class="label">{lang}Currency{/lang}</div>
        <div class="data">{$active_payment->getCurrency()->getCode()}</div>
      </div>
      {if $active_payment->getPayerId()}
        <div class="property">
          <div class="label">{lang}Payer id{/lang}</div>
          <div class="data">{$active_payment->getPayerId()}</div>
        </div>
      {/if}
      <div class="property">
        <div class="label">{lang}Status{/lang}</div>
        <div class="data">{$active_payment->getStatus()}</div>
      </div>
      {if $active_payment->getReason()}
        <div class="property">
          <div class="label">{lang}Reason{/lang}</div>
          <div class="data">{$active_payment->getReason()}</div>
        </div>
         <div class="property">
          <div class="label">{lang}Reason text{/lang}</div>
          <div class="data">{$active_payment->getReasonText()}</div>
        </div>
      {/if}

      <div class="property">
        <div class="label">{lang}Paid on{/lang}</div>
        <div class="data">{$active_payment->getPaidOn()|date:0}</div>
      </div>

      {if $active_payment->getMethod()}
      	<div class="property">
        	<div class="label">{lang}Method{/lang}</div>
        	<div class="data">{$active_payment->getMethod()}</div>
      	</div>
     	{/if}
      <div class="property">
        <div class="label">{lang}Service{/lang}</div>
        <div class="data">{$active_payment->getGateway()->getGatewayName()}</div>
      </div>
    </div>
  </div>
  
  <div class="body">{$active_payment->getComment()}</div>
</div>