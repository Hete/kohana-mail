<?php

return array(
    'default' => array(
        'from' => array(
            'name' => NULL,
            'email' => NULL
        ),
        'subject' => NULL,
        'async' => array(
            // Asynchronous configuration
            'path' => APPPATH . 'mailqueue',
            'salt' => NULL,
        ),
    ),
);
?>