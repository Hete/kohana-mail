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
        'MIME-Version' => '1.0'
    ),
    /**
     * Headers to apply on multipart/mixed document.
     */
    'attachement_headers' => array(
        'MIME-Version' => '1.0'
    ),
    /**
     * Sender configuration
     */
    'sender' => array(
        /**
         * PHP built-in mail() function.
         * 
         * Paremeters are imploded with a spaced and passed as $parameters
         * of the mail() function.
         */
        'Mail' => array(),
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
        /**
         * Plain produces a simple text document.
         */
        'Plain' => array(
            /**
             * FALSE to disable.
             * 72 characters recommended.
             */
            'wordwrap' => FALSE,
        ),
        /**
         * HTML produces a complex HTML document by inlining a CSS file.
         */
        'HTML' => array(
            'css_file' => MODPATH . 'mail/bootstrap-mail.min.css', // FALSE to disable
        ),
        /**
         * Auto produces a simple HTML document.
         */
        'Auto' => array(
            /**
             * Applies a Text::auto_p
             */
            'paragraph' => TRUE,
            /**
             * Applies a Text::auto_link
             */
            'link' => TRUE
        )
    )
);
