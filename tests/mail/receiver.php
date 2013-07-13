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
class Mail_Receiver_Test extends Unittest_TestCase {

    public function test_model_mail_receiver() {

        $receiver = Model::factory("Mail_Receiver");

        $receiver->name = "Foo Bar";
        $receiver->email = "foo@foo.com";
    }

}

?>
