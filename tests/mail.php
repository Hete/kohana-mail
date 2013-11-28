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

    public function test_email() {

        $this->assertTrue(Mail_Sender::factory()->send('foo@bar.com', 'hey', 'mail/test'));

        $this->assertTrue(Mail_Sender::factory()->send(array('foo@bar.com'), 'hey', 'mail/test'));

        $this->assertTrue(Mail_Sender::factory()->send(array('foo@bar.com' => 'Foo Bar'), 'hey', 'mail/test'));

        $this->assertTrue(Mail_Sender::factory()->send(array('Foo Bar' => 'foo@bar.com'), 'hey', 'mail/test'));
    }

    public function test_headers_encode() {

        // Encode ascii string
        $this->assertEquals(Model_Mail::headers_encode("askd9922 ewas"), "askd9922 ewas");

        // Encode non-ascii string
        $this->assertEquals(Model_Mail::headers_encode('aslkd*2378*(*&2ééé'), '=?UTF-8?B?YXNsa2QqMjM3OCooKiYyw6nDqcOp?=');
        
        $this->assertEquals(Model_Mail::headers_encode('Jamès <foo@bar.com>'), Model_Mail::headers_encode('Jamès') . ' <foo@bar.com>');
        
    }
    
    public function test_model_mail() {
        
        $mail = new Model_Mail(Model::factory('Mail_Receiver'), 'Subject', 'LOL!');
        
        $this->assertEmpty($mail->headers());
        
    }

}
