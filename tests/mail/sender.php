<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * 
 * @package Mail
 * @category Tests
 */
class Mail_Sender_Test extends Unittest_TestCase {

    public function test_send() {

        Mail_Sender::instance()->send($receivers, $view);
    }

}

?>
