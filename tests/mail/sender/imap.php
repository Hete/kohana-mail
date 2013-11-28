<?php

/**
 * Description of imap
 *
 * @package Mail
 * @category Tests
 * @author guillaume
 */
class Mail_Sender_IMAP_Test extends Unittest_TestCase {
    
    public function test_simple_send() {
        $model = Model::factory("Mail_Receiver");

        $model->name = "Bertrand";
        $model->email = "foo@bar.com";

        $this->assertTrue(Mail_Sender::factory("IMAP")->send($model, "Subject", "mail/test"));
    }
    
}
