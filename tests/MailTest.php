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

    public function emails() {
        return array(
            array('foo@example.com'),
            array(array('foo@example.com', 'bar@example.com')),
            array(array('foo@example.com' => 'Foo', 'bar@example.com' => 'Bar')),
            array(array('foo@example.com', 'bar@example.com' => 'Bar'))
        );   
    }

    public function subjects() {
        return array(
            array('Hello Foo'),
            array('¤ Hello Foo ¤'), // non-ascii
            array(''), // empty
            array('Hello :name <:email>'), // with substitution
            );
        }

    public function bodies() {
        return array(
            array(View::factory('mail/test')), // View
            array('<html><head></head><body></body></html>'), // html
            array("Hello Foo, it's about your delightful ideas."),
            array("Hello Foo\nHow are you?")
        );
    }


    public function headers() {
        return array(
            array(array(
                'From' => 'Bar <bar@example.com>',
                'To'   => 'Foo <foo@example.com>',
            )),
        );    
    }

    public function emails_subjects_bodies_headers() {
        $emails_subjects_bodies_headers = array();

        foreach ($this->emails() as $email) {
            foreach ($this->subjects() as $subject) {
                foreach ($this->bodies() as $body) {
                    foreach ($this->headers() as $headers) {
                        $subjects_bodies_headers[] = array($subject, $body, $headers);
                    }
                }
            }
        }
        return $subjects_bodies_headers;
    }

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

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_Mail($email, $subject, $body, $headers) {
        $this->assertTrue(Mail_Sender::factory('Mail')->send($email, $subject, $body, $headers));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_IMAP($email, $subject, $body, $headers) {
        $this->assertTrue(Mail_Sender::factory('IMAP')->send($email, $subject, $body, $headers));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_PEAR_Mail($email, $subject, $body, $headers) {
        $this->assertTrue(Mail_Sender::factory('PEAR_Mail')->send($email, $subject, $body, $headers));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_PEAR_SMTP($email, $subject, $body, $headers) {
        $this->assertTrue(Mail_Sender::factory('PEAR_SMTP')->send($email, $subject, $body, $headers));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_PEAR_Sendmail($email, $subject, $body, $headers) {
        $this->assertTrue(Mail_Sender::factory('PEAR_Sendmail')->send($email, $subject, $body, $headers));
    }

    /**
     * @dataProvider bodies
     */
    public function test_Styler_Plain($body) {
        $styler = Mail_Styler::factory('Plain');
        $this->assertEquals($styler->style($body), $body);
    }

    /**
     * @dataProvider bodies
     */
    public function test_Styler_Auto($body) {
        $styler = Mail_Styler::factory('Auto');
        $styler->style($body);
    }

    /**
     * @dataProvider
     */
    public function test_Styler_HTML($body) {

        $this->assertFileExists(Kohana::$config->load('mail.styler.HTML.css_file'));

        $styler = Mail_Styler::factory('HTML');
        $this->assertDifferent($styler->style($body), $body);
    }
    
}
