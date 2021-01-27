<?php
require __DIR__ . '../../../../vendor/vendor/autoload.php';
use Twilio\Rest\Client;


// Find your Account Sid and Auth Token at twilio.com/console
// and set the environment variables. See http://twil.io/secure
$sid = 'AC9c11d0af67a64d881203a63be2aade7b';
$token = 'c06c9f66d7b0212807f0793aeeb255e9';
$twilio = new Client($sid, $token);

$incoming_phone_number = $twilio->incomingPhoneNumbers
                                ->create([
                                             "phoneNumber" => "+15005550006",
                                             "voiceUrl" => "http://demo.twilio.com/docs/voice.xml"
                                         ]
                                );

print($incoming_phone_number->sid);