<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for the Mail package.
 * 
 * @package Mail
 * @category Tests
 */
class Mail_Test extends Unittest_TestCase {

    public function setUp() {
        parent::setUp();
        $_SERVER['SERVER_NAME'] = "phpunit";
    }

    public function test_sender_native() {
        $model = Model::factory("Mail_Receiver");

        $model->name = "Bertrand";
        $model->email = "bertrand@gmail.com";

        $this->assertTrue(Mail_Sender::factory()->send($model, "mail/inscription", array("user" => ORM::factory("user"))));

        // Testing resend
        $this->assertTrue(Mail_Sender::factory()->send($model, "mail/inscription", array("user" => ORM::factory("user"))));
    }

    public function test_sender_pear_smtp() {

        if (!class_exists("Mail")) {
            $this->markTestSkipped("PEAR Mail is unavailable.");
        }

        $model = Model::factory("Mail_Receiver");

        $model->name = "Bertrand";
        $model->email = "bertrand@gmail.com";

        Mail_Sender::factory("PEAR_SMTP")->send($model, "mail/inscription", array("user" => ORM::factory("user")));
    }

    public function test_sender_pear_sendmail() {

        if (!class_exists("Mail")) {
            $this->markTestSkipped("PEAR Mail is unavailable.");
        }
    }

}

?>
