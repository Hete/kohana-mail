<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mailer.
 *
 * @package   Mail
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2014, Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license   BSD-3-Clauses
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

    /**
     * A Message-ID generator following Matt Curtin and Jamie Zawinski
     * recommendations.
     * 
     * It is using base64 encoding instead of base36 for the random byte.
     * 
     * domain is defaulted to localhost.
     * 
     * @link http://www.jwz.org/doc/mid.html
     */
    public static function message_id() {

        $microtime = base_convert(microtime(), 10, 36);

        // Generate a new unique token
        if (function_exists('openssl_random_pseudo_bytes')) {

            $random = base64_encode(openssl_random_pseudo_bytes(8));
        } else {

            $random = base64_encode(substr(sha1(uniqid(NULL, TRUE)), 0, 8));
        }

        $domain = Arr::get($_SERVER, 'SERVER_NAME', 'localhost');

        return '<' . $microtime . '.' . $random . '@' . $domain . '>';
    }

}
