<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Driver for sendmail built-in php function.
 * 
 * @package   Mail
 * @category  Senders
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender_Sendmail extends Mail_Sender {

    protected function _send(Model_Mail $mail) {
        return (bool) mail($mail->to(), $mail->subject(), (string) $mail, $mail->headers_encoded());
    }

}

?>
