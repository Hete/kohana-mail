<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for the Mail package.
 * 
 * @package Mail
 * @category Tests
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Mail_Test extends Unittest_TestCase {

    public function test_mail() {

        $receiver = Model::factory("Mail_Receiver");
        $receiver->name = "Foo Bar";
        $receiver->email = "foo@bar.com";

        $mail = new Model_Mail($receiver, "This is just a test", View::factory("mail/test"));

        // Asserting mail content
        $this->assertEquals($mail->subject(), "This is just a test");

        $this->assertTrue(Valid::email($receiver->email));
    }

    public function test_email() {

        $this->assertTrue(Mail_Sender::factory()->send('foo@bar.com', 'hey', 'mail/test'));

        $this->assertTrue(Mail_Sender::factory()->send(array('foo@bar.com'), 'hey', 'mail/test'));

        $this->assertTrue(Mail_Sender::factory()->send(array('foo@bar.com' => 'Foo Bar'), 'hey', 'mail/test'));

        $this->assertTrue(Mail_Sender::factory()->send(array('Foo Bar' => 'foo@bar.com'), 'hey', 'mail/test'));
    }

}

?>
