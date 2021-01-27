<?php
require __DIR__ .'/vendor/autoload.php';

 
use Twilio\Rest\Client; 
 
$account_sid    = "AC9c11d0af67a64d881203a63be2aade7b"; 
$auth_token  = "c06c9f66d7b0212807f0793aeeb255e9"; 

$twilio_number = "+15005550006";


$client = new Client($account_sid, $auth_token);
$end = $client->messages->create(
    // Where to send a text message (your cell phone?)
    '+8618406576618',
    array(
        'from' => '+15005550006',
        'body' => rand(1000,9999)
    )
);
halt($end->body);