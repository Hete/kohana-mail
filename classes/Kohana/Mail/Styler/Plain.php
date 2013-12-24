<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Plain styler.
 *
 * @package   Mail
 * @category  Stylers
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Styler_Plain extends Mail_Styler {

    public function style($body) {

        if ($length = Kohana::$config->load('mail.styler.Plain.wordwrap')) {
            $body = wordwrap($body, $length, "\r\n");   
        }

        return (string) $body;
    }
}
