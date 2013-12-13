<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * Default headers
     */
    'headers' => array(
        'Content-Type' => 'text/plain',
        'Content-Encoding' => 'utf-8',
        'MIME-Version' => '1.0'
    ),
    'sender' => array(
        'Sendmail' => array(),
        'IMAP' => array(),
    ),
    'styler' => array(
        'HTML' => array(
            'css_file' => MODPATH . 'mail/bootstrap-mail.min.css' 
        )
    ),
);
