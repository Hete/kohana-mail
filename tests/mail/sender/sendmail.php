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
class Mail_Sender_Sendmail_Test extends Unittest_TestCase {

    public function test_sender_sendmail() {
        $model = Model::factory("Mail_Receiver");

        $model->name = "Bertrand";
        $model->email = "foo@bar.com";

        $this->assertTrue(Mail_Sender::factory("Sendmail")->send($model, "Subject", "mail/test"));
    }

}
