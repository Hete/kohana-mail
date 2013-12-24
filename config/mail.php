<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Configuration for the mail module.
 *
 * @package   Mail
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 * @license   BSD License
 */
return array(
    /**
     * Default headers
     */
    'headers' => array(
        'Content-Encoding' => 'utf-8',
        'MIME-Version' => '1.0'
    ),
    'attachement' => array(
        'encoding'
    ),
    /**
     * Sender configuration
     */
    'sender' => array(
        /**
         * PHP built-in mail() function
         * 
         * Paremeters are imploded with a spaced and passed as $parameters
         * of the mail() function.
         */
        'Mail' => array(), // $additional_parameters for mail()
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
        'Plain' => array(
            'wordwrap' => 70, // wordwrap, FALSE to disable
        ),
        'HTML' => array(
            'css_file' => MODPATH . 'mail/bootstrap-mail.min.css', // FALSE to disable
        ),
        'Auto' => array(
            'paragraph' => TRUE, // Text::auto_p
            'link' => TRUE // Text::auto_link
        )
    )
);
