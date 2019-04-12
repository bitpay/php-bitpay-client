<?php

class Item { 
   #private $config;
   #private $item_params;

   function __construct($config,$item_params) {
      $this->token = $config->getAPIToken();
      $this->endpoint = $config->getNetwork();
      $this->item_params = $item_params;
      return $this->getItem();
}


function getItem(){
   $this->item_params->invoice_endpoint = $this->endpoint.'/invoices';
   $this->item_params->buyer_transaction_endpoint = $this->endpoint.'/invoiceData/setBuyerSelectedTransactionCurrency';
   $this->item_params->buyers_emaill_endpoint = $this->endpoint.'/invoiceData/setBuyerProvidedEmail';

   $this->item_params->token = $this->token;
   return ($this->item_params);
}

}

?>