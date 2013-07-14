<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * 
 * @package  Mail
 * @category Configurations
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
return array(
    // Additionnal headers
    'headers' => array(
        'Content-type' => 'text/html; charset=UTF-8', // You may change this for non-HTML mails
        'MIME-Version' => '1.0'
    ),
    'sender' => array(
        'sendmail' => array(),
        'imap' => array(),
        'pear' => array(
            'sendmail' => array(),
            'smtp' => array()
        )
    ),
    'styler' => array(
        'html' => array(
            'style_file' => NULL
        )
    )
);
?>