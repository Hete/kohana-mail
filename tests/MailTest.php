<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for the Mail package.
 * 
 * @package   Mail
 * @category  Tests
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 * @license   BSD-3-Clauses
 */
class MailTest extends Unittest_TestCase {

    /**
     * Set a custom email to receive the test results.
     */
    const RECEIVER = 'foo@example.com';

    public function emails() {

        return array(
            array(MailTest::RECEIVER),
            array('¤ Foo ¤ <foo@example.com>'), // non-ascii
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
                )),
            array(array(
                    'To' => 'Foo <foo@example.com>, bar@example.com', // recipient list
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
    public function testSend($email) {

        $this->assertTrue(Mailer::factory()
                        ->subject('test')
                        ->body('test')
                        ->send($email));
    }

    /**
     * @dataProvider subjects
     */
    public function testSubject($subject) {

        $this->assertTrue(Mailer::factory()
                        ->subject($subject)
                        ->body('test')
                        ->send(MailTest::RECEIVER));
    }

    /**
     * @dataProvider headers
     */
    public function testHeaders(array $headers) {

        $this->assertTrue(Mailer::factory()
                        ->subject('test')
                        ->body('test')
                        ->headers($headers)
                        ->send(MailTest::RECEIVER));
    }

    public function testParam() {

        $this->assertTrue(Mailer::factory()
                        ->subject('Mail sent by :name')
                        ->body('Hi, it\'s :name, how are you?')
                        ->param(':name', 'Foo')
                        ->send(MailTest::RECEIVER));
    }

    public function testAttachment() {

        $this->assertTrue(Mailer::factory()
                        ->subject('Sent you some files!')
                        ->body('Hey!')
                        ->attachment('{}', array('Content-Type' => 'application/json'))
                        ->attachment(file_get_contents(MODPATH . 'mail/tests/test.png'), array('Content-Type' => 'image/png'))
                        ->send(MailTest::RECEIVER));
    }

    public function testMessageIDGenerator() {

        $this->assertRegExp('/<\w+\.\w+\=@[\w\+=]+>/', Mailer::message_id());
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function testSenderMock($email, $subject, $body, array $headers) {

        $this->assertTrue(Mail_Sender::factory('Mock')
                        ->from('Mock')
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function testSenderMail($email, $subject, $body, array $headers) {

        if (!$this->hasInternet()) {
        
            $this->markTestSkipped();    
        }

        $this->assertTrue(Mail_Sender::factory('Mail')
                        ->from('Mail')
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }


    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function testSenderPEARMail($email, $subject, $body, $headers) {

        if (!$this->hasInternet()) {
        
            $this->markTestSkipped();    
        }

        $this->assertTrue(Mail_Sender::factory('PEAR_Mail')
                        ->from('PEAR Mail')
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function testSenderPEARSMTP($email, $subject, $body, $headers) {

        if (!$this->hasInternet()) {
        
            $this->markTestSkipped();    
        }

        $this->assertTrue(Mail_Sender::factory('PEAR_SMTP')
                        ->from('PEAR SMTP')
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

    /**
     * @dataProvider emails_subjects_bodies_headers
     */
    public function testSenderPEARSendmail($email, $subject, $body, $headers) {

        if (!$this->hasInternet()) {
        
            $this->markTestSkipped();    
        }

        $this->assertTrue(Mail_Sender::factory('PEAR_Sendmail')
                        ->from('PEAR Sendmail')
                        ->subject($subject)
                        ->body($body)
                        ->headers($headers)
                        ->send($email));
    }

}
