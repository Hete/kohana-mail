<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Mail
 * @todo ajouter la configuration interne pour ce sender.
 */
class Kohana_Mail_Sender_Native extends Mail_Sender {

    public function _send(Model_Mail $mail) {
        return (bool) mail($mail->receiver->receiver_email(), $mail->subject(), $mail->render(), $mail->headers());
    }

}

?>
