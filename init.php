<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Route::set('mail', '<controller>((/<folder>)/<view>)', array(
	'controller' => 'mail'
)
        
        )->defaults(array(
    
    'action' => 'index',
    
));	



?>
