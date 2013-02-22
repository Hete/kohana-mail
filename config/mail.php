<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    'sender' => array(
        'native' => array(
            'from' => array(
                'name' => NULL,
                'email' => NULL
            ),
            'subject' => NULL,
        ),
        // PEAR implementation of Mail
        'pear' => array(
            'sendmail' => array(
                'from' => array(
                    'name' => NULL,
                    'email' => NULL
                ),
                'subject' => NULL,
            ),
            'smtp' => array(
                'from' => array(
                    'name' => NULL,
                    'email' => NULL
                ),
                'subject' => NULL,
            )
        )
    ),
    'queue' => array(
        'file' => array()
    )
);
?>