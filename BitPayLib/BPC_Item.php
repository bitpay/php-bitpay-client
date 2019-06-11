<?php

class BPC_Item { 
  function __construct($config,$item_params) {
      $this->token = $config->BPC_getAPIToken();
      $this->endpoint = $config->BPC_getNetwork();
      $this->item_params = $item_params;
      return $this->BPC_getItem();
}


function BPC_getItem(){
   $this->invoice_endpoint = $this->endpoint.'/invoices';
   $this->buyer_transaction_endpoint = $this->endpoint.'/invoiceData/setBuyerSelectedTransactionCurrency';
   $this->item_params->token = $this->token;
   return ($this->item_params);
}

}

?>
