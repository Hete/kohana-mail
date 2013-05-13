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
        return (bool) imap_mail($mail->to(), $mail->subject(), $mail->render(), $mail->headers());
    }

}

?>
