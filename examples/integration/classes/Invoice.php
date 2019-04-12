<?php

class Invoice { 
   #private $item;

   function __construct($item) {
      $this->item =$item;
     
    
}

function createInvoice(){
   #setup some curl
   $post_fields = json_encode($this->item->item_params);
   #print_r($post_fields);die();
  
   #echo($post_fields->buyerSelectedTransactionCurrency);die();
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, 'https://'.$this->item->item_params->invoice_endpoint);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$post_fields);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $result = curl_exec($ch);

   #if the currency was set for BTC or BCH, do another call to update this invoice id
   if($this->item->item_params->buyerSelectedTransactionCurrency):
      $this->updateBuyerCurrency($result,$this->item->item_params->buyerSelectedTransactionCurrency);
   endif;

   #if there is a buyer provided email, update it
   if($this->item->item_params->buyers_email):
      $this->updateBuyersEmail($result,$this->item->item_params->buyers_email);
   endif;

   $this->invoiceData = $result;

   curl_close ($ch);


}

function getInvoiceData(){
   return $this->invoiceData;
}


function getInvoiceURL(){
   $data = json_decode($this->invoiceData);
   return $data->data->url;
}

function updateBuyersEmail($invoice_result,$buyers_email){
   $invoice_result = json_decode($invoice_result);
  
   $update_fields = new stdClass();
   $update_fields->token = $this->item->item_params->token;
   $update_fields->buyerProvidedEmail = $buyers_email;
   $update_fields->invoiceId = $invoice_result->data->id;
   $update_fields = json_encode($update_fields);
   
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, 'https://'.$this->item->item_params->buyers_email_endpoint);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$update_fields);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $result = curl_exec($ch);
   curl_close ($ch);
   return $result;

}

function updateBuyerCurrency($invoice_result,$buyer_currency){
   $invoice_result = json_decode($invoice_result);
  
   $update_fields = new stdClass();
   $update_fields->token = $this->item->item_params->token;
   $update_fields->buyerSelectedTransactionCurrency = $buyer_currency;
   $update_fields->invoiceId = $invoice_result->data->id;
   $update_fields = json_encode($update_fields);

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, 'https://'.$this->item->item_params->buyer_transaction_endpoint);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$update_fields);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $result = curl_exec($ch);
   curl_close ($ch);
   return $result;

}

}

?>