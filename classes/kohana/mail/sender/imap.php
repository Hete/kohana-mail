<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Use integrated imap_mail function.
 * 
 * @package Mail
 * @category Senders
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender_IMAP extends Mail_Sender {

    public function _send(Model_Mail $mail) {

        $receiver = static::encode($mail->receiver()->receiver_name()) . " <" . $mail->receiver()->receiver_email() . ">";

        return (bool) imap_mail($receiver, $mail->subject(), $mail->render(), $mail->headers());
    }

}

?>
