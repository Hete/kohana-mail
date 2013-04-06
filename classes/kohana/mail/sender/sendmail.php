<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Mail
 * @todo ajouter la configuration interne pour ce sender.
 */
class Kohana_Mail_Sender_Sendmail extends Mail_Sender {

    public function _send(Model_Mail $mail) {

        $receiver = static::encode($mail->receiver->receiver_name()) . " <" . $mail->receiver->receiver_email() . ">";

        return (bool) mail($receiver, static::encode($mail->subject()), $mail->render(), $mail->headers());
    }

}

?>
