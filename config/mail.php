<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    // Additionnal headers
    'headers' => array(
        'Content-type' => 'text/html; charset=UTF-8', // You may change this for non-HTML mails
        'MIME-Version' => '1.0'
    ),
    'sender' => array(
        'sendmail' => array(),
        'imap' => array()
    ),    
    'styler' => array(
        'html' => array(
            'style_file' => NULL
        )
    )
);
?>