<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Driver for sendmail built-in php function.
 * 
 * @package Mail
 * @category Senders
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender_Sendmail extends Mail_Sender {

    public function _send(Model_Mail $mail) {

        $receiver = static::encode($mail->receiver()->receiver_name()) . " <" . $mail->receiver()->receiver_email() . ">";

        return (bool) mail($receiver, static::encode($mail->subject()), $mail->render(), $mail->headers());
    }

}

?>
