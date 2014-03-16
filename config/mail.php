<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Configuration for the mail module.
 *
 * @package   Mail
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 * @license   BSD 3-clauses
 */
return array(
    'default' => array(
        'sender' => 'Mail',
        /**
         * Options for the sender.
         */
        'sender_options' => array(
        /* PEAR_Sendmail
         * 'sendmail_path' => '/usr/bin/sendmail',
         * 'sendmail_args' => '-i',
         */
        /* PEAR_SMTP
         * 'host' => 'localhost',
         * 'port' => 25,
         * 'auth' => FALSE,
         * 'username' => NULL,
         * 'password' => NULL,
         * 'localhost' => 'localhost',
         * 'timeout' => NULL,
         * 'verp' => FALSE,
         * 'debug' => FALSE,
         * 'persist' => NULL,
         * 'pipelining' => NULL
         */
        ),
        'styler' => NULL,
        'styler_options' => array(
        /* Plain
         * 'wordwrap' => FALSE, // 72 characters recommended, FALSE to disable.
         */
        /* HTML
         * 'css_file' => MODPATH . 'mail/bootstrap-mail.css',
         */
        /* Auto
         * 'paragraph' => TRUE, // Applies a Text::auto_p
         * 'link' => TRUE // Applies a Text::auto_link
         */
        )
    )
);
