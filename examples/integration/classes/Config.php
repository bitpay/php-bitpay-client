<?php

class Configuration { 
   private $apiToken;
   private $network;

   function __construct( $apiToken, $network = null) {
    $this->apiToken = $apiToken;
    if($network == 'dev' || $network == null):
        $this->network = $this->getApiHostDev();
    else:
        $this->network = $this->getApiHostProd();
    endif;
}

function getAPIToken() {
    return $this->apiToken;
}

function getNetwork() {
    return $this->network;
}

public function getApiHostDev()
{
    return 'test.bitpay.com';
}

public function getApiHostProd()
{
    return 'bitpay.com';
}

public function getApiPort()
{
    return 443;
}

public function getInvoiceURL(){
    return $this->network.'/invoices';
}


} 
?>