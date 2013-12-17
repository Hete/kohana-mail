<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * Default headers
     */
    'headers' => array(
        'Content-Encoding' => 'utf-8',
        'MIME-Version' => '1.0'
    ),
    /**
     * Sender configuration
     */
    'sender' => array(
        'Mail' => array(), // $additional_parameters for mail()
        'IMAP' => array(
            'rpath' => NULL // return path
        ),
        /**
         * @link http://pear.php.net/manual/en/package.mail.mail.factory.php
         */
        'PEAR' => array(
            'Sendmail' => array(
                'sendmail_path' => '/usr/bin/sendmail',
                'sendmail_args' => '-i'
            ),
            'SMTP' => array(
                'host' => 'localhost',
                'port' => 25,
                'auth' => FALSE,
                'username' => NULL,
                'password' => NULL,
                'localhost' => 'localhost',
                'timeout' => NULL,
                'verp' => FALSE,
                'debug' => FALSE,
                'persist' => NULL,
                'pipelining' => NULL
            ),
            'Mail' => array()
        )
    ),
    'styler' => array(
        'HTML' => array(
            'css_file' => MODPATH . 'mail/bootstrap-mail.min.css' 
        ),
        'Auto' => array(
            'paragraph' => TRUE, // Text::auto_p
            'link' => TRUE // Text::auto_link
        )
    )
);
