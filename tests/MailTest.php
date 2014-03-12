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
            );
        }

    public function bodies() {
        return array(
            array('<html><head></head><body></body></html>'), // html
            array("Hello Foo, it's about your delightful ideas."),
            array("Hello Foo\nHow are you?") // end-of-line
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

        if ($cached = Kohana::cache(__METHOD__)) {
            return $cached;    
        }

        $cached = array();

        foreach ($this->emails() as $email) {
            foreach ($this->subjects() as $subject) {
                foreach ($this->bodies() as $body) {
                    foreach ($this->headers() as $headers) {
                        $cached = array($email, $subject, $body, $headers);
                    }
                }
            }
        }

        Kohana::cache(__METHOD__, $cached);

        return $cached;
    }

    public function test_send() {

        $this->assertTrue(Mail_Sender::factory()
            ->subject('test')
            ->body('test')
            ->send('foo@example.com'));

        // list of email
        $this->assertTrue(Mail_Sender::factory()
            ->subject('test')
            ->body('test')
            ->send(array(
                'foo@example.com'
            )));

        // assoc of email
        $this->assertTrue(Mail_Sender::factory()
            ->subject('test')
            ->body('test')
            ->send(array(
                'foo@example.com' => 'Foo'
            )));

        // mixed emails
        $this->assertEquals(Mail_Sender::factory()
            ->subject('test')
            ->body('test')
            ->send(array(
                'foo@example.com' => 'Foo',
                'bar@example.com'
            )));

        // one-by-one
        $this->assertEquals(Mail_Sender::factory()
            ->subject('test')
            ->body('test')
            ->send(array(
                'foo@example.com' => 'Foo',
                'bar@example.com'
            ), TRUE), array(TRUE, TRUE));

        // omit the subject
        $this->assertTrue(Mail_Sender::factory()
            ->body('test')
            ->send('foo@example.com'));
    }

    /**
     * @expectedException Exception
     */
    public function test_missing_body() {
        Mail_Sender::factory()
            ->send('foo@example.com');
    }

    public function test_attachment() {
        
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
        Mail_Styler::factory('Auto')
            ->style($body);
    }

    /**
     * @dataProvider bodies
     */
    public function test_Styler_HTML($body) {

        $this->assertFileExists(Kohana::$config->load('mail.styler.HTML.css_file'));

        $this->assertNotEqual(Mail_Styler::factory('HTML')->style($body), $body);
    }
}
