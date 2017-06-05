<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function send_sms($to, $sms) {
    require_once(APPPATH . "third_party/twilio-php/Services/Twilio.php");

    $account_sid = 'AC42990bcdc1b020f6dfb732ce80b6f33a';
    $auth_token = '8f9c31265a680d25a70bb37849e82880';
    $client = new Services_Twilio($account_sid, $auth_token);

    $client->account->messages->create(array(
        'To' => $to,
        'From' => "+12027594212",
        'Body' => $sms,
    ));
}
