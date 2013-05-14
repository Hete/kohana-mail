<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Description of queue
 *
 * @package Mail
 * @category Tests
 * @author guillaume
 */
class Mail_Queue_Test extends Unittest_TestCase {

    public function test_iterator() {

        $mails = array();

        $receiver = Model::factory('Mail_Receiver');

        $mails[] = new Model_Mail($receiver, 'subject', 'mail/test');
        $mails[] = new Model_Mail($receiver, 'subject', 'mail/test');
        $mails[] = new Model_Mail($receiver, 'subject', 'mail/test');
        $mails[] = new Model_Mail($receiver, 'subject', 'mail/test');

        $queue = Mail_Queue::factory();

        foreach ($mails as $mail) {
            $queue->push($mail);
        }

        foreach ($queue as $count => $mail) {
            $this->assertEquals($mails[$count], $mail);
        }
    }

}

?>
