<?php
/*
Access our latest branding options and integrate as needed.  This endpoint is public and does not require authentication
https://bitpay.com/api
 */


/*
Autoload the classes
*/

function BPC_autoloader($class)
{
    #change the pathing if needed
    if (strpos($class, 'BPC_') !== false):
        if (!class_exists('../BitPayLib/' . $class, false)):
            #doesnt exist so include it
            include '../BitPayLib/' . $class . '.php';
        endif;
    endif;
}
spl_autoload_register('BPC_autoloader');
$buttonObj = new BPC_Buttons;
$buttons = json_decode($buttonObj->BPC_getButtons());
$output = [];
foreach ($buttons->data as $key => $b):

    $names = preg_split('/(?=[A-Z])/', $b->name);
    $names = implode(" ", $names);
    $names = ucwords($names);
    
    $names = str_replace(" Button", "", $names);
    $output['//' . $b->url] = $names;
 
endforeach;
echo (print_r($output,true));
