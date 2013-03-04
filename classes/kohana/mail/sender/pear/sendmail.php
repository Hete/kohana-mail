<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Sender_PEAR_Sendmail extends Mail_Sender {

    public function _send(Model_Mail $mail) {
        return (bool) Mail::factory("sendmail", $this->config())->send($mail->receiver->receiver_email(), $mail->headers, $mail->render());
    }

}

?>
