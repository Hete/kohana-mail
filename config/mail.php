<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    'from_name' => NULL, // Foo Bar
    'from_email' => NULL, // foo@bar.com
    // Additionnal headers
    'headers' => array(
        'Content-type' => 'text/html; charset=UTF-8', // You may change this for non-HTML mails
        'MIME-Version' => '1.0'
    ),
    'sender' => array(
        'sendmail' => array(),
        'imap' => array()
    ),
    'queue' => array(
        'file' => array(
            'path' => APPPATH . 'mails/',
        ),
        'database' => array(
            'table' => 'mails'
        )
    ),
);
?>