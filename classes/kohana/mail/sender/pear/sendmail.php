<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
class Kohana_Mail_Sender_PEAR_Sendmail extends Mail_Sender {

    protected function _send(Model_Mail $mail) {
        
        $config = Kohana::$config->load('mail.sender.pear.sendmail');
        
        return (bool) Mail::factory('sendmail', $config)->send($mail->to(), $mail->headers(), (string) $mail);
    }

}

?>
