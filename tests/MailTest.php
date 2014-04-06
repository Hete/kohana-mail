<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for the Mail package.
 * 
 * @package   Mail
 * @category  Tests
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 * @license   BSD 3 clauses
 */
class MailTest extends Unittest_TestCase {
    
    /**
     * Set a custom email to receive the test results.
     */
    const RECEIVER = 'guillaumepoiriermorency@gmail.com';

    public function emails() {
        return array(
            array(MailTest::RECEIVER),
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
                    'To' => 'Foo <foo@example.com>',
                )),
        );
    }

    /**
     * Combine emails, subjects, bodies and headers.
     * 
     * @uses Kohana::cache for lightning speed!
     * 
     * @return array
     */
    public function emails_subjects_bodies_headers() {

        if ($cached = Kohana::cache(__CLASS__ . __METHOD__)) {
            return $cached;
        }

        $cached = array();

        foreach ($this->emails() as $email) {
            foreach ($this->subjects() as $subject) {
                foreach ($this->bodies() as $body) {
                    foreach ($this->headers() as $headers) {
                        $cached[] = array($email[0], $subject[0], $body[0], $headers[0]);
                    }
                }
            }
        }

        Kohana::cache(__CLASS__ . __METHOD__, $cached);

        return $cached;
    }

    /**
     * @dataProvider emails
     */
    public function test_send($email) {

        $this->assertTrue(Mailer::factory()
                        ->subject('test')
                        ->body('test')
                        ->send($email));
    }

    /**
     * @dataProvider subjects
     */
    public function test_subject($subject) {

        $this->assertTrue(Mailer::factory()
                        ->subject($subject)
                        ->body('test')
                        ->send(MailTest::RECEIVER));
    }

    /**
     * @dataProvider headers
     */
    public function test_headers(array $headers) {

        $this->assertTrue(Mailer::factory()
                        ->subject('test')
                        ->body('test')
                        ->headers($headers)
                        ->send(MailTest::RECEIVER));
    }

    public function test_attachment() {

        $this->assertTrue(Mailer::factory()
                        ->subject('Sent you some files!')
                        ->body('Hey!')
                        ->attachment('<html><body>Hey!</body></html>', array('Content-Type' => 'text/html'))
                        ->attachment('smdkn3ihriweojrwefr', array('Content-Type' => 'image/png'))
                        ->send(MailTest::RECEIVER));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_Mail($email, $subject, $body, array $headers) {

        $this->assertTrue(Mail_Sender::factory('Mail', array())
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_PEAR_Mail($email, $subject, $body, $headers) {

        $this->assertTrue(Mail_Sender::factory('PEAR_Mail', array())
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_PEAR_SMTP($email, $subject, $body, $headers) {

        $this->assertTrue(Mail_Sender::factory('PEAR_SMTP', array())
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function test_Sender_PEAR_Sendmail($email, $subject, $body, $headers) {

        $this->assertTrue(Mail_Sender::factory('PEAR_Sendmail', array())
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

}
