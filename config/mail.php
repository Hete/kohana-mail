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
        'file' => array(
            'path' => APPPATH . 'mails',
        ),
    ),
    'database' => array(
        'table' => 'mails'
    )
);
?>