<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    'sender' => array(
        'from' => array(
            'name' => NULL,
            'email' => NULL
        ),
    ),
    'queue' => array(
        'Cache' => array(
        ),
        'File' => array(
            'path' => APPPATH . 'mails',
        ),
        'Database' => array(
            'table' => 'mails'
        )
    ),
);
?>