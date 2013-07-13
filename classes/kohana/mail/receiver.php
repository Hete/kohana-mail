<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Interface to define receivers.
 * 
 * Implement this interface on your models so they can be used as receivers
 * in Mail_Sender.
 * 
 * @package   Mail
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
interface Kohana_Mail_Receiver {

    /**
     * Returns the receiver name
     */
    public function receiver_name();

    /**
     * Returns the receiver email
     */
    public function receiver_email();

    /**
     * 
     * @param type $view
     */
    public function receiver_subscribed($view);
}

?>
