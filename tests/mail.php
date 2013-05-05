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

    public function setUp() {
        parent::setUp();
        $_SERVER['SERVER_NAME'] = "phpunit";
    }

    public function test_mail() {

        $receiver = Model::factory("Mail_Receiver");
        $receiver->name = "Foo Bar";
        $receiver->email = "foo@bar.com";

        $mail = new Model_Mail($receiver, "This is just a test", View::factory("mail/test"));

        // Asserting mail content
        $this->assertEquals($mail->subject(), '=?UTF-8?B?' . base64_encode("This is just a test") . '?=');

        // Change subject
        $mail->subject("tata");
        $this->assertEquals($mail->subject(), '=?UTF-8?B?' . base64_encode("tata") . '?=');
    }

    public function test_sender_sendmail() {
        $model = Model::factory("Mail_Receiver");

        $model->name = "Bertrand";
        $model->email = "foo@bar.com";

        $this->assertTrue(Mail_Sender::factory("Sendmail")->send($model, "mail/test", array("user" => ORM::factory("user"))));

        // Testing resend
        $this->assertTrue(Mail_Sender::factory("Sendmail")->send($model, "mail/test", array("user" => ORM::factory("user"))));
    }

    public function test_sender_imap() {
        $model = Model::factory("Mail_Receiver");

        $model->name = "Bertrand";
        $model->email = "foo@bar.com";

        $this->assertTrue(Mail_Sender::factory("IMAP")->send($model, "mail/test", array("user" => ORM::factory("user"))));

        // Testing resend
        $this->assertTrue(Mail_Sender::factory("IMAP")->send($model, "mail/test", array("user" => ORM::factory("user"))));
    }

    public function test_sender_pear_smtp() {

        $this->markTestIncomplete();

        $model = Model::factory("Mail_Receiver");

        $model->name = "Bertrand";
        $model->email = "bertrand@gmail.com";

        Mail_Sender::factory("PEAR_SMTP")->send($model, "mail/test", array("user" => ORM::factory("user")));
    }

    public function test_sender_pear_sendmail() {

        $this->markTestIncomplete();
    }

    public function test_mail_receiver() {
        
    }

    public function test_model_mail_receiver() {

        $receiver = Model::factory("Mail_Receiver");
        
        $receiver->name = "Foo Bar";
        $receiver->email = "foo@foo.com";
        
        $this->assertTrue($receiver->check());
    
    }

}

?>
