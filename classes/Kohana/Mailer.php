<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mailer.
 *
 * @package   Mail
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2014, Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license   BSD-3 clauses
 */
class Kohana_Mailer {

    public static $default = 'default';

    /**
     * Prepare a Mail_Sender.
     * 
     * @param  string $name
     * @return Mail_Sender
     */
    public static function factory($name = NULL) {

        if ($name === NULL) {
            $name = Mailer::$default;
        }

        $sender = Kohana::$config->load("mail.$name.sender");
        $options = Kohana::$config->load("mail.$name.options");

        return Mail_Sender::factory($sender, $options);
    }

}
