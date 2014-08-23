<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Configuration for the mail module.
 *
 * @package   Mail
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 * @license   BSD-3-Clauses
 */
return array(
    'default' => array(
        /**
         * Mail: simple mail() function wrapper.
         * 
         * You will need the PEAR Mail package installed on your computer if
         * you want to use any of the following mailer.
         * 
         * PEAR_Sendmail: send mail through a sendmail server using PEAR.
         * 
         * PEAR_SMTP: send mail through smtp protocol using PEAR.
         * 
         * PEAR_Mail: PEAR wrapper for the mail() function.
         * 
         * PHPMailer: send mail using PHPMailer.
         */
        'sender' => 'Mail',
        /**
         * Options for the sender.
         *
         * For PEAR senders options, refer to their official documentation at http://pear.php.net/manual/en/package.mail.mail.factory.php 
         */
        'options' => array(
        /**
         * Mail
         * 
         * Specify an array of commands. They will be automatically joined
         * by a space character.
         *
         * 'option_1', 'option_2',
         */
        /**
         * PEAR_Sendmail
         * 
         * 'sendmail_path' => '/usr/bin/sendmail',
         * 'sendmail_args' => '-i',
         */
        /**
         * PEAR_SMTP
         * 
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
        /**
         * PHPMailer
         * 
         * Keys are automatically capitalized so that you may write your
         * configuration using lowercase.
         *
         * @link https://github.com/PHPMailer/PHPMailer
         * 
         * 'host' => 'localhost',
         * 'username' => NULL,
         * 'password' => NULL
         */
        )
    )
);
