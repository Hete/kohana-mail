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

    public function test_send() {

        Mail_Sender::factory()->send('foo@example.com', 'i am a foobar!', 'Yes! Here we are :)');

        Mail_Sender::factory()->send(array(
            'foo@example.com' => ''
        ));

        Mail_Sender::factory()->send('foo@example.com');
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

    public function test_Styler_None() {

    }

    public function test_Styler_Auto() {

    }

    public function test_Styler_HTML() {

    }
    
}
